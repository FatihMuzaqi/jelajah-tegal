<?php

namespace App\Services\Checkout\Adapters;

use App\Models\EventTicketType;
use App\Models\User;
use App\Services\Checkout\CheckoutEligibility;
use App\Services\Checkout\CheckoutQuote;
use App\Services\Checkout\Contracts\CheckoutAdapter;
use App\Support\Money;
use Illuminate\Validation\ValidationException;

class EventCheckoutAdapter implements CheckoutAdapter
{
    public function __construct(private CheckoutEligibility $eligibility) {}

    public function quoteAndReserve(User $user, array $p): CheckoutQuote
    {
        $type = EventTicketType::with(['event.catalogEntity.mitra.features.serviceType', 'event.catalogEntity.serviceType', 'offer'])->lockForUpdate()->findOrFail($p['reference_id']);
        $entity = $type->event->catalogEntity;
        $this->eligibility->assert($entity, 'event');
        $quantity = (int) $p['quantity'];
        if ($type->event->registration_deadline?->isPast() || $type->sale_starts_at?->isFuture() || $type->sale_ends_at?->isPast() || $type->issued_quantity + $type->reserved_quantity + $quantity > $type->quota) {
            throw ValidationException::withMessages(['quantity' => 'Kuota atau periode penjualan tiket tidak tersedia.']);
        }$type->increment('reserved_quantity', $quantity);
        $unit = Money::toMinor($type->offer->price);

        return new CheckoutQuote('event', $entity->mitra_id, (string) $entity->service_type_id, $type->catalog_offer_id, 'event_ticket_type', $type->id, $quantity, $type->name, $type->offer->sku, $unit, $unit * $quantity, $type->event->starts_at->toDateString(), $type->event->starts_at->toDateTimeString(), $type->event->ends_at->toDateTimeString(), ['catalog_entity_id' => $entity->id, 'event_id' => $type->event_id], [['resource_type' => 'event_ticket_type', 'resource_id' => $type->id, 'service_date' => $type->event->starts_at->toDateString(), 'quantity' => $quantity]]);
    }
}
