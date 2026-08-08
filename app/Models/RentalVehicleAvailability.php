<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalVehicleAvailability extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['rental_vehicle_id', 'service_date', 'status', 'price_override', 'notes'];

    protected function casts(): array
    {
        return ['service_date' => 'date', 'price_override' => 'decimal:2'];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(RentalVehicle::class, 'rental_vehicle_id');
    }
}
