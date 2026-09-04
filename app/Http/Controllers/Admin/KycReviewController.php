<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewKycRequest;
use App\Models\DatabaseNotification;
use App\Models\MitraKycDocument;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KycReviewController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('kyc.review'), 403);

        return view('admin.kyc.index', [
            'documents' => MitraKycDocument::query()
                ->with(['mitra', 'submitter:id,name', 'mediaAsset'])
                ->whereIn('status', ['submitted', 'under_review'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function update(ReviewKycRequest $request, MitraKycDocument $document, AuditLogger $audit): RedirectResponse
    {
        abort_unless(in_array($document->status, ['submitted', 'under_review'], true), 409, 'Dokumen sudah diputuskan.');
        DB::transaction(function () use ($request, $document, $audit) {
            $before = ['status' => $document->status];
            $document->update(['status' => $request->validated('decision'), 'rejection_reason' => $request->validated('reason'), 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);
            DatabaseNotification::create(['user_id' => $document->submitted_by, 'mitra_id' => $document->mitra_id, 'type' => 'kyc.reviewed', 'data' => ['title' => 'KYC telah ditinjau', 'message' => 'Dokumen '.$document->document_type.' berstatus '.$document->status.'.']]);
            $audit->record('admin.kyc_reviewed', $document, $before, ['status' => $document->status, 'reason' => $request->validated('reason')], $request->user());
        });

        return back()->with('status', 'Keputusan KYC tersimpan.');
    }
}
