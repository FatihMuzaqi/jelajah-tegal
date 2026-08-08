<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodation extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['catalog_entity_id', 'property_type', 'check_in_time', 'check_out_time', 'star_rating'];

    protected function casts(): array
    {
        return ['star_rating' => 'integer'];
    }

    public function catalogEntity(): BelongsTo
    {
        return $this->belongsTo(CatalogEntity::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(AccommodationRoom::class);
    }
}
