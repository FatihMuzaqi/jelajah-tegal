<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use BelongsToMitra, HasFactory, HasUlids;

    protected $fillable = ['mitra_id', 'catalog_offer_id', 'service_date', 'starts_at', 'ends_at', 'capacity', 'reserved_quantity', 'price_override', 'status', 'metadata'];

    protected function casts(): array
    {
        return ['service_date' => 'date', 'capacity' => 'integer', 'reserved_quantity' => 'integer', 'price_override' => 'decimal:2', 'metadata' => 'array'];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(CatalogOffer::class, 'catalog_offer_id');
    }
}
