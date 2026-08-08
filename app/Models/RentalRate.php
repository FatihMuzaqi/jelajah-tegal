<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalRate extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['rental_vehicle_id', 'catalog_offer_id', 'drive_mode', 'duration_unit', 'duration_value'];

    protected function casts(): array
    {
        return ['duration_value' => 'integer'];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(RentalVehicle::class, 'rental_vehicle_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(CatalogOffer::class, 'catalog_offer_id');
    }
}
