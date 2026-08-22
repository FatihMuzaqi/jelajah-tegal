<?php

namespace App\Livewire\Public;

use App\Models\CatalogEntity;
use Livewire\Component;

class NearbyServices extends Component
{
    public float $lat;
    public float $lng;
    public string $excludeId;
    
    public string $filterType = 'all';

    public function mount(float $lat, float $lng, string $excludeId)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->excludeId = $excludeId;
    }

    public function setFilter(string $type)
    {
        $this->filterType = $type;
    }

    public function getNearbyServicesProperty()
    {
        $query = CatalogEntity::query()
            ->select('catalog_entities.*')
            ->leftJoin('catalog_locations', 'catalog_entities.id', '=', 'catalog_locations.catalog_entity_id')
            ->selectRaw('( 6371 * acos( cos( radians(?) ) * cos( radians( catalog_locations.latitude ) ) * cos( radians( catalog_locations.longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( catalog_locations.latitude ) ) ) ) AS distance', [$this->lat, $this->lng, $this->lat])
            ->where('catalog_entities.id', '!=', $this->excludeId)
            ->where('catalog_entities.status', 'published')
            ->whereHas('mitra', fn ($q) => $q->publiclyVisible());

        if ($this->filterType !== 'all') {
            $query->whereHas('serviceType', fn ($type) => $type->where('code', $this->filterType));
        }

        return $query->orderByRaw('distance IS NULL, distance ASC')
            ->limit(10)
            ->with(['serviceType', 'media' => fn($q) => $q->where('catalog_media.role', 'cover')])
            ->get()->map(function($service) {
                // Mock distance for dev if missing
                if (is_null($service->distance)) {
                    $service->distance = rand(10, 149) / 10;
                }
                return $service;
            });
    }

    public function render()
    {
        return view('livewire.public.nearby-services', [
            'services' => $this->nearbyServices,
        ]);
    }
}
