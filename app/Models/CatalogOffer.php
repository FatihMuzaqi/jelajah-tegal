<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogOffer extends Model
{
    use BelongsToMitra, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['mitra_id', 'catalog_entity_id', 'offer_type', 'sku', 'name', 'currency', 'price', 'status', 'purchasable_from', 'purchasable_until', 'min_quantity', 'max_quantity'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'purchasable_from' => 'datetime', 'purchasable_until' => 'datetime', 'min_quantity' => 'integer', 'max_quantity' => 'integer'];
    }

    public function catalogEntity(): BelongsTo
    {
        return $this->belongsTo(CatalogEntity::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function ticketPackage(): HasOne
    {
        return $this->hasOne(TourismTicketPackage::class);
    }

    public function accommodationRoom(): HasOne
    {
        return $this->hasOne(AccommodationRoom::class);
    }

    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
}
