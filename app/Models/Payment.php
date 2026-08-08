<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory,HasUlids;

    protected $fillable = ['order_id', 'mitra_id', 'provider', 'provider_reference', 'snap_token', 'snap_redirect_url', 'method', 'currency', 'amount', 'status', 'paid_at', 'authorized_at', 'expired_at', 'failed_at', 'cancelled_at', 'refunded_at', 'last_synced_at', 'failure_code', 'provider_snapshot'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'status' => PaymentStatus::class, 'paid_at' => 'datetime', 'authorized_at' => 'datetime', 'expired_at' => 'datetime', 'failed_at' => 'datetime', 'cancelled_at' => 'datetime', 'refunded_at' => 'datetime', 'last_synced_at' => 'datetime', 'provider_snapshot' => 'array'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }
}
