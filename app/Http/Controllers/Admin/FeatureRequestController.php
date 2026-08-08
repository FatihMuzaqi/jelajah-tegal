<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewFeatureRequest;
use App\Models\DatabaseNotification;
use App\Models\MitraFeatureRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeatureRequestController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('feature-requests.review'), 403);

        return view('admin.features.index', ['requests' => MitraFeatureRequest::query()->with(['mitra:id,display_name', 'serviceType:id,name', 'requester:id,name'])->latest()->paginate(20)]);
    }

    public function update(ReviewFeatureRequest $request, MitraFeatureRequest $featureRequest, AuditLogger $audit): RedirectResponse
    {
        abort_unless($featureRequest->status === 'requested', 409, 'Permintaan sudah diputuskan.');
        DB::transaction(function () use ($request, $featureRequest, $audit) {
            $featureRequest->update(['status' => $request->validated('decision'), 'review_note' => $request->validated('reason'), 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);
            if ($featureRequest->status === 'approved') {
                $featureRequest->mitra->features()->updateOrCreate(['service_type_id' => $featureRequest->service_type_id], ['status' => 'enabled', 'enabled_at' => now(), 'disabled_at' => null, 'enabled_by' => $request->user()->id]);
            }
            DatabaseNotification::create(['user_id' => $featureRequest->requested_by, 'mitra_id' => $featureRequest->mitra_id, 'type' => 'mitra.feature_reviewed', 'data' => ['title' => 'Permintaan fitur ditinjau', 'message' => 'Permintaan '.$featureRequest->serviceType->name.' berstatus '.$featureRequest->status.'.']]);
            $audit->record('admin.feature_request_reviewed', $featureRequest, ['status' => 'requested'], ['status' => $featureRequest->status, 'reason' => $request->validated('reason')], $request->user());
        });

        return back()->with('status', 'Keputusan fitur tersimpan.');
    }
}
