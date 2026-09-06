<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\RenterDocument;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RenterDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $documents = $request->user()->renterDocuments()->with('media')->latest()->get();

        $counts = [
            'total' => $documents->count(),
            'approved' => $documents->where('status.value', 'approved')->count(),
            'pending' => $documents->where('status.value', 'pending')->count(),
            'rejected' => $documents->where('status.value', 'rejected')->count(),
        ];

        return view('consumer.renter-documents.index', compact('documents', 'counts'));
    }

    public function store(Request $request, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => 'required|in:ktp,sim_a,sim_c,passport',
            'document_number' => 'required|string|max:100',
            'expires_at' => 'nullable|date|after:today',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:8192',
        ], [
            'document_type.required' => 'Pilih jenis dokumen identitas.',
            'document_type.in' => 'Jenis dokumen tidak valid.',
            'document_number.required' => 'Nomor dokumen wajib diisi.',
            'expires_at.after' => 'Masa berlaku dokumen harus lebih dari hari ini.',
            'file.required' => 'Unggah berkas foto atau scan dokumen Anda.',
            'file.mimes' => 'Format berkas harus berupa JPG, JPEG, PNG, atau PDF.',
            'file.max' => 'Ukuran berkas dokumen maksimal 8 MB.',
        ]);

        $file = $request->file('file');
        $path = $file->store('renter-documents/' . $request->user()->id, 'local');

        $asset = MediaAsset::create([
            'owner_user_id' => $request->user()->id,
            'is_platform_owned' => false,
            'disk' => 'local',
            'object_key' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'checksum_sha256' => hash_file('sha256', $file->getRealPath()),
            'visibility' => 'private',
            'purpose' => 'renter_document',
            'status' => 'ready',
            'uploaded_at' => now(),
        ]);

        $doc = $request->user()->renterDocuments()->create([
            'document_type' => $validated['document_type'],
            'document_number' => $validated['document_number'],
            'expires_at' => $validated['expires_at'] ?? null,
            'media_asset_id' => $asset->id,
            'status' => 'pending',
        ]);

        $audit->record('renter_document.submitted', $doc, [], ['document_type' => $doc->document_type], $request->user());

        return back()->with('status', 'Dokumen identitas berhasil diunggah dan sedang dalam antrean verifikasi.');
    }

    public function download(Request $request, RenterDocument $document)
    {
        $this->authorize('view', $document);
        abort_unless($document->media && $document->media->visibility === 'private', 404);

        return Storage::disk($document->media->disk)->download(
            $document->media->object_key,
            $document->media->original_name
        );
    }

    public function destroy(Request $request, RenterDocument $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        if ($document->media) {
            Storage::disk($document->media->disk)->delete($document->media->object_key);
            $document->media->delete();
        }

        $document->delete();

        return back()->with('status', 'Dokumen berhasil dihapus.');
    }
}

