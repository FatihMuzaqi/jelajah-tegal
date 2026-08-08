<?php

namespace App\Http\Controllers\Mitra;

use App\Actions\Mitras\SubmitKycDocument;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\UploadKycRequest;
use App\Models\MitraKycDocument;
use App\Services\AuditLogger;
use App\Services\MitraMediaStorage;
use App\Services\PlatformNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KycController extends Controller
{
    use ResolvesActiveMitra;

    public function index(Request $request): View
    {
        $mitra = $this->activeMitra($request);

        return view('mitra.kyc.index', ['mitra' => $mitra, 'documents' => $mitra->kycDocuments()->with('reviewer:id,name')->latest()->paginate(15)]);
    }

    public function store(UploadKycRequest $request, MitraMediaStorage $storage, SubmitKycDocument $submit, AuditLogger $audit, PlatformNotifier $notifier): RedirectResponse
    {
        $mitra = $this->activeMitra($request);
        $media = $storage->store($mitra, $request->file('document'), 'kyc', true);

        try {
            $number = $request->validated('document_number');
            $document = $submit->execute($mitra, $request->user(), $media, $request->validated('document_type'), [
                'document_number_encrypted' => $number,
                'document_fingerprint' => $number ? hash_hmac('sha256', preg_replace('/\s+/', '', $number), config('app.key')) : null,
                'expires_on' => $request->validated('expires_on'),
            ]);
            $audit->record('mitra.kyc_submitted', $document, [], ['type' => $document->document_type, 'version' => $document->version], $request->user());
            $notifier->administrators('admin.kyc_submitted', $mitra->id, ['title' => 'KYC baru', 'message' => $mitra->display_name.' mengirim dokumen '.$document->document_type.'.']);
        } catch (\Throwable $exception) {
            $storage->discard($media);
            throw $exception;
        }

        return back()->with('status', 'Dokumen KYC berhasil dikirim untuk ditinjau.');
    }

    public function download(Request $request, MitraKycDocument $document, AuditLogger $audit): StreamedResponse
    {
        $this->authorize('view', $document);
        $media = $document->mediaAsset;
        abort_unless($media && $media->visibility === 'private' && Storage::disk($media->disk)->exists($media->object_key), 404);
        $audit->record('mitra.kyc_accessed', $document, [], ['purpose' => 'authorized_download'], $request->user());

        return Storage::disk($media->disk)->download($media->object_key, $media->original_name ?? 'dokumen-kyc');
    }
}
