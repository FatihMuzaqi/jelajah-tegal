<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureFlag extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['key_name', 'description', 'status', 'rollout_percentage', 'starts_at', 'ends_at', 'rules', 'owner_user_id'];

    protected function casts(): array
    {
        return ['rules' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
