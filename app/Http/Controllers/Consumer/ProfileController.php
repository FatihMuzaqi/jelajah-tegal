<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('consumer.profile.edit', compact('user'));
    }

    public function update(Request $request, AuditLogger $audit): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:20'],
            'preferred_locale' => ['nullable', 'string', 'in:id,en'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 120 karakter.',
        ]);

        $before = $user->only(['name', 'phone', 'preferred_locale']);
        $user->update($validated);

        $audit->record('consumer.profile_updated', $user, $before, $validated, $user);

        return back()->with('status', 'Profil data diri Anda berhasil diperbarui.');
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

        if (! $user->credential || ! Hash::check($request->current_password, $user->credential->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.',
            ]);
        }

        $user->credential()->updateOrCreate([], [
            'password_hash' => Hash::make($request->password),
            'failed_login_count' => 0,
            'locked_until' => null,
        ]);

        $audit->record('consumer.password_updated', $user, [], ['user_id' => $user->id], $user);

        return back()->with('status', 'Kata sandi akun Anda berhasil diperbarui.');
    }
}
