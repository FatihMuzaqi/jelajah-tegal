<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReservationHold extends Model
{
    use HasFactory,HasUlids;

    protected $fillable = ['order_item_id', 'resource_type', 'resource_id', 'service_date', 'quantity', 'status', 'details'];

    protected function casts(): array
    {
        return ['service_date' => 'date', 'quantity' => 'integer', 'details' => 'array'];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
