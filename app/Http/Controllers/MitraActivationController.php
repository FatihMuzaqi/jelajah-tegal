<?php

namespace App\Http\Controllers;

use App\Actions\Mitras\AcceptMitraInvitation;
use App\Http\Requests\ActivateMitraInvitationRequest;
use App\Models\MitraInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MitraActivationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = $this->invitation($token);
        $user = User::query()->where('email', $invitation->email)->firstOrFail();

        return view('auth.mitra-activation', compact('invitation', 'token', 'user'));
    }

    public function store(ActivateMitraInvitationRequest $request, string $token, AcceptMitraInvitation $action): RedirectResponse
    {
        $user = $action->execute($token, $request->user(), $request->validated('password'));
        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->route('mitra.select')->with('status', 'Undangan berhasil diaktifkan. Pilih Mitra untuk melanjutkan.');
    }

    private function invitation(string $token): MitraInvitation
    {
        $invitation = MitraInvitation::query()->with(['mitra:id,display_name', 'role:id,name'])->where('token_hash', hash('sha256', $token))->firstOrFail();
        abort_unless($invitation->isUsable(), 410, 'Undangan tidak lagi berlaku.');

        return $invitation;
    }
}
