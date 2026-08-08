<?php

namespace App\Models;

use App\Enums\VoucherStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory,HasUlids,SoftDeletes;

    protected $fillable = ['mitra_id', 'code', 'name', 'discount_type', 'flat_amount', 'percentage_basis_points', 'maximum_discount_amount', 'minimum_order_amount', 'usage_limit', 'used_count', 'per_user_limit', 'starts_at', 'ends_at', 'status', 'created_by'];

    protected function casts(): array
    {
        return ['flat_amount' => 'decimal:2', 'percentage_basis_points' => 'integer', 'maximum_discount_amount' => 'decimal:2', 'minimum_order_amount' => 'decimal:2', 'usage_limit' => 'integer', 'used_count' => 'integer', 'per_user_limit' => 'integer', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'status' => VoucherStatus::class];
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function serviceTypes(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class, 'voucher_service_types');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(VoucherClaim::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }
}
