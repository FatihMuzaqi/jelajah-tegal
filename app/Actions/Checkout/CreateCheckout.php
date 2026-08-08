<?php

namespace App\Actions\Checkout;

use App\Models\Mitra;
use App\Models\Order;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\Checkout\Adapters\AccommodationCheckoutAdapter;
use App\Services\Checkout\Adapters\CulinaryCheckoutAdapter;
use App\Services\Checkout\Adapters\EventCheckoutAdapter;
use App\Services\Checkout\Adapters\RentalCheckoutAdapter;
use App\Services\Checkout\Adapters\TourismCheckoutAdapter;
use App\Services\Checkout\CommercialTerms;
use App\Services\Checkout\IdempotencyService;
use App\Services\Vouchers\VoucherEngine;
use App\Support\Money;
use Illuminate\Validation\ValidationException;

class CreateCheckout
{
    public function __construct(private IdempotencyService $idempotency, private VoucherEngine $vouchers, private CommercialTerms $terms, private AuditLogger $audit, private TourismCheckoutAdapter $tourism, private EventCheckoutAdapter $event, private CulinaryCheckoutAdapter $culinary, private RentalCheckoutAdapter $rental, private AccommodationCheckoutAdapter $accommodation) {}

    public function execute(User $user, array $payload): Order
    {
        $key = $payload['idempotency_key'];
        unset($payload['idempotency_key']);

        return $this->idempotency->execute($user, 'checkout', $key, $payload, function () use ($user, $payload) {
            $quote = $this->adapter($payload['domain'])->quoteAndReserve($user, $payload);
            $mitra = Mitra::findOrFail($quote->mitraId);
            $subtotal = $quote->subtotalMinor;
            $voucherResult = null;
            $discount = 0;
            if (filled($payload['voucher_code'] ?? null)) {
                $voucherResult = $this->vouchers->apply($user, $payload['voucher_code'], $quote->mitraId, $quote->serviceTypeId, $subtotal);
                $discount = $voucherResult->discountMinor;
            }$fee = $this->terms->adminFeeMinor();
            $bps = $this->terms->commissionBasisPoints($mitra);
            $commission = Money::basisPoints($subtotal, $bps);
            $sponsor = $voucherResult ? $voucherResult->snapshot['sponsor'] : null;
            if ($sponsor === 'mitra') {
                $commission = min($commission, $subtotal - $discount);
            }$mitraNet = $subtotal - $commission - ($sponsor === 'mitra' ? $discount : 0);
            $total = $subtotal - $discount + $fee;
            $complimentary = $subtotal === 0 && $fee === 0;
            $order = Order::create(['order_number' => 'ORD-'.now()->format('ymd').'-'.str()->upper(str()->random(10)), 'user_id' => $user->id, 'mitra_id' => $quote->mitraId, 'voucher_id' => $voucherResult?->voucher->id, 'subtotal' => Money::fromMinor($subtotal), 'admin_fee' => Money::fromMinor($fee), 'discount_amount' => Money::fromMinor($discount), 'total_amount' => Money::fromMinor($total), 'commission_basis_points' => $bps, 'commission_amount' => Money::fromMinor($commission), 'mitra_net_amount' => Money::fromMinor($mitraNet), 'status' => $complimentary ? 'paid' : 'pending_payment', 'payment_status' => $complimentary ? 'paid' : 'pending', 'user_snapshot' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone], 'mitra_snapshot' => ['id' => $mitra->id, 'name' => $mitra->display_name, 'slug' => $mitra->slug], 'voucher_snapshot' => $voucherResult?->snapshot, 'placed_at' => now(), 'expires_at' => $complimentary ? null : now()->addMinutes(15), 'paid_at' => $complimentary ? now() : null]);
            $item = $order->items()->create(['mitra_id' => $quote->mitraId, 'catalog_offer_id' => $quote->offerId, 'resource_type' => $quote->resourceType, 'reference_id' => $quote->referenceId, 'quantity' => $quote->quantity, 'item_name' => $quote->itemName, 'sku' => $quote->sku, 'unit_price' => Money::fromMinor($quote->unitPriceMinor), 'subtotal' => Money::fromMinor($subtotal), 'admin_fee' => Money::fromMinor($fee), 'discount_amount' => Money::fromMinor($discount), 'line_total' => Money::fromMinor($total), 'booking_date' => $quote->bookingDate, 'starts_at' => $quote->startsAt, 'ends_at' => $quote->endsAt, 'details' => $quote->details]);
            foreach ($quote->holds as $hold) {
                $item->holds()->create($hold + ['status' => $complimentary ? 'converted' : 'active']);
            }$order->payments()->create(['mitra_id' => $quote->mitraId, 'provider' => $complimentary ? 'none' : 'pending_gateway', 'amount' => Money::fromMinor($total), 'status' => $complimentary ? 'paid' : 'pending', 'paid_at' => $complimentary ? now() : null]);
            if ($voucherResult) {
                $voucherResult->voucher->increment('used_count');
                $voucherResult->voucher->usages()->create(['voucher_claim_id' => $voucherResult->claim->id, 'order_id' => $order->id, 'user_id' => $user->id, 'discount_amount' => Money::fromMinor($discount), 'status' => 'applied', 'applied_at' => now(), 'created_at' => now()]);
                if ($voucherResult->voucher->usage_limit !== null && $voucherResult->voucher->fresh()->used_count >= $voucherResult->voucher->usage_limit) {
                    $voucherResult->voucher->update(['status' => 'exhausted']);
                }
            }$this->audit->record('checkout.created', $order, [], ['domain' => $quote->domain, 'total_amount' => Money::fromMinor($total)], $user);

            return $order->fresh(['items.holds', 'payments']);
        });
    }

    private function adapter(string $domain)
    {
        return match ($domain) {
            'tourism' => $this->tourism,'event' => $this->event,'culinary' => $this->culinary,'rental' => $this->rental,'accommodation' => $this->accommodation,default => throw ValidationException::withMessages(['domain' => 'Domain checkout tidak didukung.'])
        };
    }
}
