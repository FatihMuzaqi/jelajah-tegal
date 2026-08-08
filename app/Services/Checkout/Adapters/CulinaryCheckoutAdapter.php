<?php

namespace App\Services\Checkout\Adapters;

use App\Models\CatalogOffer;
use App\Models\CulinaryReservation;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\Checkout\CheckoutEligibility;
use App\Services\Checkout\CheckoutQuote;
use App\Services\Checkout\Contracts\CheckoutAdapter;
use Illuminate\Validation\ValidationException;

class CulinaryCheckoutAdapter implements CheckoutAdapter
{
    public function __construct(private CheckoutEligibility $eligibility) {}

    public function quoteAndReserve(User $user, array $p): CheckoutQuote
    {
        $reservation = CulinaryReservation::with(['venue.catalogEntity.mitra.features.serviceType', 'venue.catalogEntity.serviceType', 'slot'])->lockForUpdate()->findOrFail($p['reference_id']);
        if ($reservation->user_id !== $user->id || ! in_array($reservation->status->value, ['requested', 'confirmed'], true)) {
            throw ValidationException::withMessages(['reference_id' => 'Reservasi tidak dapat di-checkout.']);
        }
        if (OrderItem::where('resource_type', 'culinary_reservation')->where('reference_id', $reservation->id)->exists()) {
            throw ValidationException::withMessages(['reference_id' => 'Reservasi sudah memiliki order.']);
        }
        $entity = $reservation->venue->catalogEntity;
        $this->eligibility->assert($entity, 'culinary');
        $offer = CatalogOffer::withTrashed()->firstOrCreate(['mitra_id' => $entity->mitra_id, 'sku' => 'CUL-'.$reservation->venue->id], ['catalog_entity_id' => $entity->id, 'offer_type' => 'culinary_reservation', 'name' => 'Reservasi '.$entity->name, 'price' => '0.00', 'status' => 'active']);
        if ($offer->trashed()) {
            throw ValidationException::withMessages(['reference_id' => 'Offer reservasi tidak aktif.']);
        }

return new CheckoutQuote('culinary', $entity->mitra_id, (string) $entity->service_type_id, $offer->id, 'culinary_reservation', $reservation->id, 1, 'Reservasi '.$entity->name, $offer->sku, 0, 0, $reservation->slot->service_date->toDateString(), $reservation->slot->service_date->toDateString().' '.$reservation->slot->starts_at, $reservation->slot->service_date->toDateString().' '.$reservation->slot->ends_at, ['party_size' => $reservation->party_size, 'venue_id' => $reservation->culinary_venue_id], [['resource_type' => 'culinary_reservation', 'resource_id' => $reservation->id, 'service_date' => $reservation->slot->service_date->toDateString(), 'quantity' => 1]]);
    }
}
