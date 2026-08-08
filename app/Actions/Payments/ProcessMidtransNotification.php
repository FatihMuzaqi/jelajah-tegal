<?php

namespace App\Actions\Payments;

use App\Actions\Checkout\ReleaseOrder;
use App\Models\DatabaseNotification;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Services\AuditLogger;
use App\Services\Payments\MidtransSignature;
use App\Services\Ledger\LedgerPoster;
use App\Support\Money;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProcessMidtransNotification
{
    public function __construct(private MidtransSignature $signature, private CapturePayment $capture, private ReleaseOrder $release, private LedgerPoster $ledger, private AuditLogger $audit) {}

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
        $payment = Payment::whereHas('order', fn ($query) => $query->where('order_number', $payload['order_id']))->with('order')->firstOrFail();
        $hash = hash('sha256', json_encode($this->canonical($payload), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $eventId = (string) ($payload['notification_id'] ?? $payload['event_id'] ?? $hash);

        try {
            $event = PaymentWebhookEvent::create([
                'provider' => 'midtrans', 'provider_event_id' => $eventId, 'payment_id' => $payment->id, 'order_id' => $payment->order_id,
                'event_type' => (string) $payload['transaction_status'], 'payload_hash' => $hash, 'gross_amount' => $payload['gross_amount'],
                'payload' => $payload, 'source' => $source, 'received_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            $existing = PaymentWebhookEvent::where('provider', 'midtrans')->where('provider_event_id', $eventId)->firstOrFail();
            if (! hash_equals($existing->payload_hash, $hash)) {
                throw ValidationException::withMessages(['provider_event_id' => 'Provider event ID telah digunakan oleh payload berbeda.']);
            }
            return $existing;
        }

        try {
            if (Money::toMinor((string) $payload['gross_amount']) !== Money::toMinor($payment->amount)) {
                throw ValidationException::withMessages(['gross_amount' => 'Gross amount Midtrans tidak sesuai payment.']);
            }
            $status = $this->canonicalStatus($payload);
            DB::transaction(function () use ($payment, $payload, $status) {
                $payment = Payment::with('order')->lockForUpdate()->findOrFail($payment->id);
                $reference = (string) ($payload['transaction_id'] ?? $payload['order_id']);
                if ($status === 'paid') {
                    $this->capture->execute($payment, $reference, (string) $payload['gross_amount'], 'IDR', 'midtrans', $payload);
                    return;
                }
                if ($status === 'authorized' && $payment->status->value === 'pending') {
                    $payment->update(['provider' => 'midtrans', 'provider_reference' => $reference, 'status' => 'authorized', 'authorized_at' => now(), 'provider_snapshot' => $payload]);
                    return;
                }
                if (in_array($status, ['failed', 'expired', 'cancelled'], true) && in_array($payment->status->value, ['pending', 'authorized'], true)) {
                    $this->release->execute($payment->order, $status === 'expired' ? 'expired' : 'cancelled');
                    $payment->fresh()->update([
                        'provider' => 'midtrans', 'provider_reference' => $reference, 'status' => $status,
                        'failed_at' => $status === 'failed' ? now() : null,
                        'expired_at' => $status === 'expired' ? now() : null,
                        'cancelled_at' => $status === 'cancelled' ? now() : null,
                        'failure_code' => $payload['status_message'] ?? null, 'provider_snapshot' => $payload,
                    ]);
                    return;
                }
                if ($status === 'refunded' && $payment->status->value === 'paid') {
                    if (($payload['transaction_status'] ?? null) === 'partial_refund' && Money::toMinor((string) ($payload['refund_amount'] ?? '0')) !== Money::toMinor($payment->amount)) {
                        throw ValidationException::withMessages(['refund_amount' => 'Partial refund belum didukung oleh lifecycle order canonical.']);
                    }
                    $payment->update(['status' => 'refunded', 'refunded_at' => now(), 'provider_snapshot' => $payload]);
                    $payment->order()->update(['status' => 'refunded', 'payment_status' => 'refunded']);
                    $payment->order->items()->with('tickets')->get()->each(fn ($item) => $item->tickets()->whereIn('status', ['unused', 'active'])->update(['status' => 'revoked', 'revoked_at' => now(), 'revocation_reason' => 'Payment refunded']));
                    $this->ledger->paymentRefunded($payment->order->fresh(), $payment->fresh());
                }
            });
            $event->update(['processed_at' => now()]);
            $this->audit->record('payment.midtrans_notification', $payment, [], ['provider_event_id' => $eventId, 'status' => $status]);
            DatabaseNotification::create(['user_id' => $payment->order->user_id, 'mitra_id' => $payment->mitra_id, 'type' => 'payment.status_changed', 'data' => ['order_id' => $payment->order_id, 'status' => $status]]);
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
