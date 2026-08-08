<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccommodationRoom extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['accommodation_id', 'catalog_offer_id', 'name', 'description', 'room_type', 'kind', 'capacity_adults', 'capacity_children', 'total_units', 'plot_count', 'min_stay_nights', 'max_stay_nights', 'advance_booking_days', 'availability_notes', 'status', 'bed_config'];

    protected function casts(): array
    {
        return ['capacity_adults' => 'integer', 'capacity_children' => 'integer', 'total_units' => 'integer', 'plot_count' => 'integer', 'min_stay_nights' => 'integer', 'max_stay_nights' => 'integer', 'advance_booking_days' => 'integer', 'bed_config' => 'array'];
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(CatalogOffer::class, 'catalog_offer_id');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(MediaAsset::class, 'accommodation_room_media')->withPivot(['role', 'sort_order', 'caption']);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'accommodation_room_facilities')->withPivot('notes');
    }
}
