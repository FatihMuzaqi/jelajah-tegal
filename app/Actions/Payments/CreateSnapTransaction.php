<?php

namespace App\Actions\Payments;

use App\Models\Payment;
use App\Services\AuditLogger;
use App\Services\Payments\MidtransClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSnapTransaction
{
    public function __construct(private MidtransClient $client, private AuditLogger $audit) {}

    public function execute(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment) {
            $payment = Payment::with('order')->lockForUpdate()->findOrFail($payment->id);
            if ($payment->status->value !== 'pending' || $payment->order->status->value !== 'pending_payment' || $payment->order->expires_at?->isPast()) {
                throw ValidationException::withMessages(['payment' => 'Payment tidak lagi dapat dibuatkan transaksi Snap.']);
            }
            if ($payment->snap_token && $payment->snap_redirect_url) {
                return $payment;
            }
            $result = $this->client->createSnap($payment->order);
            $before = $payment->only(['provider', 'provider_reference']);
            $payment->update(['provider' => 'midtrans', 'snap_token' => $result['token'], 'snap_redirect_url' => $result['redirect_url'], 'provider_snapshot' => ['snap_created_at' => now()->toIso8601String()]]);
            $this->audit->record('payment.snap_created', $payment, $before, ['provider' => 'midtrans'], $payment->order->user);
            return $payment->fresh();
        });
    }
}
