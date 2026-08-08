<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogOperatingHour extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['catalog_entity_id', 'weekday', 'sequence', 'opens_at', 'closes_at', 'is_closed'];

    protected function casts(): array
    {
        return ['weekday' => 'integer', 'sequence' => 'integer', 'is_closed' => 'boolean'];
    }

    public function catalogEntity(): BelongsTo
    {
        return $this->belongsTo(CatalogEntity::class);
    }
}
