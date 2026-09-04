@extends('layouts.admin')
@section('title', 'Review Dokumen KYC')
@section('page-title', 'Review Dokumen KYC Mitra')
@section('page-description', 'Tinjau dan verifikasi dokumen legalitas mitra secara aman dengan pratinjau langsung tanpa perlu mengunduh file.')

@section('content')
    <!-- 1. Ringkasan Antrean Dokumen -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 18px;">
                    <i class="fa-solid fa-file-circle-exclamation"></i>
                </div>
                <div>
                    <span class="text-muted fs-8 fw-semibold text-uppercase d-block">Menunggu Tinjauan</span>
                    <h4 class="fw-bold text-dark mb-0 fs-5">{{ $documents->total() }} <span class="fs-8 text-muted fw-normal">Dokumen</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 18px;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <span class="text-muted fs-8 fw-semibold text-uppercase d-block">Cakupan Dokumen</span>
                    <h5 class="fw-bold text-dark mb-0 fs-7">KTP, Legalitas, SITU &amp; Pajak</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 18px;">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <div>
                    <span class="text-muted fs-8 fw-semibold text-uppercase d-block">Metode Inspeksi</span>
                    <h5 class="fw-bold text-dark mb-0 fs-7">Pratinjau Langsung In-App</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Antrean KYC Menggunakan x-table-wrapper Lokantara -->
    <x-table-wrapper title="Daftar Antrean Dokumen KYC">
        @if ($documents->isEmpty())
            <tbody>
                <tr>
                    <td colspan="5">
                        <x-empty-state 
                            title="Tidak ada antrean dokumen KYC" 
                            description="Semua pengajuan dokumen legalitas mitra yang masuk telah selesai ditinjau." 
                            compact 
                        />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th style="min-width: 220px;">Nama Mitra</th>
                    <th style="min-width: 220px;">Jenis Dokumen</th>
                    <th style="min-width: 190px;">Pengunggah &amp; Waktu</th>
                    <th style="min-width: 120px;">Status</th>
                    <th class="text-end" style="min-width: 260px;">Aksi Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $document)
                    @php
                        $docTypeIcon = match($document->document_type) {
                            'ktp' => 'fa-id-card text-primary',
                            'business_license' => 'fa-file-contract text-success',
                            'situ' => 'fa-building-circle-check text-warning',
                            'asset_ownership' => 'fa-car-side text-info',
                            'tax_document' => 'fa-receipt text-danger',
                            default => 'fa-file-shield text-secondary',
                        };
                        $docTypeLabel = match($document->document_type) {
                            'ktp' => 'KTP Penanggung Jawab',
                            'business_license' => 'Izin Usaha (NIB/SIUP)',
                            'situ' => 'SITU / Domisili Usaha',
                            'asset_ownership' => 'Bukti Kepemilikan Aset',
                            'tax_document' => 'Dokumen Pajak (NPWP)',
                            default => str($document->document_type)->replace('_', ' ')->headline(),
                        };
                        $isImage = str_starts_with($document->mediaAsset?->mime_type ?? '', 'image/');
                        $isPdf = ($document->mediaAsset?->mime_type ?? '') === 'application/pdf' || str_ends_with(strtolower($document->mediaAsset?->object_key ?? ''), '.pdf');
                    @endphp
                    <tr>
                        <!-- 1. Nama Mitra & Kategori -->
                        <td data-label="Nama Mitra">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <a href="{{ route('admin.mitras.show', $document->mitra) }}" class="text-dark fw-bold fs-7 text-decoration-none hover-primary">
                                    {{ $document->mitra->display_name }}
                                </a>
                                @if ($document->mitra->category === 'dinas')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fs-8 fw-bold">
                                        <i class="fa-solid fa-building-columns me-1"></i> Dinas
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 fs-8">
                                        <i class="fa-solid fa-store me-1"></i> {{ $document->mitra->isDinas() ? 'Dinas' : 'Swasta' }}
                                    </span>
                                @endif
                            </div>
                            <small class="text-muted fs-8 d-block">
                                Legal: {{ $document->mitra->legal_name ?? $document->mitra->display_name }} &middot; <code class="text-muted">/{{ $document->mitra->slug }}</code>
                            </small>
                        </td>

                        <!-- 2. Jenis Dokumen & Versi -->
                        <td data-label="Jenis Dokumen">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid {{ $docTypeIcon }} fs-5 flex-shrink-0"></i>
                                <div>
                                    <strong class="text-dark fs-7 d-block mb-0.5">{{ $docTypeLabel }}</strong>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-0.5 fs-8">
                                            v{{ $document->version }}
                                        </span>
                                        @if ($document->document_number_encrypted)
                                            <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5 fs-8 font-monospace" title="Nomor Dokumen">
                                                #{{ $document->document_number_encrypted }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- 3. Pengunggah & Waktu -->
                        <td data-label="Pengunggah">
                            <span class="text-dark fw-medium fs-8 d-block">
                                <i class="fa-regular fa-user text-muted me-1"></i>{{ $document->submitter->name ?? 'Pengguna' }}
                            </span>
                            <small class="text-muted fs-8 d-block">
                                <i class="fa-regular fa-clock text-muted me-1"></i>{{ $document->created_at->format('d M Y, H:i') }}
                            </small>
                            @if ($document->mediaAsset)
                                <small class="text-muted fs-8 font-monospace d-block mt-0.5">
                                    <i class="fa-solid fa-paperclip text-muted me-1"></i>{{ number_format($document->mediaAsset->size_bytes / 1024, 1) }} KB &middot; {{ strtoupper(pathinfo($document->mediaAsset->original_name ?? '', PATHINFO_EXTENSION) ?: ($isPdf ? 'PDF' : ($isImage ? 'IMG' : 'DOC'))) }}
                                </small>
                            @endif
                        </td>

                        <!-- 4. Status -->
                        <td data-label="Status">
                            <x-status-badge :status="$document->status" />
                        </td>

                        <!-- 5. Aksi Verifikasi -->
                        <td data-label="Aksi" class="text-end">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1.5 flex-nowrap">
                                <!-- Tombol Pratinjau (Modal) -->
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold fs-8 d-inline-flex align-items-center gap-1.5 shadow-sm" 
                                        data-bs-toggle="modal" data-bs-target="#previewModal-{{ $document->id }}" title="Pratinjau dokumen langsung">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Pratinjau</span>
                                </button>

                                <!-- Tombol Setujui -->
                                <form method="POST" action="{{ route('admin.kyc.update', $document) }}" class="d-inline m-0">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="decision" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 fw-bold fs-8 d-inline-flex align-items-center gap-1 shadow-sm" title="Setujui dokumen ini">
                                        <i class="fa-solid fa-check"></i>
                                        <span>Setujui</span>
                                    </button>
                                </form>

                                <!-- Tombol Tolak -->
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-bold fs-8 d-inline-flex align-items-center gap-1" 
                                        data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $document->id }}" title="Tolak dokumen dengan alasan">
                                    <i class="fa-solid fa-xmark"></i>
                                    <span>Tolak</span>
                                </button>

                                <!-- Tombol Unduh File Asli -->
                                <a href="{{ route('admin.kyc.download', $document) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 fs-8 d-inline-flex align-items-center" title="Unduh file fisik ke komputer">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>

                            <!-- MODAL PRATINJAU DOKUMEN -->
                            <div class="modal fade text-start" id="previewModal-{{ $document->id }}" tabindex="-1" aria-labelledby="previewModalLabel-{{ $document->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        <!-- Modal Header -->
                                        <div class="modal-header bg-dark text-white py-3 px-4">
                                            <div class="d-flex align-items-center gap-2.5">
                                                <i class="fa-solid {{ $docTypeIcon }} fs-5 text-info"></i>
                                                <div>
                                                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="previewModalLabel-{{ $document->id }}">
                                                        Pratinjau Dokumen KYC: {{ $docTypeLabel }}
                                                    </h5>
                                                    <small class="text-white-50">
                                                        Mitra: <strong>{{ $document->mitra->display_name }}</strong> &middot; Versi {{ $document->version }}
                                                    </small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <!-- Modal Body -->
                                        <div class="modal-body p-4 bg-light">
                                            <!-- Ringkasan Metadata -->
                                            <div class="card border rounded-3 shadow-none bg-white p-3 mb-3">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-6 col-md-3">
                                                        <small class="text-muted d-block fs-8 fw-semibold text-uppercase">Nama Mitra</small>
                                                        <strong class="text-dark fs-7 d-block text-truncate">{{ $document->mitra->display_name }}</strong>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <small class="text-muted d-block fs-8 fw-semibold text-uppercase">Nomor Dokumen</small>
                                                        <span class="text-dark fs-7 font-monospace fw-medium d-block">{{ $document->document_number_encrypted ?? 'Tidak tercantum' }}</span>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <small class="text-muted d-block fs-8 fw-semibold text-uppercase">Masa Berlaku</small>
                                                        <span class="text-dark fs-7 fw-medium d-block">
                                                            {{ $document->expires_on ? \Carbon\Carbon::parse($document->expires_on)->format('d M Y') : 'Seumur Hidup / Permanen' }}
                                                        </span>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <small class="text-muted d-block fs-8 fw-semibold text-uppercase">Nama File &amp; Ukuran</small>
                                                        <span class="text-dark fs-7 font-monospace d-block text-truncate">
                                                            {{ $document->mediaAsset?->original_name ?? 'Dokumen' }} 
                                                            @if ($document->mediaAsset)
                                                                <span class="text-muted">({{ number_format($document->mediaAsset->size_bytes / 1024, 1) }} KB)</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Preview Canvas -->
                                            <div class="card border rounded-3 shadow-sm overflow-hidden bg-body">
                                                @if ($isImage)
                                                    <div class="d-flex justify-content-center align-items-center p-3 bg-secondary-subtle" style="min-height: 480px; max-height: 72vh; overflow: auto;">
                                                        <img src="{{ route('admin.kyc.preview', $document) }}" 
                                                             alt="Pratinjau {{ $docTypeLabel }}" 
                                                             class="img-fluid rounded border shadow-sm mx-auto d-block" 
                                                             style="max-height: 68vh; width: auto; object-fit: contain;">
                                                    </div>
                                                @elseif ($isPdf)
                                                    <div style="height: 72vh; width: 100%;">
                                                        <iframe src="{{ route('admin.kyc.preview', $document) }}#toolbar=1" 
                                                                class="w-100 h-100 border-0" 
                                                                title="Pratinjau Dokumen PDF {{ $docTypeLabel }}">
                                                        </iframe>
                                                    </div>
                                                @else
                                                    <div style="height: 72vh; width: 100%;">
                                                        <iframe src="{{ route('admin.kyc.preview', $document) }}" 
                                                                class="w-100 h-100 border-0" 
                                                                title="Pratinjau Dokumen {{ $docTypeLabel }}">
                                                        </iframe>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Modal Footer -->
                                        <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.kyc.preview', $document) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1.5 fw-medium">
                                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Tab Penuh
                                                </a>
                                                <a href="{{ route('admin.kyc.download', $document) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-medium">
                                                    <i class="fa-solid fa-download me-1"></i> Unduh File
                                                </a>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-bs-dismiss="modal">
                                                    Tutup
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-bold" 
                                                        data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $document->id }}">
                                                    <i class="fa-solid fa-xmark me-1"></i> Tolak Dokumen
                                                </button>
                                                <form method="POST" action="{{ route('admin.kyc.update', $document) }}" class="d-inline m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="decision" value="approved">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 py-1.5 fw-bold shadow-sm">
                                                        <i class="fa-solid fa-check me-1"></i> Setujui Dokumen Ini
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL TOLAK DOKUMEN -->
                            <div class="modal fade text-start" id="rejectModal-{{ $document->id }}" tabindex="-1" aria-labelledby="rejectModalLabel-{{ $document->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" method="POST" action="{{ route('admin.kyc.update', $document) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="decision" value="rejected">
                                        <div class="modal-header bg-danger text-white py-3 px-4">
                                            <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="rejectModalLabel-{{ $document->id }}">
                                                <i class="fa-solid fa-triangle-exclamation me-1.5"></i> Tolak Dokumen KYC
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-white">
                                            <div class="p-3 bg-light rounded-3 border mb-3">
                                                <div class="fs-8 text-muted">Mitra: <strong>{{ $document->mitra->display_name }}</strong></div>
                                                <div class="fs-8 text-muted mt-0.5">Dokumen: <strong>{{ $docTypeLabel }} (v{{ $document->version }})</strong></div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark fs-7 mb-1">
                                                    Alasan Penolakan <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Foto dokumen buram dan tidak terbaca, mohon unggah ulang dengan resolusi lebih jelas..."></textarea>
                                                <div class="form-text fs-8 text-muted">Alasan ini akan dikirimkan sebagai notifikasi resmi ke akun mitra.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-top py-2.5 px-4">
                                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold">
                                                <i class="fa-solid fa-xmark me-1"></i> Konfirmasi Penolakan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $documents->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
