<?php

namespace App\Models;

use App\Enums\EventTicketStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventTicket extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['ticket_number', 'event_ticket_type_id', 'mitra_id', 'user_id', 'qr_token_hash', 'status', 'valid_from', 'valid_until', 'used_at'];

    protected $hidden = ['qr_token_hash'];

    protected function casts(): array
    {
        return ['status' => EventTicketStatus::class, 'valid_from' => 'datetime', 'valid_until' => 'datetime', 'used_at' => 'datetime'];
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(EventTicketType::class, 'event_ticket_type_id');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validations(): HasMany
    {
        return $this->hasMany(EventTicketValidationLog::class);
    }
}
