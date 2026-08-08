<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulinaryMenuCategory extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['culinary_venue_id', 'name', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(CulinaryVenue::class, 'culinary_venue_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CulinaryMenuItem::class)->orderByDesc('is_featured')->orderBy('name');
    }
}
