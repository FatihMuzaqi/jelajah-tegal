<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory, HasUlids;

    public $timestamps = false;

    protected $fillable = ['mitra_id', 'actor_user_id', 'event', 'auditable_type', 'auditable_id', 'request_id', 'ip_address', 'user_agent', 'before_values', 'after_values', 'metadata', 'created_at'];

    protected function casts(): array
    {
        return ['before_values' => 'array', 'after_values' => 'array', 'metadata' => 'array', 'created_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('Audit logs are append-only.'));
        static::deleting(fn () => throw new \LogicException('Audit logs are append-only.'));
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
