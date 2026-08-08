<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulinaryVenue extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['catalog_entity_id', 'venue_type', 'accepts_reservations', 'phone', 'reservation_notes'];

    protected function casts(): array
    {
        return ['accepts_reservations' => 'boolean'];
    }

    public function catalogEntity(): BelongsTo
    {
        return $this->belongsTo(CatalogEntity::class);
    }

    public function menuCategories(): HasMany
    {
        return $this->hasMany(CulinaryMenuCategory::class)->orderBy('sort_order');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(CulinaryMenuItem::class);
    }

    public function tableSlots(): HasMany
    {
        return $this->hasMany(CulinaryTableSlot::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(CulinaryReservation::class);
    }
}
