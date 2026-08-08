<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherUsage extends Model
{
    use HasFactory,HasUlids;

    public $timestamps = false;

    protected $fillable = ['voucher_id', 'voucher_claim_id', 'order_id', 'user_id', 'discount_amount', 'status', 'applied_at', 'reversed_at', 'created_at'];

    protected function casts(): array
    {
        return ['discount_amount' => 'decimal:2', 'applied_at' => 'datetime', 'reversed_at' => 'datetime', 'created_at' => 'datetime'];
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(VoucherClaim::class, 'voucher_claim_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
