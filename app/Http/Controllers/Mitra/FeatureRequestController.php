<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\StoreFeatureRequest;
use App\Models\MitraFeatureRequest;
use App\Models\ServiceType;
use App\Services\AuditLogger;
use App\Services\PlatformNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FeatureRequestController extends Controller
{
    use ResolvesActiveMitra;

    public function index(Request $request): View
    {
        $mitra = $this->activeMitra($request);

        return view('mitra.features.index', ['mitra' => $mitra, 'services' => ServiceType::query()->orderBy('sort_order')->get(), 'features' => $mitra->features()->with('serviceType:id,name')->get(), 'requests' => $mitra->featureRequests()->with('serviceType:id,name')->latest()->paginate(15)]);
    }

    public function store(StoreFeatureRequest $request, AuditLogger $audit, PlatformNotifier $notifier): RedirectResponse
    {
        $mitra = $this->activeMitra($request);
        $serviceId = $request->integer('service_type_id');
        if ($mitra->features()->where('service_type_id', $serviceId)->where('status', 'enabled')->exists()) {
            throw ValidationException::withMessages(['service_type_id' => 'Fitur tersebut sudah aktif.']);
        }
        if ($mitra->featureRequests()->where('service_type_id', $serviceId)->where('status', 'requested')->exists()) {
            throw ValidationException::withMessages(['service_type_id' => 'Permintaan untuk fitur tersebut masih diproses.']);
        }

        $featureRequest = MitraFeatureRequest::create(['mitra_id' => $mitra->id, 'service_type_id' => $serviceId, 'requested_by' => $request->user()->id, 'status' => 'requested', 'reason' => $request->validated('reason')]);
        $audit->record('mitra.feature_requested', $featureRequest, [], ['service_type_id' => $serviceId], $request->user());
        $notifier->administrators('admin.feature_requested', $mitra->id, ['title' => 'Permintaan fitur baru', 'message' => $mitra->display_name.' mengajukan fitur tambahan.']);

        return back()->with('status', 'Permintaan fitur dikirim.');
    }
}
