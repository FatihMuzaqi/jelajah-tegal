<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Mitra;
use App\Models\User;
use App\Support\MitraContext;

class AuditLogger
{
    public function __construct(private MitraContext $context) {}

    public function record(string $event, $subject = null, array $before = [], array $after = [], ?User $actor = null): AuditLog
    {
        $subjectMitraId = $subject instanceof Mitra ? $subject->id : ($subject?->mitra_id ?? null);

        return AuditLog::create(['mitra_id' => $this->context->id() ?? $subjectMitraId ?? session('active_mitra_id'), 'actor_user_id' => $actor?->id ?? auth()->id(), 'event' => $event, 'auditable_type' => $subject ? get_class($subject) : null, 'auditable_id' => $subject?->getKey(), 'request_id' => request()->header('X-Request-ID'), 'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(), 'before_values' => $before, 'after_values' => $after, 'created_at' => now()]);
    }
}
