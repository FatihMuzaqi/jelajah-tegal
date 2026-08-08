<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\UpdateOperatingHoursRequest;
use App\Http\Requests\Mitra\UpdateProfileRequest;
use App\Http\Requests\Mitra\UploadBrandMediaRequest;
use App\Models\ApplicationSetting;
use App\Models\Region;
use App\Services\AuditLogger;
use App\Services\MitraMediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use ResolvesActiveMitra;

    public function edit(Request $request): View
    {
        $mitra = $this->activeMitra($request)->load(['region:id,name', 'features.serviceType:id,name', 'operatingHours']);
        $commission = ApplicationSetting::query()->forMitra($mitra)->where('key_name', 'finance.commission_rate')->where('is_secret', false)->value('value_json');

        return view('mitra.profile.edit', ['mitra' => $mitra, 'regions' => Region::query()->orderBy('name')->get(['id', 'name']), 'commission' => $commission]);
    }

    public function update(UpdateProfileRequest $request, AuditLogger $audit): RedirectResponse
    {
        $mitra = $this->activeMitra($request);
        $before = $mitra->only(array_keys($request->validated()));
        $mitra->update($request->validated());
        $audit->record('mitra.profile_updated', $mitra, $before, $mitra->only(array_keys($request->validated())), $request->user());

        return back()->with('status', 'Profil Mitra diperbarui.');
    }

    public function media(UploadBrandMediaRequest $request, string $type, MitraMediaStorage $storage, AuditLogger $audit): RedirectResponse
    {
        abort_unless(in_array($type, ['logo', 'banner'], true), 404);
        $mitra = $this->activeMitra($request);
        $media = $storage->store($mitra, $request->file('image'), $type, false);
        $column = $type.'_media_id';
        $before = [$column => $mitra->{$column}];
        $mitra->update([$column => $media->id]);
        $audit->record('mitra.'.$type.'_updated', $mitra, $before, [$column => $media->id], $request->user());

        return back()->with('status', str($type)->headline().' diperbarui.');
    }

    public function hours(UpdateOperatingHoursRequest $request, AuditLogger $audit): RedirectResponse
    {
        $mitra = $this->activeMitra($request);
        DB::transaction(function () use ($mitra, $request, $audit) {
            foreach ($request->validated('hours') as $hour) {
                $closed = (bool) ($hour['is_closed'] ?? false);
                $mitra->operatingHours()->updateOrCreate(['day_of_week' => $hour['day_of_week']], ['is_closed' => $closed, 'opens_at' => $closed ? null : $hour['opens_at'], 'closes_at' => $closed ? null : $hour['closes_at']]);
            }
            $audit->record('mitra.operating_hours_updated', $mitra, [], ['days' => 7], $request->user());
        });

        return back()->with('status', 'Jam operasional diperbarui.');
    }
}
