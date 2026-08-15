<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogLocation extends Model
{
    protected $primaryKey = 'catalog_entity_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['catalog_entity_id', 'latitude', 'longitude', 'location'];

    protected $hidden = ['location'];

    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7'];
    }

    public function catalogEntity(): BelongsTo
    {
        return $this->belongsTo(CatalogEntity::class);
    }
}
