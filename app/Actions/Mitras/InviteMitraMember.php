<?php

namespace App\Actions\Mitras;

use App\Models\DatabaseNotification;
use App\Models\Mitra;
use App\Models\MitraInvitation;
use App\Models\User;
use App\Notifications\MitraInvitationNotification;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class InviteMitraMember
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function execute(Mitra $mitra, User $actor, array $data): MitraInvitation
    {
        $role = Role::query()->whereNull('mitra_id')->where('name', $data['role'])->firstOrFail();
        $email = str($data['email'])->lower()->trim()->toString();
        $user = User::query()->where('email', $email)->first();

        if ($user && $mitra->members()->where('user_id', $user->id)->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['email' => 'Pengguna sudah menjadi anggota aktif Mitra ini.']);
        }

        $token = bin2hex(random_bytes(32));
        $invitation = DB::transaction(function () use ($mitra, $actor, $data, $role, $email, $user, $token) {
            $user ??= User::create(['name' => $data['name'], 'email' => $email, 'status' => 'invited']);
            $mitra->members()->updateOrCreate(['user_id' => $user->id], ['status' => 'invited', 'invited_by' => $actor->id, 'joined_at' => null]);
            $mitra->invitations()->where('email', $email)->whereNull('accepted_at')->whereNull('revoked_at')->update(['revoked_at' => now()]);
            $invitation = $mitra->invitations()->create([
                'email' => $email,
                'intended_role_id' => $role->id,
                'token_hash' => hash('sha256', $token),
                'invited_by' => $actor->id,
                'expires_at' => now()->addHours(72),
            ]);
            DatabaseNotification::create(['user_id' => $user->id, 'mitra_id' => $mitra->id, 'type' => 'mitra.invited', 'data' => ['title' => 'Undangan Mitra', 'message' => 'Anda diundang bergabung dengan '.$mitra->display_name.'.']]);
            $this->audit->record('mitra.member_invited', $invitation, [], ['email' => $email, 'role' => $role->name], $actor);

            return $invitation;
        });

        DB::afterCommit(fn () => Notification::route('mail', $email)->notify(new MitraInvitationNotification($mitra->display_name, $token)));

        return $invitation;
    }
}
