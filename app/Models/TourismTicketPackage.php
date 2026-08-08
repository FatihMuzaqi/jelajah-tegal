<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourismTicketPackage extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['tourism_destination_id', 'catalog_offer_id', 'name', 'quota_per_day'];

    protected function casts(): array
    {
        return ['quota_per_day' => 'integer'];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(TourismDestination::class, 'tourism_destination_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(CatalogOffer::class, 'catalog_offer_id');
    }
}
