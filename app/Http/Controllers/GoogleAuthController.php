<?php

namespace App\Http\Controllers;

use App\Models\OauthIdentity;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        abort_unless(config('services.google.client_id'), 503, 'Google OAuth belum dikonfigurasi.');

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $g = Socialite::driver('google')->user();
        $u = User::firstOrCreate(['email' => $g->getEmail()], ['name' => $g->getName() ?: 'Consumer', 'status' => 'active', 'email_verified_at' => now()]);
        $u->profile()->firstOrCreate([], ['notification_preferences' => []]);
        OauthIdentity::updateOrCreate(['provider' => 'google', 'provider_subject' => $g->getId()], ['user_id' => $u->id, 'provider_email' => $g->getEmail(), 'linked_at' => now(), 'last_used_at' => now()]);
        setPermissionsTeamId(null);
        $u->assignRole('consumer');
        auth()->login($u);

        return redirect()->route('post-login');
    }
}
