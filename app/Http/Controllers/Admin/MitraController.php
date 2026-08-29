<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Mitras\CreateInvitedMitra;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMitraRequest;
use App\Http\Requests\Admin\UpdateMitraRequest;
use App\Http\Requests\Admin\UpdateMitraStatusRequest;
use App\Models\DatabaseNotification;
use App\Models\Mitra;
use App\Models\Region;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MitraController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('mitras.create'), 403);

        $query = Mitra::query()->with(['region:id,name', 'owner:id,name,email']);

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $search = '%' . trim($request->query('q')) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', $search)
                  ->orWhere('legal_name', 'like', $search)
                  ->orWhere('slug', 'like', $search)
                  ->orWhereHas('owner', fn($u) => $u->where('name', 'like', $search)->orWhere('email', 'like', $search));
            });
        }

        $counts = [
            'total' => Mitra::count(),
            'dinas' => Mitra::where('category', 'dinas')->count(),
            'non_dinas' => Mitra::where('category', 'non_dinas')->count(),
            'pending' => Mitra::where('status', 'pending')->count(),
            'active' => Mitra::where('status', 'active')->count(),
            'rejected' => Mitra::where('status', 'rejected')->count(),
            'suspended' => Mitra::where('status', 'suspended')->count(),
        ];

        $mitras = $query->latest()->paginate(15)->withQueryString();

        return view('admin.mitras.index', compact('mitras', 'counts'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('mitras.create'), 403);

        return view('admin.mitras.create', [
            'regions' => Region::query()->orderBy('name')->get(['id', 'name'])
        ]);
    }

    public function store(StoreMitraRequest $request, CreateInvitedMitra $action): RedirectResponse
    {
        $mitra = $action->execute($request->user(), $request->validated());

        return redirect()->route('admin.mitras.index')->with('status', 'Mitra '.$mitra->display_name.' ('.$mitra->category_label.') berhasil dibuat dan undangan owner dikirim.');
    }

    public function show(Mitra $mitra): View
    {
        abort_unless(auth()->user()->can('mitras.create'), 403);

        $mitra->load([
            'region:id,name',
            'serviceType',
            'categoryModel',
            'owner:id,name,email,phone',
            'members.user:id,name,email',
            'features.serviceType',
            'bankAccounts',
            'kycDocuments.mediaAsset',
            'mediaAssets',
            'balance'
        ]);

        return view('admin.mitras.show', compact('mitra'));
    }

    public function edit(Mitra $mitra): View
    {
        abort_unless(auth()->user()->can('mitras.create'), 403);

        $regions = Region::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.mitras.edit', compact('mitra', 'regions'));
    }

    public function update(UpdateMitraRequest $request, Mitra $mitra, AuditLogger $audit): RedirectResponse
    {
        $before = $mitra->only(['category', 'legal_name', 'display_name', 'slug', 'region_id', 'description', 'contact_email', 'contact_phone', 'address']);

        $mitra->update($request->validated());

        $audit->record('admin.mitra_updated', $mitra, $before, $request->validated(), $request->user());

        return redirect()->route('admin.mitras.index')->with('status', 'Data Mitra '.$mitra->display_name.' ('.$mitra->category_label.') berhasil diperbarui.');
    }

    public function status(UpdateMitraStatusRequest $request, Mitra $mitra, AuditLogger $audit): RedirectResponse
    {
        $status = $request->validated('status');

        DB::transaction(function () use ($request, $mitra, $status, $audit) {
            $before = ['status' => $mitra->status, 'is_verified' => $mitra->is_verified];

            $updateData = [
                'status' => $status,
                'rejection_reason' => $status === 'rejected' ? $request->validated('reason') : null,
                'admin_notes' => $request->validated('admin_notes', $mitra->admin_notes),
            ];

            if ($request->has('is_verified')) {
                $updateData['is_verified'] = $request->boolean('is_verified');
            }

            if ($status === 'active') {
                $updateData['approved_by'] = $request->user()->id;
                $updateData['approved_at'] = now();
                $updateData['suspended_at'] = null;

                // Auto-approve pending KYC documents & bank account on mitra activation
                $mitra->kycDocuments()->where('status', 'pending')->update([
                    'status' => 'approved',
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                ]);
                $mitra->bankAccounts()->where('status', 'pending')->update([
                    'status' => 'active',
                    'verified_by' => $request->user()->id,
                    'verified_at' => now(),
                ]);

                if ($mitra->owner_user_id) {
                    $mitra->members()->updateOrCreate(
                        ['user_id' => $mitra->owner_user_id],
                        ['status' => 'active', 'joined_at' => now()]
                    );
                    setPermissionsTeamId($mitra->id);
                    $mitra->owner?->assignRole('mitra-owner');
                    setPermissionsTeamId(null);
                    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                }
            } elseif ($status === 'suspended') {
                $updateData['suspended_at'] = now();
            } elseif ($status === 'rejected') {
                $mitra->kycDocuments()->where('status', 'pending')->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->validated('reason'),
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                ]);
            }

            $mitra->update($updateData);

            DatabaseNotification::create([
                'user_id' => $mitra->owner_user_id,
                'mitra_id' => $mitra->id,
                'type' => 'mitra.status_changed',
                'data' => [
                    'title' => 'Status Pendaftaran Mitra',
                    'message' => $status === 'active'
                        ? 'Selamat! Pendaftaran kemitraan ' . $mitra->display_name . ' telah disetujui dan aktif.'
                        : ($status === 'rejected'
                            ? 'Pendaftaran kemitraan ' . $mitra->display_name . ' belum dapat disetujui: ' . $request->validated('reason')
                            : 'Mitra ' . $mitra->display_name . ' berstatus ' . $status . '.'),
                ]
            ]);

            $audit->record('admin.mitra_status_changed', $mitra, $before, [
                'status' => $status,
                'reason' => $request->validated('reason'),
                'is_verified' => $mitra->is_verified,
            ], $request->user());
        });

        $message = match ($status) {
            'active' => 'Mitra berhasil disetujui & diaktifkan. Hak akses portal mitra telah diberikan kepada akun owner.',
            'rejected' => 'Pendaftaran mitra telah ditolak dan alasan penolakan telah disimpan.',
            'suspended' => 'Mitra berhasil disuspend sementara.',
            default => 'Status Mitra berhasil diperbarui.',
        };

        return back()->with('status', $message);
    }

    public function toggleVerifiedBadge(Request $request, Mitra $mitra): RedirectResponse
    {
        abort_unless(auth()->user()->can('mitras.create'), 403);

        $mitra->update([
            'is_verified' => ! $mitra->is_verified,
        ]);

        $status = $mitra->is_verified ? 'diberikan (Aktif)' : 'dicabut';

        return back()->with('status', "Badge Resmi 'Terverifikasi' untuk {$mitra->display_name} berhasil {$status}.");
    }

    public function resetOwnerPassword(Request $request, Mitra $mitra, AuditLogger $audit): RedirectResponse
    {
        abort_unless(auth()->user()->can('mitras.create'), 403);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $owner = $mitra->owner;
        if (! $owner) {
            throw ValidationException::withMessages(['password' => 'Mitra ini belum memiliki akun owner terdaftar.']);
        }

        $owner->credential()->updateOrCreate([], [
            'password_hash' => \Illuminate\Support\Facades\Hash::make($request->password),
            'failed_login_count' => 0,
            'locked_until' => null,
        ]);

        $audit->record('admin.mitra_owner_password_reset', $mitra, [], [
            'owner_id' => $owner->id,
            'owner_email' => $owner->email,
        ], $request->user());

        return back()->with('status', "Kata sandi akun owner {$mitra->display_name} ({$owner->email}) berhasil direset.");
    }
}
