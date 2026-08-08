<?php

namespace App\Services\Checkout\Adapters;

use App\Models\RentalBooking;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\Checkout\CheckoutEligibility;
use App\Services\Checkout\CheckoutQuote;
use App\Services\Checkout\Contracts\CheckoutAdapter;
use App\Support\Money;
use Illuminate\Validation\ValidationException;

class RentalCheckoutAdapter implements CheckoutAdapter
{
    public function __construct(private CheckoutEligibility $eligibility) {}

    public function quoteAndReserve(User $user, array $p): CheckoutQuote
    {
        $booking = RentalBooking::with(['vehicle.catalogEntity.mitra.features.serviceType', 'vehicle.catalogEntity.serviceType', 'rate.offer'])->lockForUpdate()->findOrFail($p['reference_id']);
        if ($booking->user_id !== $user->id || $booking->status->value !== 'approved') {
            throw ValidationException::withMessages(['reference_id' => 'Booking rental belum disetujui atau bukan milik pengguna.']);
        }
        if (OrderItem::where('resource_type', 'rental_booking')->where('reference_id', $booking->id)->exists()) {
            throw ValidationException::withMessages(['reference_id' => 'Booking rental sudah memiliki order.']);
        }
        $entity = $booking->vehicle->catalogEntity;
        $this->eligibility->assert($entity, 'rental');
        $total = Money::toMinor($booking->total_amount);

        return new CheckoutQuote('rental', $entity->mitra_id, (string) $entity->service_type_id, $booking->rate->catalog_offer_id, 'rental_booking', $booking->id, 1, 'Rental '.$entity->name, $booking->rate->offer->sku, $total, $total, $booking->pickup_at->toDateString(), $booking->pickup_at->toDateTimeString(), $booking->return_at->toDateTimeString(), ['drive_mode' => $booking->drive_mode, 'unit_price' => $booking->unit_price, 'deposit_amount' => $booking->deposit_amount, 'pickup_location' => $booking->pickup_location, 'return_location' => $booking->return_location], [['resource_type' => 'rental_booking', 'resource_id' => $booking->id, 'service_date' => $booking->pickup_at->toDateString(), 'quantity' => 1]]);
    }
}
