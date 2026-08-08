<?php

namespace App\Models;

use App\Enums\RentalBookingStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RentalBooking extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['booking_number', 'rental_vehicle_id', 'rental_rate_id', 'mitra_id', 'user_id', 'pickup_at', 'return_at', 'pickup_location', 'return_location', 'drive_mode', 'unit_price', 'deposit_amount', 'total_amount', 'status', 'decided_by', 'decided_at', 'decision_reason'];

    protected function casts(): array
    {
        return ['pickup_at' => 'datetime', 'return_at' => 'datetime', 'unit_price' => 'decimal:2', 'deposit_amount' => 'decimal:2', 'total_amount' => 'decimal:2', 'status' => RentalBookingStatus::class, 'decided_at' => 'datetime'];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(RentalVehicle::class, 'rental_vehicle_id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(RentalRate::class, 'rental_rate_id');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(RenterDocument::class, 'rental_booking_documents')->withPivot('created_at');
    }
}
