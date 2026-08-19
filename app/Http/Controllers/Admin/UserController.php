<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['roles', 'credential']);

        if ($request->filled('q')) {
            $search = '%' . trim($request->query('q')) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('phone', 'like', $search);
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($r) => $r->where('name', $request->query('role')));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function resetPassword(Request $request, User $user, AuditLogger $audit): RedirectResponse
    {
        abort_unless(auth()->user()->can('users.manage') || auth()->user()->hasRole(['admin', 'super-admin']), 403);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user->credential()->updateOrCreate([], [
            'password_hash' => Hash::make($request->password),
            'failed_login_count' => 0,
            'locked_until' => null,
        ]);

        $audit->record('admin.user_password_reset', $user, [], [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ], $request->user());

        return back()->with('status', "Kata sandi akun {$user->name} ({$user->email}) berhasil direset.");
    }
}
