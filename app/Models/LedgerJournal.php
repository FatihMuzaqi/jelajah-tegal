<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LedgerJournal extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = ['journal_number', 'mitra_id', 'event_key', 'event_type', 'order_id', 'payment_id', 'withdrawal_claim_id', 'reversal_of_id', 'description', 'effective_at', 'posted_at', 'metadata', 'created_at'];

    protected function casts(): array
    {
        return ['effective_at' => 'datetime', 'posted_at' => 'datetime', 'metadata' => 'array', 'created_at' => 'datetime'];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(LedgerLine::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
