<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MitraMember extends Model
{
    use BelongsToMitra, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['mitra_id', 'user_id', 'status', 'joined_at', 'invited_by'];

    protected function casts(): array
    {
        return ['joined_at' => 'datetime'];
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function gatekeeperAssignments(): HasMany
    {
        return $this->hasMany(GatekeeperAssignment::class, 'member_id');
    }
}
