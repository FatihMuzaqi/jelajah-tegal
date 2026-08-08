<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceType extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['code', 'name', 'is_transactional', 'sort_order'];

    protected function casts(): array
    {
        return ['is_transactional' => 'boolean', 'sort_order' => 'integer'];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
