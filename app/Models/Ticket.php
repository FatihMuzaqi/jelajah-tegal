<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory,HasUlids;

    protected $fillable = ['id', 'ticket_code', 'order_item_id', 'mitra_id', 'holder_user_id', 'qr_token_hash', 'token_version', 'status', 'valid_from', 'valid_until', 'used_at', 'revoked_by', 'revoked_at', 'revocation_reason'];

    protected $hidden = ['qr_token_hash'];

    protected function casts(): array
    {
        return ['token_version' => 'integer', 'valid_from' => 'datetime', 'valid_until' => 'datetime', 'used_at' => 'datetime', 'revoked_at' => 'datetime'];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function holder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'holder_user_id');
    }

    public function holderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'holder_user_id');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function validations(): HasMany
    {
        return $this->hasMany(TicketValidationLog::class);
    }

    public function validationLogs(): HasMany
    {
        return $this->hasMany(TicketValidationLog::class);
    }
}
