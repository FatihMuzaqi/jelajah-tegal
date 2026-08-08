<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Mitras\CreateInvitedMitra;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMitraRequest;
use App\Http\Requests\Admin\UpdateMitraStatusRequest;
use App\Models\DatabaseNotification;
use App\Models\Mitra;
use App\Models\Region;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MitraController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('mitras.create'), 403);

        return view('admin.mitras.index', ['mitras' => Mitra::query()->with('region:id,name')->latest()->paginate(15)]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('mitras.create'), 403);

        return view('admin.mitras.create', ['regions' => Region::query()->orderBy('name')->get(['id', 'name'])]);
    }

    public function store(StoreMitraRequest $request, CreateInvitedMitra $action): RedirectResponse
    {
        $mitra = $action->execute($request->user(), $request->validated());

        return redirect()->route('admin.mitras.index')->with('status', 'Mitra '.$mitra->display_name.' dibuat dan undangan owner dikirim.');
    }

    public function status(UpdateMitraStatusRequest $request, Mitra $mitra, AuditLogger $audit): RedirectResponse
    {
        $status = $request->validated('status');
        if ($status === 'active' && ! $mitra->kycDocuments()->where('status', 'approved')->exists()) {
            throw ValidationException::withMessages(['status' => 'Mitra memerlukan minimal satu dokumen KYC approved sebelum aktivasi.']);
        }

        DB::transaction(function () use ($request, $mitra, $status, $audit) {
            $before = ['status' => $mitra->status];
            $mitra->update([
                'status' => $status,
                'approved_by' => $status === 'active' ? $request->user()->id : $mitra->approved_by,
                'approved_at' => $status === 'active' ? now() : $mitra->approved_at,
                'suspended_at' => $status === 'suspended' ? now() : null,
            ]);

            if ($status === 'active' && $mitra->owner_user_id) {
                $mitra->members()->updateOrCreate(
                    ['user_id' => $mitra->owner_user_id],
                    ['status' => 'active', 'joined_at' => now()]
                );
                setPermissionsTeamId($mitra->id);
                $mitra->owner?->assignRole('mitra-owner');
                setPermissionsTeamId(null);
                app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            }

            DatabaseNotification::create(['user_id' => $mitra->owner_user_id, 'mitra_id' => $mitra->id, 'type' => 'mitra.status_changed', 'data' => ['title' => 'Status Mitra berubah', 'message' => $mitra->display_name.' sekarang berstatus '.$status.'.']]);
            $audit->record('admin.mitra_status_changed', $mitra, $before, ['status' => $status, 'reason' => $request->validated('reason')], $request->user());
        });

        return back()->with('status', 'Status Mitra diperbarui dan hak akses owner telah aktif.');
    }
}
