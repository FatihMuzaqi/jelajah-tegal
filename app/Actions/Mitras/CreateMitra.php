<?php

namespace App\Actions\Mitras;

use App\Models\Mitra;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\MitraContext;
use Illuminate\Support\Facades\DB;

class CreateMitra
{
    public function __construct(private AuditLogger $audit, private MitraContext $context) {}

    public function execute(User $owner, array $attributes): Mitra
    {
        return DB::transaction(function () use ($owner, $attributes) {
            $mitra = Mitra::create(array_merge($attributes, ['owner_user_id' => $owner->id, 'status' => $attributes['status'] ?? 'draft']));
            $mitra->members()->create(['user_id' => $owner->id, 'status' => 'active', 'joined_at' => now()]);

            $this->context->activate($mitra->id);
            try {
                $owner->assignRole('mitra-owner');
                $this->audit->record('mitra.created', $mitra, [], ['status' => $mitra->status], $owner);
            } finally {
                $this->context->clear();
            }

            return $mitra;
        });
    }
}
