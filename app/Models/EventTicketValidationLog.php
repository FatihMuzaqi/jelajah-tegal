<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTicketValidationLog extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['event_ticket_id', 'gatekeeper_user_id', 'gatekeeper_assignment_id', 'result', 'device_reference', 'validated_at'];

    protected function casts(): array
    {
        return ['validated_at' => 'datetime'];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(EventTicket::class, 'event_ticket_id');
    }

    public function gatekeeper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gatekeeper_user_id');
    }
}
