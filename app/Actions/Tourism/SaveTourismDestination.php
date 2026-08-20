<?php

namespace App\Actions\Tourism;

use App\Enums\CatalogStatus;
use App\Models\CatalogEntity;
use App\Models\Mitra;
use App\Models\ServiceType;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveTourismDestination
{
    public function __construct(private AuditLogger $audit) {}

    public function execute(Mitra $mitra, array $data, $actor, ?CatalogEntity $entity = null): CatalogEntity
    {
        $service = ServiceType::where('code', 'tourism')->firstOrFail();
        abort_unless($mitra->features()->where('service_type_id', $service->id)->where('status', 'enabled')->exists(), 403);
        if ($entity) {
            abort_unless($entity->mitra_id === $mitra->id, 403);
            if (! in_array($entity->status, [CatalogStatus::Draft->value, CatalogStatus::Rejected->value, CatalogStatus::Published->value, CatalogStatus::Submitted->value, CatalogStatus::UnderReview->value], true)) {
                throw ValidationException::withMessages(['status' => 'Destinasi dengan status saat ini tidak dapat diubah.']);
            }
        }

        return DB::transaction(function () use ($mitra, $service, $data, $actor, $entity) {
            $before = $entity?->toArray() ?? [];
            $entity ??= new CatalogEntity(['mitra_id' => $mitra->id, 'service_type_id' => $service->id]);
            $entity->fill(Arr::only($data, ['category_id', 'region_id', 'name', 'slug', 'description', 'address', 'is_featured']));
            $entity->status = $entity->status ?? CatalogStatus::Draft->value;
            $entity->save();
            $entity->tourism()->updateOrCreate([], Arr::only($data, ['destination_type', 'visit_duration_minutes', 'badge', 'is_hidden_gem']));
            if (isset($data['latitude'], $data['longitude'])) {
                $lat = (float) $data['latitude'];
                $lng = (float) $data['longitude'];
                DB::table('catalog_locations')->updateOrInsert(
                    ['catalog_entity_id' => $entity->id],
                    [
                        'location' => DB::raw("ST_GeomFromText('POINT({$lat} {$lng})', 4326)"),
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
            if (array_key_exists('facilities', $data)) {
                $entity->facilities()->sync($data['facilities'] ?? []);
            }
            $this->audit->record($before ? 'tourism.updated' : 'tourism.created', $entity, $before, $entity->fresh()->toArray(), $actor);

            return $entity->fresh(['tourism', 'location']);
        });
    }
}
