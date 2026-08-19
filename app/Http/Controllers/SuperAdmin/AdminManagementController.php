<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreAdminUserRequest;
use App\Http\Requests\SuperAdmin\UpdateAdminUserRequest;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminManagementController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user() ?? auth()->user();
        abort_unless($user && ($user->hasRole('super-admin') || $user->can('users.manage')), 403);

        $adminRoleNames = ['admin', 'super-admin', 'dinas-supervisor'];

        $query = User::role($adminRoleNames)->with('roles');

        if ($request->filled('q')) {
            $search = '%' . trim($request->query('q')) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('phone', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('role')) {
            $query->role($request->query('role'));
        }

        $counts = [
            'total' => User::role($adminRoleNames)->count(),
            'active' => User::role($adminRoleNames)->where('status', 'active')->count(),
            'suspended' => User::role($adminRoleNames)->where('status', 'suspended')->count(),
            'admins' => User::role('admin')->count(),
            'super_admins' => User::role('super-admin')->count(),
        ];

        $admins = $query->latest()->paginate(15)->withQueryString();

        return view('super-admin.admins.index', compact('admins', 'counts'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasRole('super-admin') || auth()->user()->can('users.manage'), 403);

        return view('super-admin.admins.create');
    }

    public function store(StoreAdminUserRequest $request, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validated();
        $targetRole = $validated['role'] ?? 'admin';

        $user = DB::transaction(function () use ($validated, $targetRole, $request, $audit) {
            $u = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $u->credential()->create([
                'password_hash' => Hash::make($validated['password']),
            ]);

            $u->profile()->create([
                'notification_preferences' => [],
            ]);

            setPermissionsTeamId(null);
            $u->assignRole($targetRole);

            $audit->record('superadmin.admin_created', $u, [], [
                'name' => $u->name,
                'email' => $u->email,
                'role' => $targetRole,
            ], $request->user());

            return $u;
        });

        return redirect()->route('super-admin.admins.index')->with('status', 'Akun Administrator ' . $user->name . ' (' . $user->email . ') berhasil dibuat dan langsung aktif.');
    }

    public function edit(User $admin): View
    {
        abort_unless(auth()->user()->hasRole('super-admin') || auth()->user()->can('users.manage'), 403);

        $admin->load('roles');

        return view('super-admin.admins.edit', compact('admin'));
    }

    public function update(UpdateAdminUserRequest $request, User $admin, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validated();
        $before = $admin->only(['name', 'email', 'phone', 'status']);

        DB::transaction(function () use ($admin, $validated, $before, $request, $audit) {
            $admin->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'status' => $validated['status'],
            ]);

            if (!empty($validated['password'])) {
                $admin->credential()->updateOrCreate(
                    [],
                    ['password_hash' => Hash::make($validated['password'])]
                );
            }

            if (!empty($validated['role']) && !$admin->hasRole('super-admin')) {
                setPermissionsTeamId(null);
                $admin->syncRoles([$validated['role']]);
            }

            $audit->record('superadmin.admin_updated', $admin, $before, $validated, $request->user());
        });

        return redirect()->route('super-admin.admins.index')->with('status', 'Data Administrator ' . $admin->name . ' berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, User $admin, AuditLogger $audit): RedirectResponse
    {
        $user = $request->user() ?? auth()->user();
        abort_unless($user && ($user->hasRole('super-admin') || $user->can('users.manage')), 403);

        if ($admin->id === $user->id) {
            return back()->withErrors(['status' => 'Anda tidak dapat menonaktifkan akun Super Admin Anda sendiri.']);
        }

        $newStatus = $admin->status === 'active' ? 'suspended' : 'active';
        $before = ['status' => $admin->status];

        $admin->update(['status' => $newStatus]);

        $audit->record('superadmin.admin_status_changed', $admin, $before, ['status' => $newStatus], $request->user());

        $msg = $newStatus === 'active' 
            ? 'Akun Administrator ' . $admin->name . ' telah diaktifkan kembali.' 
            : 'Akun Administrator ' . $admin->name . ' telah dinonaktifkan (suspended).';

        return back()->with('status', $msg);
    }

    public function resetPassword(Request $request, User $admin, AuditLogger $audit): RedirectResponse
    {
        $user = $request->user() ?? auth()->user();
        abort_unless($user && ($user->hasRole('super-admin') || $user->can('users.manage')), 403);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $admin->credential()->updateOrCreate([], [
            'password_hash' => Hash::make($request->password),
            'failed_login_count' => 0,
            'locked_until' => null,
        ]);

        $audit->record('superadmin.admin_password_reset', $admin, [], [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
        ], $user);

        return back()->with('status', "Kata sandi Administrator {$admin->name} ({$admin->email}) berhasil direset.");
    }
}
