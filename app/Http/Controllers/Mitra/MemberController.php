<?php

namespace App\Http\Controllers\Mitra;

use App\Actions\Mitras\InviteMitraMember;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\InviteMemberRequest;
use App\Models\DatabaseNotification;
use App\Models\MitraMember;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberController extends Controller
{
    use ResolvesActiveMitra;

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('members.manage'), 403);
        $mitra = $this->activeMitra($request);

        return view('mitra.members.index', ['mitra' => $mitra, 'members' => $mitra->members()->with('user:id,name,email')->latest()->paginate(15), 'invitations' => $mitra->invitations()->with('role:id,name')->latest()->limit(10)->get()]);
    }

    public function store(InviteMemberRequest $request, InviteMitraMember $action): RedirectResponse
    {
        $action->execute($this->activeMitra($request), $request->user(), $request->validated());

        return back()->with('status', 'Undangan anggota dikirim.');
    }

    public function destroy(Request $request, MitraMember $member, AuditLogger $audit): RedirectResponse
    {
        abort_unless($request->user()->can('members.manage'), 403);
        $mitra = $this->activeMitra($request);
        abort_unless($member->mitra_id === $mitra->id, 404);
        abort_if($member->user_id === $mitra->owner_user_id || $member->user_id === $request->user()->id, 422, 'Owner atau akun sendiri tidak dapat dicabut.');

        DB::transaction(function () use ($member, $request, $audit, $mitra) {
            $member->update(['status' => 'revoked']);
            foreach (['mitra-staff', 'gatekeeper'] as $role) {
                if ($member->user->hasRole($role)) {
                    $member->user->removeRole($role);
                }
            }
            DatabaseNotification::create(['user_id' => $member->user_id, 'mitra_id' => $mitra->id, 'type' => 'mitra.membership_revoked', 'data' => ['title' => 'Akses Mitra dicabut', 'message' => 'Keanggotaan Anda pada '.$mitra->display_name.' telah dicabut.']]);
            $audit->record('mitra.member_revoked', $member, ['status' => 'active'], ['status' => 'revoked'], $request->user());
        });

        return back()->with('status', 'Keanggotaan dicabut.');
    }

    public function resetPassword(Request $request, MitraMember $member, AuditLogger $audit): RedirectResponse
    {
        abort_unless($request->user()->can('members.manage'), 403);
        $mitra = $this->activeMitra($request);
        abort_unless($member->mitra_id === $mitra->id, 404);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $targetUser = $member->user;
        $targetUser->credential()->updateOrCreate([], [
            'password_hash' => \Illuminate\Support\Facades\Hash::make($request->password),
            'failed_login_count' => 0,
            'locked_until' => null,
        ]);

        $audit->record('mitra.member_password_reset', $mitra, [], ['member_id' => $member->id, 'user_id' => $targetUser->id], $request->user());

        return back()->with('status', "Kata sandi untuk anggota {$targetUser->name} ({$targetUser->email}) berhasil direset.");
    }
}
