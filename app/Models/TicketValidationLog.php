<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketValidationLog extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'ticket_id',
        'gatekeeper_user_id',
        'gatekeeper_assignment_id',
        'result',
        'device_reference',
        'presented_token_hash',
        'scanned_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function gatekeeperUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gatekeeper_user_id');
    }

    public function gatekeeper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gatekeeper_user_id');
    }

    public function gatekeeperAssignment(): BelongsTo
    {
        return $this->belongsTo(GatekeeperAssignment::class, 'gatekeeper_assignment_id');
    }
}
