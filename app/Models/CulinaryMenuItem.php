<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CulinaryMenuItem extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['culinary_venue_id', 'culinary_menu_category_id', 'name', 'description', 'price', 'is_featured', 'status'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_featured' => 'boolean'];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(CulinaryVenue::class, 'culinary_venue_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CulinaryMenuCategory::class, 'culinary_menu_category_id');
    }
}
