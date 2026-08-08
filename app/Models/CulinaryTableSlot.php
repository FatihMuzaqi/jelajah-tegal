<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulinaryTableSlot extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['culinary_venue_id', 'service_date', 'starts_at', 'ends_at', 'capacity_tables', 'capacity_guests', 'status'];

    protected function casts(): array
    {
        return ['service_date' => 'date', 'capacity_tables' => 'integer', 'capacity_guests' => 'integer'];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(CulinaryVenue::class, 'culinary_venue_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(CulinaryReservation::class);
    }
}
