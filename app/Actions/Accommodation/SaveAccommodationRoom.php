<?php

namespace App\Actions\Accommodation;

use App\Models\Accommodation;
use App\Models\AccommodationRoom;
use App\Models\CatalogOffer;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveAccommodationRoom
{
    public function __construct(private AuditLogger $audit) {}

    public function execute(Accommodation $accommodation, array $data, $actor, ?AccommodationRoom $room = null): AccommodationRoom
    {
        if ($room) {
            abort_unless($room->accommodation_id === $accommodation->id, 404);
            $highestCapacity = (int) $room->offer->availabilities()->max('capacity');
            if ((int) $data['total_units'] < $highestCapacity) {
                throw ValidationException::withMessages(['total_units' => 'Total unit tidak boleh lebih kecil dari kapasitas kalender yang sudah tersimpan.']);
            }
        }

        return DB::transaction(function () use ($accommodation, $data, $actor, $room) {
            $entity = $accommodation->catalogEntity;
            $before = $room?->toArray() ?? [];
            $offer = $room?->offer ?? new CatalogOffer(['mitra_id' => $entity->mitra_id, 'catalog_entity_id' => $entity->id, 'offer_type' => 'accommodation_room']);
            $offer->fill(['sku' => $data['sku'] ?? $offer->sku, 'name' => $data['name'], 'currency' => 'IDR', 'price' => $data['nightly_price'], 'status' => $data['status'] === 'active' ? 'active' : 'draft']);
            $offer->save();
            $room ??= new AccommodationRoom(['accommodation_id' => $accommodation->id, 'catalog_offer_id' => $offer->id]);
            $room->fill(Arr::only($data, ['name', 'description', 'room_type', 'kind', 'capacity_adults', 'capacity_children', 'total_units', 'plot_count', 'min_stay_nights', 'max_stay_nights', 'advance_booking_days', 'availability_notes', 'status', 'bed_config']));
            $room->save();
            $room->facilities()->sync($data['facilities'] ?? []);
            $this->audit->record($before ? 'accommodation.room_updated' : 'accommodation.room_created', $entity, $before, $room->fresh()->toArray(), $actor);

            return $room->fresh(['offer', 'facilities']);
        });
    }
}
