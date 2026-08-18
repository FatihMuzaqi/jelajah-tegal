<?php

namespace App\Actions\Payments;

use App\Models\Invoice;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\MidtransSignature;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessMidtransInvoiceNotification
{
    public function __construct(private MidtransSignature $signature, private ProcessMidtransNotification $orderProcessor) {}

    public function execute(array $payload, string $source = 'webhook', bool $verifySignature = true): PaymentWebhookEvent
    {
        if ($verifySignature && ! $this->signature->valid($payload)) {
            throw ValidationException::withMessages(['signature_key' => 'Signature Midtrans tidak valid.']);
        }
        foreach (['order_id', 'transaction_status', 'gross_amount'] as $field) {
            if (blank($payload[$field] ?? null)) {
                throw ValidationException::withMessages([$field => 'Payload Midtrans tidak lengkap.']);
            }
        }

        $invoice = Invoice::with('orders.payments')->where('invoice_number', $payload['order_id'])->firstOrFail();
        $hash = hash('sha256', json_encode($this->canonical($payload), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $eventId = (string) ($payload['notification_id'] ?? $payload['event_id'] ?? $hash);

        try {
            $event = PaymentWebhookEvent::create([
                'provider' => 'midtrans', 'provider_event_id' => $eventId, 'payment_id' => null, 'order_id' => null,
                'event_type' => 'invoice_' . (string) $payload['transaction_status'], 'payload_hash' => $hash, 'gross_amount' => $payload['gross_amount'],
                'payload' => $payload, 'source' => $source, 'received_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            $existing = PaymentWebhookEvent::where('provider', 'midtrans')->where('provider_event_id', $eventId)->firstOrFail();
            if (! hash_equals($existing->payload_hash, $hash)) {
                throw ValidationException::withMessages(['provider_event_id' => 'Provider event ID telah digunakan oleh payload berbeda.']);
            }
            if ($existing->processed_at !== null && $invoice->status === 'paid') {
                return $existing;
            }
            $event = $existing;
        }

        try {
            $status = $this->canonicalStatus($payload);
            
            DB::transaction(function () use ($invoice, $payload, $status, $verifySignature) {
                if ($status === 'paid') {
                    $invoice->update(['status' => 'paid', 'paid_at' => now()]);
                } elseif (in_array($status, ['failed', 'expired', 'cancelled'])) {
                    $invoice->update([
                        'status' => $status,
                        'failed_at' => $status === 'failed' ? now() : null,
                        'expired_at' => $status === 'expired' ? now() : null,
                        'cancelled_at' => $status === 'cancelled' ? now() : null,
                    ]);
                }

                // Cascade the webhook payload to every child order
                foreach ($invoice->orders as $order) {
                    $childPayload = $payload;
                    $childPayload['order_id'] = $order->order_number;
                    $childPayload['transaction_id'] = ($payload['transaction_id'] ?? $payload['order_id']) . ':' . $order->order_number;
                    $childPayload['gross_amount'] = (string) $order->total_amount;
                    
                    // We skip signature validation for the child payload because we modified it
                    $this->orderProcessor->execute($childPayload, 'invoice_cascade', false);
                }
            });

            $event->update(['processed_at' => now()]);
        } catch (Throwable $e) {
            $event->update(['processing_error' => mb_substr($e->getMessage(), 0, 2000)]);
            throw $e;
        }

        return $event->fresh();
    }

    private function canonicalStatus(array $payload): string
    {
        $status = strtolower((string) $payload['transaction_status']);
        if ($status === 'capture') {
            return strtolower((string) ($payload['fraud_status'] ?? 'accept')) === 'accept' ? 'paid' : 'authorized';
        }
        return match ($status) {
            'settlement' => 'paid', 'authorize' => 'authorized', 'pending' => 'pending',
            'deny', 'failure' => 'failed', 'expire' => 'expired', 'cancel' => 'cancelled',
            'refund', 'partial_refund' => 'refunded',
            default => throw ValidationException::withMessages(['transaction_status' => 'Status Midtrans tidak dikenali.']),
        };
    }

    private function canonical(array $payload): array
    {
        ksort($payload);
        return $payload;
    }
}
