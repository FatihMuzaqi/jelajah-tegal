<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory,HasUlids;

    protected $fillable = ['order_number', 'user_id', 'mitra_id', 'voucher_id', 'currency', 'subtotal', 'admin_fee', 'discount_amount', 'total_amount', 'commission_basis_points', 'commission_amount', 'mitra_net_amount', 'status', 'payment_status', 'user_snapshot', 'mitra_snapshot', 'voucher_snapshot', 'placed_at', 'expires_at', 'paid_at', 'cancelled_at'];

    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'admin_fee' => 'decimal:2', 'discount_amount' => 'decimal:2', 'total_amount' => 'decimal:2', 'commission_basis_points' => 'integer', 'commission_amount' => 'decimal:2', 'mitra_net_amount' => 'decimal:2', 'status' => OrderStatus::class, 'payment_status' => PaymentStatus::class, 'user_snapshot' => 'array', 'mitra_snapshot' => 'array', 'voucher_snapshot' => 'array', 'placed_at' => 'datetime', 'expires_at' => 'datetime', 'paid_at' => 'datetime', 'cancelled_at' => 'datetime'];
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->where($field ?? 'id', $value)
            ->orWhere('order_number', $value)
            ->firstOrFail();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function voucherUsages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function journals(): HasMany
    {
        return $this->hasMany(LedgerJournal::class);
    }
}
