<?php

namespace App\Actions\Payments;

use App\Models\EventTicketType;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Services\AuditLogger;
use App\Services\Ledger\LedgerPoster;
use App\Support\Money;
use App\Support\TicketToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CapturePayment
{
    public function __construct(private LedgerPoster $ledger, private AuditLogger $audit) {}

    public function execute(Payment $payment, string $providerReference, string $amount, string $currency = 'IDR', string $provider = 'manual_verified', ?array $providerSnapshot = null): Payment
    {
        return DB::transaction(function () use ($payment, $providerReference, $amount, $currency, $provider, $providerSnapshot) {
            $payment = Payment::lockForUpdate()->findOrFail($payment->id);
            $order = Order::with('items.holds')->lockForUpdate()->findOrFail($payment->order_id);

            if ($payment->status->value === 'paid') {
                if ($payment->provider_reference !== $providerReference
                    || Money::toMinor($amount) !== Money::toMinor($payment->amount)
                    || $currency !== $payment->currency) {
                    throw ValidationException::withMessages(['payment' => 'Replay payment berbeda dari capture sebelumnya.']);
                }
                return $payment;
            }
            if (Money::toMinor($amount) !== Money::toMinor($order->total_amount) || $currency !== $order->currency) {
                throw ValidationException::withMessages(['amount' => 'Nominal atau mata uang payment tidak sesuai order.']);
            }
            if ($order->expires_at?->isPast()) {
                throw ValidationException::withMessages(['order' => 'Order telah kedaluwarsa.']);
            }

            $payment->update(['provider' => $provider, 'provider_reference' => $providerReference, 'amount' => $amount, 'currency' => $currency, 'status' => 'paid', 'paid_at' => now(), 'provider_snapshot' => $providerSnapshot ?? $payment->provider_snapshot]);
            $order->update(['status' => 'paid', 'payment_status' => 'paid', 'paid_at' => now(), 'expires_at' => null]);

            foreach ($order->items as $item) {
                foreach ($item->holds as $hold) {
                    if ($hold->resource_type === 'event_ticket_type') {
                        $type = EventTicketType::lockForUpdate()->findOrFail($hold->resource_id);
                        if ($type->reserved_quantity < $hold->quantity) {
                            throw new RuntimeException('Reserved Event quota tidak konsisten.');
                        }
                        $type->decrement('reserved_quantity', $hold->quantity);
                        $type->increment('issued_quantity', $hold->quantity);
                    }
                    $hold->update(['status' => 'converted']);
                }

                if (in_array($item->resource_type, ['tourism_ticket_package', 'event_ticket_type'], true)) {
                    for ($i = 0; $i < $item->quantity; $i++) {
                        $id = (string) Str::ulid();
                        $token = TicketToken::for($id);
                        Ticket::create([
                            'id' => $id,
                            'ticket_code' => 'TKT-'.str()->upper(str()->random(12)),
                            'order_item_id' => $item->id,
                            'mitra_id' => $order->mitra_id,
                            'holder_user_id' => $order->user_id,
                            'qr_token_hash' => TicketToken::hash($token),
                            'token_version' => 1,
                            'status' => 'unused',
                            'valid_from' => $item->starts_at ?? $item->booking_date?->startOfDay(),
                            'valid_until' => $item->ends_at ?? $item->booking_date?->endOfDay(),
                        ]);
                    }
                }
                $item->update(['fulfillment_status' => 'confirmed']);
            }

            $this->ledger->paymentCaptured($order->fresh(), $payment->fresh());
            $this->audit->record('payment.captured', $payment, [], ['order_id' => $order->id, 'amount' => $amount]);

            return $payment->fresh();
        });
    }
}
