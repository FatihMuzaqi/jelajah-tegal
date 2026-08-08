<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventTicketType extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['event_id', 'catalog_offer_id', 'name', 'quota', 'issued_quantity', 'reserved_quantity', 'sale_starts_at', 'sale_ends_at'];

    protected function casts(): array
    {
        return ['quota' => 'integer', 'issued_quantity' => 'integer', 'reserved_quantity' => 'integer', 'sale_starts_at' => 'datetime', 'sale_ends_at' => 'datetime'];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(CatalogOffer::class, 'catalog_offer_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(EventTicket::class);
    }
}
