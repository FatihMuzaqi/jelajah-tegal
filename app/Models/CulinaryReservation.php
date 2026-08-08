<?php

namespace App\Models;

use App\Enums\CulinaryReservationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CulinaryReservation extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['reservation_number', 'culinary_venue_id', 'culinary_table_slot_id', 'user_id', 'party_size', 'contact_name', 'contact_phone', 'notes', 'status', 'decided_by', 'decided_at', 'decision_reason'];

    protected function casts(): array
    {
        return ['party_size' => 'integer', 'status' => CulinaryReservationStatus::class, 'decided_at' => 'datetime'];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(CulinaryVenue::class, 'culinary_venue_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(CulinaryTableSlot::class, 'culinary_table_slot_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
