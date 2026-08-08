<?php

namespace App\Actions\Mitras;

use App\Models\MitraInvitation;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\MitraContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AcceptMitraInvitation
{
    public function __construct(private readonly AuditLogger $audit, private readonly MitraContext $context) {}

    public function execute(string $token, ?User $authenticatedUser, ?string $password): User
    {
        return DB::transaction(function () use ($token, $authenticatedUser, $password) {
            $invitation = MitraInvitation::query()->where('token_hash', hash('sha256', $token))->lockForUpdate()->firstOrFail();

            if (! $invitation->isUsable()) {
                throw ValidationException::withMessages(['token' => 'Undangan tidak berlaku atau sudah digunakan.']);
            }

            $user = User::query()->where('email', $invitation->email)->firstOrFail();
            if ($user->credential && $authenticatedUser?->id !== $user->id) {
                throw ValidationException::withMessages(['token' => 'Masuk dengan akun yang menerima undangan untuk melanjutkan.']);
            }
            if (! $user->credential && ! $password) {
                throw ValidationException::withMessages(['password' => 'Password wajib dibuat untuk mengaktifkan akun.']);
            }

            if (! $user->credential) {
                $user->credential()->create(['password_hash' => Hash::make($password)]);
            }
            $user->forceFill(['status' => 'active', 'email_verified_at' => $user->email_verified_at ?? now()])->save();
            $member = $invitation->mitra->members()->where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            $member->update(['status' => 'active', 'joined_at' => now()]);
            $invitation->update(['accepted_at' => now()]);

            $this->context->activate($invitation->mitra_id);
            try {
                $user->assignRole($invitation->role->name);
                $this->audit->record('mitra.invitation_accepted', $invitation, [], ['role' => $invitation->role->name], $user);
            } finally {
                $this->context->clear();
            }

            return $user;
        });
    }
}
