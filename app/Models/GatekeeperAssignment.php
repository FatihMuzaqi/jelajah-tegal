<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMitra;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatekeeperAssignment extends Model
{
    use BelongsToMitra,HasFactory,HasUlids;

    protected $fillable = ['mitra_id', 'member_id', 'scope_type', 'valid_from', 'valid_until', 'assigned_by', 'revoked_by', 'revoked_at'];

    protected function casts(): array
    {
        return ['valid_from' => 'datetime', 'valid_until' => 'datetime', 'revoked_at' => 'datetime'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MitraMember::class, 'member_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
