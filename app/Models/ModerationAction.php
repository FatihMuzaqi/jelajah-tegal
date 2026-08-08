<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModerationAction extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = ['report_id', 'actor_user_id', 'action_type', 'from_status', 'to_status', 'notes', 'metadata', 'created_at'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'created_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('Moderation actions are append-only.'));
        static::deleting(fn () => throw new \LogicException('Moderation actions are append-only.'));
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(ModerationReport::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
