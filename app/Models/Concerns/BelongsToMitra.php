<?php

namespace App\Models\Concerns;

use App\Models\Mitra;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToMitra
{
    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function scopeForMitra(Builder $query, Mitra|string $mitra): Builder
    {
        return $query->where($query->qualifyColumn('mitra_id'), $mitra instanceof Mitra ? $mitra->getKey() : $mitra);
    }
}
