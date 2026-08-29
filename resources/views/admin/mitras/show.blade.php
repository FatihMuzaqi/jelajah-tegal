@extends('layouts.admin')

@section('title', 'Tinjau Mitra: ' . $mitra->display_name)
@section('page-title', 'Detail & Verifikasi Mitra')
@section('page-description', 'Tinjau berkas identitas penanggung jawab, legalitas usaha, lokasi, dan dokumen pendukung mitra.')

@section('page-actions')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.mitras.index') }}" class="btn btn-sm btn-light border fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <a href="{{ route('admin.mitras.edit', $mitra) }}" class="btn btn-sm btn-outline-secondary fw-semibold">
            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Data
        </a>
    </div>
@endsection

@push('head-extra')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .review-section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .review-section-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .review-section-body {
        padding: 20px;
    }
    .meta-label {
        font-size: 11.5px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.03em;
        margin-bottom: 3px;
    }
    .meta-value {
        font-size: 14px;
        color: #0f172a;
        font-weight: 600;
    }
    .admin-preview-map {
        height: 240px;
        width: 100%;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
    }
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 12px;
    }
    .gallery-item {
        height: 110px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        position: relative;
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.2s ease;
    }
    .gallery-item:hover img {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
    <!-- Status & Quick Action Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <h3 class="fw-bold text-dark fs-4 mb-0">{{ $mitra->display_name }}</h3>
                        <x-status-badge :status="$mitra->status" />
                        
                        @if ($mitra->is_verified)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-8 fw-bold">
                                <i class="fa-solid fa-circle-check me-1"></i> Terverifikasi Resmi
                            </span>
                        @else
                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 fs-8">
                                <i class="fa-regular fa-circle me-1"></i> Belum Terverifikasi
                            </span>
                        @endif

                        @if ($mitra->category === 'dinas')
                            <span class="badge bg-info-subtle text-info-emphasis border rounded-pill px-2 py-0.5 fs-8 fw-bold">
                                Dinas
                            </span>
                        @endif
                    </div>
                    <p class="text-muted small mb-0">
                        Legal: <strong>{{ $mitra->legal_name }}</strong> &middot; Layanan: <strong>{{ $mitra->serviceType?->name ?? 'Layanan' }}</strong> ({{ $mitra->categoryModel?->name ?? 'Umum' }}) &middot; Terdaftar: {{ $mitra->created_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <!-- Decision Buttons -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Toggle Badge Terverifikasi -->
                    <form method="POST" action="{{ route('admin.mitras.toggle-verified', $mitra) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $mitra->is_verified ? 'btn-outline-warning' : 'btn-outline-primary' }} rounded-pill px-3 py-1.5 fw-semibold fs-8" title="Beri atau cabut badge Terverifikasi">
                            <i class="fa-solid {{ $mitra->is_verified ? 'fa-badge-check' : 'fa-award' }} me-1"></i>
                            {{ $mitra->is_verified ? 'Cabut Badge' : 'Beri Badge Terverifikasi' }}
                        </button>
                    </form>

                    @if ($mitra->status !== 'active')
                        <!-- Button Setujui Modal -->
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3.5 py-1.5 fw-bold fs-8 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalApproveMitra">
                            <i class="fa-solid fa-check me-1"></i> Setujui & Aktifkan
                        </button>

                        <!-- Button Tolak Modal -->
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3.5 py-1.5 fw-bold fs-8" data-bs-toggle="modal" data-bs-target="#modalRejectMitra">
                            <i class="fa-solid fa-xmark me-1"></i> Tolak Pendaftaran
                        </button>
                    @else
                        <!-- Suspend Button -->
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3.5 py-1.5 fw-bold fs-8" data-bs-toggle="modal" data-bs-target="#modalSuspendMitra">
                            <i class="fa-solid fa-ban me-1"></i> Suspend Mitra
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($mitra->rejection_reason)
        <div class="alert alert-danger border rounded-4 p-3 mb-4 shadow-sm">
            <div class="d-flex align-items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation text-danger mt-1 fs-5 flex-shrink-0"></i>
                <div>
                    <strong class="text-danger fs-7 d-block mb-0.5">Catatan Penolakan Terakhir:</strong>
                    <p class="mb-0 text-dark small">{{ $mitra->rejection_reason }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- LEFT COLUMN: Identitas & Usaha -->
        <div class="col-lg-7">
            <!-- 1. DATA AKUN & PENANGGUNG JAWAB -->
            <div class="review-section-card">
                <div class="review-section-header">
                    <div class="fw-bold text-dark fs-7">
                        <i class="fa-solid fa-id-card text-primary me-2"></i> 1. Data Akun & Penanggung Jawab
                    </div>
                    <span class="badge bg-light text-muted border">Identitas Pemilik</span>
                </div>
                <div class="review-section-body">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="meta-label">Nama Penanggung Jawab</div>
                            <div class="meta-value">{{ $mitra->owner?->name ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="meta-label">NIK (Verifikasi Identitas)</div>
                            <div class="meta-value font-mono">{{ $mitra->owner_nik_encrypted ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="meta-label">Nomor WhatsApp / HP</div>
                            <div class="meta-value">
                                @if ($mitra->contact_phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mitra->contact_phone) }}" target="_blank" class="text-success text-decoration-none">
                                        <i class="fa-brands fa-whatsapp me-1"></i> {{ $mitra->contact_phone }}
                                    </a>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="meta-label">Alamat Email Login</div>
                            <div class="meta-value">{{ $mitra->contact_email ?? $mitra->owner?->email ?? '—' }}</div>
                        </div>
                    </div>

                    <!-- Dokumen KTP -->
                    @php
                        $ktpDoc = $mitra->kycDocuments->where('document_type', 'ktp')->first();
                    @endphp
                    <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2.5">
                            <i class="fa-solid fa-file-shield text-success fs-4"></i>
                            <div>
                                <strong class="text-dark fs-7 d-block">Foto KTP Penanggung Jawab</strong>
                                <small class="text-muted fs-8">
                                    {{ $ktpDoc ? 'Terlampir &middot; Status: ' . $ktpDoc->status : 'Belum diunggah' }}
                                </small>
                            </div>
                        </div>
                        @if ($ktpDoc)
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('admin.kyc.preview', $ktpDoc) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1 fw-bold fs-8">
                                    <i class="fa-solid fa-eye me-1"></i> Pratinjau
                                </a>
                                <a href="{{ route('admin.kyc.download', $ktpDoc) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold fs-8">
                                    <i class="fa-solid fa-download me-1"></i> Unduh
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. DATA USAHA & LOKASI -->
            <div class="review-section-card">
                <div class="review-section-header">
                    <div class="fw-bold text-dark fs-7">
                        <i class="fa-solid fa-store text-primary me-2"></i> 2. Profil Usaha & Titik Lokasi
                    </div>
                    <span class="badge bg-light text-muted border">{{ $mitra->region?->name ?? 'Wilayah Tegal' }}</span>
                </div>
                <div class="review-section-body">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="meta-label">Nama Usaha / Bisnis</div>
                            <div class="meta-value">{{ $mitra->display_name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="meta-label">Nama Legal Usaha</div>
                            <div class="meta-value">{{ $mitra->legal_name }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="meta-label">Jenis Layanan</div>
                            <div class="meta-value">{{ $mitra->serviceType?->name ?? '—' }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="meta-label">Kategori Spesifik</div>
                            <div class="meta-value">{{ $mitra->categoryModel?->name ?? 'Umum' }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="meta-label">Tahun Berdiri</div>
                            <div class="meta-value">{{ $mitra->founded_year ? $mitra->founded_year . ' (' . (date('Y') - $mitra->founded_year) . ' tahun)' : '—' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="meta-label">Deskripsi Usaha</div>
                            <p class="text-dark small mb-0" style="line-height: 1.6;">{{ $mitra->description ?: '—' }}</p>
                        </div>
                        <div class="col-12">
                            <div class="meta-label">Alamat Lengkap Tempat Usaha</div>
                            <div class="meta-value small">{{ $mitra->address ?: '—' }}</div>
                        </div>
                    </div>

                    <!-- Coordinates & Map -->
                    <div class="meta-label mb-1.5">Koordinat & Peta Lokasi</div>
                    @if ($mitra->latitude && $mitra->longitude)
                        <div id="admin-map" class="admin-preview-map mb-2"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small font-mono text-muted fs-8">
                                Lat: {{ $mitra->latitude }} &middot; Lng: {{ $mitra->longitude }}
                            </span>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $mitra->latitude }},{{ $mitra->longitude }}" target="_blank" class="btn btn-sm btn-link text-primary text-decoration-none fs-8 fw-semibold p-0">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka di Google Maps
                            </a>
                        </div>
                    @else
                        <div class="p-3 rounded-3 bg-light border text-center text-muted fs-8">
                            Titik koordinat peta belum diatur oleh mitra.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Legalitas, Galeri, Bank -->
        <div class="col-lg-5">
            <!-- 3. LEGALITAS USAHA -->
            <div class="review-section-card">
                <div class="review-section-header">
                    <div class="fw-bold text-dark fs-7">
                        <i class="fa-solid fa-certificate text-primary me-2"></i> 3. Legalitas Usaha
                    </div>
                    @if ($mitra->nib || $mitra->npwp)
                        <span class="badge bg-success-subtle text-success border">Lengkap</span>
                    @else
                        <span class="badge bg-light text-muted border">UMKM Sederhana</span>
                    @endif
                </div>
                <div class="review-section-body">
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <div class="meta-label">NIB (Nomor Induk Berusaha)</div>
                            <div class="meta-value font-mono">{{ $mitra->nib ?: '— (Tidak diisi)' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="meta-label">NPWP Usaha / Pemilik</div>
                            <div class="meta-value font-mono">{{ $mitra->npwp ?: '— (Tidak diisi)' }}</div>
                        </div>
                    </div>

                    <!-- File Dokumen Izin Usaha & SITU -->
                    @php
                        $licenseDoc = $mitra->kycDocuments->where('document_type', 'business_license')->first();
                        $situDoc = $mitra->kycDocuments->where('document_type', 'situ')->first();
                        $assetDoc = $mitra->kycDocuments->where('document_type', 'asset_ownership')->first();
                    @endphp

                    <div class="d-flex flex-column gap-2">
                        @if ($licenseDoc)
                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                <div class="fs-8">
                                    <strong class="d-block text-dark">Izin Usaha Spesifik (Pariwisata/Halal/SIUP)</strong>
                                    <span class="text-muted">Status: {{ $licenseDoc->status }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <a href="{{ route('admin.kyc.preview', $licenseDoc) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-0.5 fs-8">
                                        <i class="fa-solid fa-eye"></i> Pratinjau
                                    </a>
                                    <a href="{{ route('admin.kyc.download', $licenseDoc) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-0.5 fs-8">
                                        <i class="fa-solid fa-download"></i> Unduh
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($situDoc)
                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                <div class="fs-8">
                                    <strong class="d-block text-dark">SITU / Surat Domisili Usaha</strong>
                                    <span class="text-muted">Status: {{ $situDoc->status }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <a href="{{ route('admin.kyc.preview', $situDoc) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-0.5 fs-8">
                                        <i class="fa-solid fa-eye"></i> Pratinjau
                                    </a>
                                    <a href="{{ route('admin.kyc.download', $situDoc) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-0.5 fs-8">
                                        <i class="fa-solid fa-download"></i> Unduh
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($assetDoc)
                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                <div class="fs-8">
                                    <strong class="d-block text-dark">Bukti Kepemilikan Aset (STNK/BPKB/Sewa)</strong>
                                    <span class="text-muted">Status: {{ $assetDoc->status }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <a href="{{ route('admin.kyc.preview', $assetDoc) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-0.5 fs-8">
                                        <i class="fa-solid fa-eye"></i> Pratinjau
                                    </a>
                                    <a href="{{ route('admin.kyc.download', $assetDoc) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-0.5 fs-8">
                                        <i class="fa-solid fa-download"></i> Unduh
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if (! $licenseDoc && ! $situDoc && ! $assetDoc)
                            <div class="text-muted fs-8 p-2 rounded bg-light text-center">
                                Tidak ada dokumen legalitas tambahan yang diunggah.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 4. FOTO LOKASI & PRODUK USAHA -->
            <div class="review-section-card">
                <div class="review-section-header">
                    <div class="fw-bold text-dark fs-7">
                        <i class="fa-solid fa-camera text-primary me-2"></i> 4. Galeri Foto Lokasi & Produk
                    </div>
                    <span class="badge bg-light text-muted border">{{ $mitra->mediaAssets->count() }} File</span>
                </div>
                <div class="review-section-body">
                    <div class="meta-label mb-2">Foto Fisik Lokasi Usaha (Tampak Depan):</div>
                    @php
                        $locationPhotos = $mitra->mediaAssets->where('purpose', 'business_location');
                        $productPhotos = $mitra->mediaAssets->where('purpose', 'business_product');
                    @endphp

                    @if ($locationPhotos->isNotEmpty())
                        <div class="gallery-grid mb-3">
                            @foreach ($locationPhotos as $photo)
                                <a href="{{ asset('storage/' . $photo->object_key) }}" target="_blank" class="gallery-item d-block" title="{{ $photo->original_name }}">
                                    <img src="{{ asset('storage/' . $photo->object_key) }}" alt="Foto Lokasi">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted fs-8 p-2 rounded bg-light text-center mb-3">Belum ada foto lokasi usaha.</div>
                    @endif

                    <div class="meta-label mb-2">Foto Produk / Layanan:</div>
                    @if ($productPhotos->isNotEmpty())
                        <div class="gallery-grid">
                            @foreach ($productPhotos as $photo)
                                <a href="{{ asset('storage/' . $photo->object_key) }}" target="_blank" class="gallery-item d-block" title="{{ $photo->original_name }}">
                                    <img src="{{ asset('storage/' . $photo->object_key) }}" alt="Foto Produk">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted fs-8 p-2 rounded bg-light text-center">Belum ada foto produk yang diunggah.</div>
                    @endif
                </div>
            </div>

            <!-- 5. DATA REKENING BANK -->
            <div class="review-section-card">
                <div class="review-section-header">
                    <div class="fw-bold text-dark fs-7">
                        <i class="fa-solid fa-credit-card text-primary me-2"></i> 5. Data Rekening Pencairan
                    </div>
                </div>
                <div class="review-section-body">
                    @forelse ($mitra->bankAccounts as $bank)
                        <div class="p-3 rounded-3 border bg-light mb-2">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <strong class="text-dark fs-7">{{ $bank->bank_code }}</strong>
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fs-8">{{ $bank->status }}</span>
                            </div>
                            <div class="font-mono text-dark fw-bold fs-6 mb-1">{{ $bank->account_number_encrypted }}</div>
                            <div class="text-muted fs-8">a.n. {{ $bank->account_name_encrypted }}</div>
                        </div>
                    @empty
                        <div class="text-muted fs-8 p-2 rounded bg-light text-center">Belum ada rekening terdaftar.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL SETUJUI MITRA -->
    <div class="modal fade text-start" id="modalApproveMitra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg rounded-4" method="POST" action="{{ route('admin.mitras.status', $mitra) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="active">
                
                <div class="modal-header border-bottom py-3 px-4 bg-success text-white">
                    <h6 class="modal-title fw-bold">
                        <i class="fa-solid fa-circle-check me-1.5"></i> Setujui & Aktifkan Mitra
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-dark fs-7 mb-3">
                        Apakah Anda yakin ingin menyetujui pendaftaran <strong>{{ $mitra->display_name }}</strong>? Akun owner akan otomatis diberikan akses login ke portal mitra.
                    </p>

                    <div class="p-3 rounded-3 border bg-light mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="approve_verified" {{ $mitra->nib ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark fs-7" for="approve_verified">
                                <i class="fa-solid fa-award text-primary me-1"></i> Berikan Badge Resmi "Terverifikasi"
                            </label>
                            <small class="text-muted d-block fs-8 mt-0.5">
                                Centang jika berkas NIB, KTP, dan kelengkapan dokumen telah diverifikasi sah oleh admin.
                            </small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark fs-8 mb-1">Catatan Tambahan Admin (Opsional)</label>
                        <textarea name="admin_notes" class="form-control fs-8" rows="2" placeholder="Catatan internal dinas..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 bg-light">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Ya, Setujui Mitra
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TOLAK MITRA -->
    <div class="modal fade text-start" id="modalRejectMitra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg rounded-4" method="POST" action="{{ route('admin.mitras.status', $mitra) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                
                <div class="modal-header border-bottom py-3 px-4 bg-danger text-white">
                    <h6 class="modal-title fw-bold">
                        <i class="fa-solid fa-triangle-exclamation me-1.5"></i> Tolak Pendaftaran Mitra
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-dark fs-7 mb-3">
                        Pendaftaran <strong>{{ $mitra->display_name }}</strong> akan ditolak. Pemilik usaha akan melihat catatan penolakan ini saat masuk ke sistem.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">
                            Alasan / Catatan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Jelaskan dokumen yang belum sesuai, foto yang kurang jelas, atau syarat yang belum terpenuhi..."></textarea>
                        <small class="text-muted fs-8 d-block mt-1">Alasan ini akan dikirimkan kepada calon mitra agar dapat memperbaiki data.</small>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 bg-light">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-ban me-1"></i> Tolak Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SUSPEND MITRA -->
    <div class="modal fade text-start" id="modalSuspendMitra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg rounded-4" method="POST" action="{{ route('admin.mitras.status', $mitra) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="suspended">
                
                <div class="modal-header border-bottom py-3 px-4 bg-dark text-white">
                    <h6 class="modal-title fw-bold">
                        <i class="fa-solid fa-ban me-1.5"></i> Suspend Mitra
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-dark fs-7 mb-3">
                        Apakah Anda yakin ingin menonaktifkan sementara operasional <strong>{{ $mitra->display_name }}</strong>?
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">Alasan Penonaktifan (Suspend) <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Alasan penangguhan operasional mitra..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 bg-light">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-4 fw-bold">
                        Suspend Mitra
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
@if ($mitra->latitude && $mitra->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const lat = {{ $mitra->latitude }};
        const lng = {{ $mitra->longitude }};
        const adminMap = L.map('admin-map', {
            center: [lat, lng],
            zoom: 14,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(adminMap);

        L.marker([lat, lng]).addTo(adminMap)
            .bindPopup('<strong>{{ addslashes($mitra->display_name) }}</strong><br>{{ addslashes($mitra->address) }}')
            .openPopup();
    });
</script>
@endif
@endpush
