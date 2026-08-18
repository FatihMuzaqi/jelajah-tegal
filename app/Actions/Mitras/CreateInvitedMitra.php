<?php

namespace App\Actions\Mitras;

use App\Models\Mitra;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class CreateInvitedMitra
{
    public function __construct(private readonly InviteMitraMember $invite, private readonly AuditLogger $audit) {}

    public function execute(User $admin, array $data): Mitra
    {
        return DB::transaction(function () use ($admin, $data) {
            $owner = User::query()->firstOrCreate(
                ['email' => str($data['owner_email'])->lower()->trim()->toString()],
                ['name' => $data['owner_name'], 'status' => 'invited']
            );
            $mitra = Mitra::create([
                'owner_user_id' => $owner->id,
                'category' => $data['category'] ?? 'non_dinas',
                'legal_name' => $data['legal_name'],
                'display_name' => $data['display_name'],
                'slug' => $data['slug'],
                'region_id' => $data['region_id'] ?? null,
                'status' => 'draft',
            ]);

            $this->audit->record('mitra.created', $mitra, [], ['status' => 'draft', 'owner_email' => $owner->email], $admin);
            $this->invite->execute($mitra, $admin, ['name' => $owner->name, 'email' => $owner->email, 'role' => 'mitra-owner']);

            return $mitra;
        });
    }
}
