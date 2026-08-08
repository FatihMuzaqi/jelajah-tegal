<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourismDestination extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['catalog_entity_id', 'destination_type', 'visit_duration_minutes', 'badge', 'is_hidden_gem'];

    protected function casts(): array
    {
        return ['visit_duration_minutes' => 'integer', 'is_hidden_gem' => 'boolean'];
    }

    public function catalogEntity(): BelongsTo
    {
        return $this->belongsTo(CatalogEntity::class);
    }

    public function ticketPackages(): HasMany
    {
        return $this->hasMany(TourismTicketPackage::class);
    }
}
