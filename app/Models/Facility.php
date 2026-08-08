<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['service_type_id', 'name', 'slug', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function catalogEntities(): BelongsToMany
    {
        return $this->belongsToMany(CatalogEntity::class, 'catalog_facilities')->withPivot('notes');
    }

    public function accommodationRooms(): BelongsToMany
    {
        return $this->belongsToMany(AccommodationRoom::class, 'accommodation_room_facilities')->withPivot('notes');
    }
}
