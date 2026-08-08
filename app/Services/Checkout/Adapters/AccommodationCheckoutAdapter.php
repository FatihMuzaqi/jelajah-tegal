<?php

namespace App\Services\Checkout\Adapters;

use App\Models\AccommodationRoom;
use App\Models\Availability;
use App\Models\User;
use App\Services\Checkout\CheckoutEligibility;
use App\Services\Checkout\CheckoutQuote;
use App\Services\Checkout\Contracts\CheckoutAdapter;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AccommodationCheckoutAdapter implements CheckoutAdapter
{
    public function __construct(private CheckoutEligibility $eligibility) {}

    public function quoteAndReserve(User $user, array $p): CheckoutQuote
    {
        $room = AccommodationRoom::with(['accommodation.catalogEntity.mitra.features.serviceType', 'accommodation.catalogEntity.serviceType', 'offer'])->findOrFail($p['reference_id']);
        $entity = $room->accommodation->catalogEntity;
        $this->eligibility->assert($entity, 'accommodation');
        if ($room->status !== 'active' || $room->offer->status !== 'active') {
            throw ValidationException::withMessages(['reference_id' => 'Kamar tidak aktif.']);
        }$start = Carbon::parse($p['start_date'])->startOfDay();
        $end = Carbon::parse($p['end_date'])->startOfDay();
        $nights = $start->diffInDays($end);
        if ($nights < 1 || ($room->min_stay_nights && $nights < $room->min_stay_nights) || ($room->max_stay_nights && $nights > $room->max_stay_nights)) {
            throw ValidationException::withMessages(['end_date' => 'Durasi menginap tidak valid.']);
        }$quantity = (int) $p['quantity'];
        $subtotal = 0;
        $daily = [];
        $holds = [];
        for ($date = $start->copy(); $date->lt($end); $date->addDay()) {
            $availability = Availability::where('catalog_offer_id', $room->catalog_offer_id)->whereDate('service_date', $date)->lockForUpdate()->first();
            if (! $availability || $availability->status !== 'available' || $availability->reserved_quantity + $quantity > $availability->capacity) {
                throw ValidationException::withMessages(['quantity' => 'Kamar tidak tersedia pada '.$date->toDateString().'.']);
            }$availability->increment('reserved_quantity', $quantity);
            $price = Money::toMinor($availability->price_override ?? $room->offer->price);
            $subtotal += $price * $quantity;
            $daily[$date->toDateString()] = Money::fromMinor($price);
            $holds[] = ['resource_type' => 'availability', 'resource_id' => $availability->id, 'service_date' => $date->toDateString(), 'quantity' => $quantity];
        }

return new CheckoutQuote('accommodation', $entity->mitra_id, (string) $entity->service_type_id, $room->catalog_offer_id, 'accommodation_room', $room->id, $quantity, $entity->name.' - '.$room->name, $room->offer->sku, Money::toMinor($room->offer->price), $subtotal, $start->toDateString(), $start->toDateTimeString(), $end->toDateTimeString(), ['nights' => $nights, 'daily_prices' => $daily, 'adults' => $p['adults'] ?? null, 'children' => $p['children'] ?? null], $holds);
    }
}
