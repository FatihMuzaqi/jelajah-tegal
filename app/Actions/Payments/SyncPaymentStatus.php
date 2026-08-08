<?php

namespace App\Actions\Payments;

use App\Models\Payment;
use App\Models\PaymentReconciliation;
use App\Models\User;
use App\Services\Payments\MidtransClient;
use Throwable;

class SyncPaymentStatus
{
    public function __construct(private MidtransClient $client, private ProcessMidtransNotification $processor) {}

    public function execute(Payment $payment, ?User $actor = null, string $source = 'manual'): PaymentReconciliation
    {
        $payment->loadMissing('order');
        $local = $payment->status->value;
        try {
            $payload = $this->client->status($payment->order->order_number);
            $this->processor->execute($payload, $source, false);
            $payment->fresh()->update(['last_synced_at' => now()]);
            return PaymentReconciliation::create(['payment_id' => $payment->id, 'initiated_by' => $actor?->id, 'source' => $source, 'local_status' => $local, 'provider_status' => $payload['transaction_status'] ?? null, 'matched' => true, 'provider_payload' => $payload, 'checked_at' => now()]);
        } catch (Throwable $e) {
            PaymentReconciliation::create(['payment_id' => $payment->id, 'initiated_by' => $actor?->id, 'source' => $source, 'local_status' => $local, 'matched' => false, 'error' => mb_substr($e->getMessage(), 0, 2000), 'checked_at' => now()]);
            throw $e;
        }
    }
}
