<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function registerForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $r): RedirectResponse
    {
        $v = $r->validate(['name' => 'required|max:120', 'email' => 'required|email|unique:users', 'password' => 'required|confirmed|min:8']);
        $u = DB::transaction(function () use ($v) {
            $u = User::create(['name' => $v['name'], 'email' => $v['email'], 'status' => 'active']);
            $u->credential()->create(['password_hash' => Hash::make($v['password'])]);
            $u->profile()->create(['notification_preferences' => []]);
            setPermissionsTeamId(null);
            $u->assignRole('consumer');

            return $u;
        });
        Auth::login($u);
        $u->sendEmailVerificationNotification();

        return redirect()->route('verification.notice');
    }

    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $r): RedirectResponse
    {
        $v = $r->validate(['email' => 'required|email', 'password' => 'required']);
        $u = User::where('email', $v['email'])->first();
        if (! $u || $u->status !== 'active' || ! $u->credential || ! Hash::check($v['password'], $u->credential->password_hash)) {
            return back()->withErrors(['email' => 'Kredensial tidak valid.'])->onlyInput('email');
        }
        Auth::login($u, $r->boolean('remember'));
        $r->session()->regenerate();
        $u->update(['last_login_at' => now()]);

        return redirect()->intended(route('post-login'));
    }

    public function logout(Request $r): RedirectResponse
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect('/');
    }

    public function forgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function forgot(Request $r): RedirectResponse
    {
        $r->validate(['email' => 'required|email']);
        $s = Password::sendResetLink($r->only('email'));

        return back()->with('status', __($s));
    }

    public function resetForm(Request $r, string $token): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $r->email]);
    }

    public function reset(Request $r): RedirectResponse
    {
        $v = $r->validate(['token' => 'required', 'email' => 'required|email', 'password' => 'required|confirmed|min:8']);
        $s = Password::reset($v, function (User $u, string $p) {
            $u->credential()->updateOrCreate([], ['password_hash' => Hash::make($p)]);
            $u->setRememberToken(Str::random(60));
            $u->save();
            event(new PasswordReset($u));
        });

        return $s === Password::PasswordReset ? redirect()->route('login')->with('status', __($s)) : back()->withErrors(['email' => __($s)]);
    }
}
