<?php

namespace App\Http\Controllers;

use App\Models\OauthIdentity;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Dapatkan instance Socialite driver Google dengan HTTP Client terkonfigurasi.
     */
    protected function getGoogleDriver()
    {
        $driver = Socialite::driver('google');

        // Pada Windows local development (XAMPP/PHP), jika sertifikat CA lokal belum dikonfigurasi di php.ini,
        // gunakan HTTP client dengan verify=false khusus di environment local agar terhindar dari cURL error 60.
        if (app()->isLocal()) {
            $driver->setHttpClient(new Client([
                'verify' => false,
                'timeout' => 15,
            ]));
        }

        return $driver;
    }

    /**
     * Redirect pengguna ke halaman otentikasi Google OAuth.
     */
    public function redirect()
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with('error', 'Google OAuth belum dikonfigurasi pada file .env (GOOGLE_CLIENT_ID & GOOGLE_CLIENT_SECRET).');
        }

        return $this->getGoogleDriver()->redirect();
    }

    /**
     * Menangani callback respon dari Google OAuth.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = $this->getGoogleDriver()->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth callback failed: ' . $e->getMessage());

            return redirect()->route('login')->with('error', 'Gagal masuk dengan Google: ' . $e->getMessage());
        }

        $email = $googleUser->getEmail();
        if (empty($email)) {
            return redirect()->route('login')->with('error', 'Alamat email tidak ditemukan dari akun Google Anda.');
        }

        // 1. Cari atau buat User berdasarkan email Google
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $googleUser->getName() ?: 'Wisatawan',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 2. Pastikan email terverifikasi jika sebelumnya belum
        if (is_null($user->email_verified_at)) {
            $user->update(['email_verified_at' => now()]);
        }

        // 3. Buat atau perbarui profil
        $user->profile()->firstOrCreate([], ['notification_preferences' => []]);

        // 4. Catat atau perbarui OAuth Identity link
        OauthIdentity::updateOrCreate(
            [
                'provider' => 'google',
                'provider_subject' => $googleUser->getId(),
            ],
            [
                'user_id' => $user->id,
                'provider_email' => $email,
                'linked_at' => now(),
                'last_used_at' => now(),
            ]
        );

        // 5. Berikan default role 'consumer' jika user belum memiliki role
        setPermissionsTeamId(null);
        if ($user->roles()->count() === 0) {
            $user->assignRole('consumer');
        }

        // 6. Login ke sistem
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('post-login');
    }
}
