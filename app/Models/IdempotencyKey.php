<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdempotencyKey extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = ['user_id', 'scope', 'key_value', 'fingerprint', 'order_id', 'response_status', 'response_payload', 'expires_at', 'created_at'];

    protected function casts(): array
    {
        return ['response_payload' => 'array', 'expires_at' => 'datetime', 'created_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
