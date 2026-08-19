<?php

namespace App\Http\Controllers\Gatekeeper;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use ResolvesActiveMitra;

    public function edit(Request $request): View
    {
        $user = $request->user();
        $mitra = $this->activeMitra($request);

        return view('gatekeeper.profile.edit', compact('user', 'mitra'));
    }

    public function updatePassword(Request $request, AuditLogger $audit): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user = $request->user();
        $mitra = $this->activeMitra($request);

        if (! $user->credential || ! Hash::check($request->current_password, $user->credential->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            ]);
        }

        $user->credential()->updateOrCreate([], [
            'password_hash' => Hash::make($request->password),
            'failed_login_count' => 0,
            'locked_until' => null,
        ]);

        $audit->record('gatekeeper.password_updated', $mitra, [], ['user_id' => $user->id], $user);

        return back()->with('status', 'Kata sandi akun petugas loket Anda berhasil diperbarui.');
    }
}
