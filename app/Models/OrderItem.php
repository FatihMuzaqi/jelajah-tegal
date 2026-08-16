<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory,HasUlids;

    protected $fillable = ['order_id', 'mitra_id', 'catalog_offer_id', 'resource_type', 'reference_id', 'quantity', 'item_name', 'sku', 'unit_price', 'subtotal', 'admin_fee', 'discount_amount', 'line_total', 'booking_date', 'starts_at', 'ends_at', 'fulfillment_status', 'details'];

    protected function casts(): array
    {
        return ['quantity' => 'integer', 'unit_price' => 'decimal:2', 'subtotal' => 'decimal:2', 'admin_fee' => 'decimal:2', 'discount_amount' => 'decimal:2', 'line_total' => 'decimal:2', 'booking_date' => 'date', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'details' => 'array'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(CatalogOffer::class, 'catalog_offer_id');
    }

    public function catalogOffer(): BelongsTo
    {
        return $this->belongsTo(CatalogOffer::class, 'catalog_offer_id');
    }

    public function holds(): HasMany
    {
        return $this->hasMany(OrderReservationHold::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
