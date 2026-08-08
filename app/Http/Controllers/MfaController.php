<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MfaController extends Controller
{
    public function setup(Request $r)
    {
        abort_unless($r->user()->can('access.admin') || $r->user()->can('access.super-admin'), 403);
        $c = $r->user()->credential()->firstOrCreate([], []);
        if (! $c->mfa_secret_encrypted) {
            $c->update(['mfa_secret_encrypted' => app('pragmarx.google2fa')->generateSecretKey()]);
        }

        return view('auth.mfa-setup', ['secret' => $c->fresh()->mfa_secret_encrypted]);
    }

    public function confirm(Request $r)
    {
        abort_unless($r->user()->can('access.admin') || $r->user()->can('access.super-admin'), 403);
        $code = $r->validate(['code' => 'required|digits:6'])['code'];
        $c = $r->user()->credential;
        abort_unless(app('pragmarx.google2fa')->verifyKey($c->mfa_secret_encrypted, $code), 422, 'Kode MFA tidak valid.');
        $c->update(['mfa_confirmed_at' => now()]);
        for ($i = 0; $i < 8; $i++) {
            $r->user()->mfaRecoveryCodes()->create(['code_hash' => Hash::make(str()->random(12))]);
        }$r->session()->put('mfa_verified_at', now()->timestamp);

        return redirect()->route('post-login');
    }

    public function challenge(Request $r)
    {
        abort_unless($r->session()->has('mfa.pending_user_id'), 403);

        return view('auth.mfa-challenge');
    }

    public function verify(Request $r)
    {
        $u = User::findOrFail($r->session()->get('mfa.pending_user_id'));
        $code = $r->validate(['code' => 'required|string'])['code'];
        $valid = app('pragmarx.google2fa')->verifyKey($u->credential->mfa_secret_encrypted, $code);
        if (! $valid) {
            $recovery = $u->mfaRecoveryCodes()->whereNull('used_at')->get()->first(fn ($item) => Hash::check($code, $item->code_hash));
            if ($recovery) {
                $recovery->update(['used_at' => now()]);
                $valid = true;
            }
        }abort_unless($valid, 422, 'Kode MFA tidak valid.');
        Auth::login($u);
        $r->session()->forget('mfa.pending_user_id');
        $r->session()->put('mfa_verified_at', now()->timestamp);

        return redirect()->route('post-login');
    }
}
