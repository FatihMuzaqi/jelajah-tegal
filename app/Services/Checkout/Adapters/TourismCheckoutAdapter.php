<?php

namespace App\Services\Checkout\Adapters;

use App\Models\Availability;
use App\Models\TourismTicketPackage;
use App\Models\User;
use App\Services\Checkout\CheckoutEligibility;
use App\Services\Checkout\CheckoutQuote;
use App\Services\Checkout\Contracts\CheckoutAdapter;
use App\Support\Money;
use Illuminate\Validation\ValidationException;

class TourismCheckoutAdapter implements CheckoutAdapter
{
    public function __construct(private CheckoutEligibility $eligibility) {}

    public function quoteAndReserve(User $user, array $p): CheckoutQuote
    {
        $package = TourismTicketPackage::with(['offer.catalogEntity.mitra.features.serviceType', 'offer.catalogEntity.serviceType'])
            ->where('id', $p['reference_id'])
            ->orWhere('catalog_offer_id', $p['reference_id'])
            ->firstOrFail();

        $entity = $package->offer->catalogEntity;
        $this->eligibility->assert($entity, 'tourism');

        $quantity = (int) $p['quantity'];
        $date = $p['service_date'];

        $availability = Availability::where('catalog_offer_id', $package->catalog_offer_id)
            ->whereDate('service_date', $date)
            ->lockForUpdate()
            ->first();

        if (! $availability) {
            $availability = Availability::create([
                'mitra_id' => $entity->mitra_id,
                'catalog_offer_id' => $package->catalog_offer_id,
                'service_date' => $date,
                'capacity' => $package->quota_per_day ?? 100,
                'reserved_quantity' => 0,
                'status' => 'available',
            ]);
        }

        if ($availability->status !== 'available' || $availability->reserved_quantity + $quantity > $availability->capacity) {
            throw ValidationException::withMessages(['quantity' => 'Kuota wisata tidak tersedia pada tanggal tersebut.']);
        }

        $availability->increment('reserved_quantity', $quantity);
        $unit = Money::toMinor($availability->price_override ?? $package->offer->price);

        return new CheckoutQuote(
            'tourism',
            $entity->mitra_id,
            (string) $entity->service_type_id,
            $package->catalog_offer_id,
            'tourism_ticket_package',
            $package->id,
            $quantity,
            $package->name,
            $package->offer->sku,
            $unit,
            $unit * $quantity,
            $date,
            null,
            null,
            ['catalog_entity_id' => $entity->id, 'slug' => $entity->slug],
            [['resource_type' => 'availability', 'resource_id' => $availability->id, 'service_date' => $date, 'quantity' => $quantity]]
        );
    }
}
