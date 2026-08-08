<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalVehicle extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['catalog_entity_id', 'vehicle_type', 'brand', 'model', 'year', 'plate_number', 'transmission', 'seats', 'self_drive_available', 'driver_available', 'deposit_amount', 'insurance_policy', 'fuel_policy', 'pickup_instructions', 'status'];

    protected $hidden = ['plate_number_encrypted'];

    protected function casts(): array
    {
        return ['year' => 'integer', 'seats' => 'integer', 'self_drive_available' => 'boolean', 'driver_available' => 'boolean', 'deposit_amount' => 'decimal:2'];
    }

    protected function plateNumber(): Attribute
    {
        return Attribute::make(get: fn () => $this->plate_number_encrypted ? decrypt($this->plate_number_encrypted) : null, set: fn ($value) => ['plate_number_encrypted' => $value ? encrypt($value) : null]);
    }

    public function catalogEntity(): BelongsTo
    {
        return $this->belongsTo(CatalogEntity::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(RentalRate::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(RentalVehicleAvailability::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(RentalBooking::class);
    }
}
