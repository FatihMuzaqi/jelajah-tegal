@extends('layouts.consumer')

@section('title', 'Dokumen Identitas & Sewa')
@section('page-title', 'Dokumen Verifikasi Rental')
@section('page-description', 'Kelola KTP, SIM A, SIM C, dan Paspor Anda untuk verifikasi identitas resmi saat memesan rental kendaraan di Jelajah Tegal.')

@section('content')
<style>
/* Modern Consumer Document UI */
.doc-hero-banner {
    background: linear-gradient(135deg, #064e3b 0%, #047857 60%, #059669 100%);
    border-radius: 20px;
    padding: 24px 28px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px -5px rgba(6, 78, 59, 0.2);
}
.doc-hero-banner::after {
    content: '';
    position: absolute;
    right: -30px;
    bottom: -30px;
    width: 160px;
    height: 160px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.doc-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.doc-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
}
.doc-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.doc-type-radio-group {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}
@media (min-width: 576px) {
    .doc-type-radio-group {
        grid-template-columns: repeat(4, 1fr);
    }
}
.doc-type-card-label {
    display: block;
    cursor: pointer;
    margin-bottom: 0;
}
.doc-type-card-label input[type="radio"] {
    display: none;
}
.doc-type-card-box {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 10px;
    text-align: center;
    background: #ffffff;
    transition: all 0.2s ease;
}
.doc-type-card-label input[type="radio"]:checked + .doc-type-card-box {
    border-color: #047857;
    background: #ecfdf5;
    color: #065f46;
    box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.15);
}
.doc-type-card-box i {
    font-size: 20px;
    margin-bottom: 4px;
    display: block;
}
.doc-type-card-box .title {
    font-size: 12.5px;
    font-weight: 700;
}
.doc-type-card-box .subtitle {
    font-size: 10.5px;
    color: #64748b;
    display: block;
}
.doc-type-card-label input[type="radio"]:checked + .doc-type-card-box .subtitle {
    color: #047857;
}

/* File Upload Dropzone */
.file-dropzone-box {
    border: 2px dashed #cbd5e1;
    border-radius: 14px;
    padding: 24px 16px;
    text-align: center;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}
.file-dropzone-box:hover,
.file-dropzone-box.dragover {
    border-color: #047857;
    background: #ecfdf5;
}

/* Saved Document Cards */
.saved-doc-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.saved-doc-card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}
.doc-icon-badge {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

/* Dark Mode Overrides */
[data-theme='dark'] .doc-stat-card,
[data-theme='dark'] .saved-doc-card,
[data-theme='dark'] .doc-type-card-box {
    background: var(--lokantara-surface, #1e293b);
    border-color: var(--lokantara-border, #334155);
    color: var(--lokantara-text, #f8fafc);
}
[data-theme='dark'] .file-dropzone-box {
    background: rgba(255, 255, 255, 0.03);
    border-color: var(--lokantara-border, #334155);
}
[data-theme='dark'] .doc-type-card-label input[type="radio"]:checked + .doc-type-card-box {
    background: rgba(4, 120, 87, 0.2);
    border-color: #10b981;
    color: #6ee7b7;
}
</style>

    <!-- 1. Hero Security & Privacy Banner -->
    <div class="doc-hero-banner">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill bg-white bg-opacity-20 text-white fs-8 fw-semibold mb-2">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Enkripsi Aman &amp; Terlindungi</span>
                </div>
                <h2 class="fw-bold fs-4 mb-1.5 text-white">Verifikasi Identitas Penyewa Kendaraan</h2>
                <p class="mb-0 text-white-50 fs-7" style="line-height: 1.5;">
                    Unggah dokumen KTP atau SIM Anda untuk mempercepat reservasi rental motor &amp; mobil di Tegal. Dokumen Anda disimpan secara aman dengan enkripsi AES-256 dan hanya dapat diakses saat pesanan aktif.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-inline-flex flex-column gap-1.5 text-start bg-white bg-opacity-10 p-2.5 rounded-3 border border-white border-opacity-20 fs-8">
                    <div><i class="fa-solid fa-check-double text-warning me-1.5"></i> 1x Unggah untuk Semua Rental</div>
                    <div><i class="fa-solid fa-lock text-warning me-1.5"></i> Privasi Data Terjamin</div>
                    <div><i class="fa-solid fa-bolt text-warning me-1.5"></i> Cek-in Kendaraan Tanpa Antre</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Ringkasan Status Dokumen (4 Quick Stat Cards) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="doc-stat-card">
                <div class="doc-stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <div class="fs-8 text-muted fw-semibold text-uppercase">Total Dokumen</div>
                    <div class="fs-4 fw-bold text-dark">{{ $counts['total'] ?? $documents->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="doc-stat-card">
                <div class="doc-stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="fs-8 text-muted fw-semibold text-uppercase">Terverifikasi</div>
                    <div class="fs-4 fw-bold text-success">{{ $counts['approved'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="doc-stat-card">
                <div class="doc-stat-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <div class="fs-8 text-muted fw-semibold text-uppercase">Dalam Proses</div>
                    <div class="fs-4 fw-bold text-warning">{{ $counts['pending'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="doc-stat-card">
                <div class="doc-stat-icon bg-danger-subtle text-danger">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="fs-8 text-muted fw-semibold text-uppercase">Ditolak</div>
                    <div class="fs-4 fw-bold text-danger">{{ $counts['rejected'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Form Unggah & Daftar Dokumen Grid -->
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Form Unggah Dokumen -->
        <div class="col-lg-5">
            <x-content-card title="Unggah Dokumen Baru">
                <form method="POST" action="{{ route('consumer.renter-documents.store') }}" enctype="multipart/form-data" id="renterDocForm">
                    @csrf

                    <!-- 1. Pilihan Jenis Dokumen -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7 mb-1.5">
                            Jenis Dokumen Identitas <span class="text-danger">*</span>
                        </label>
                        <div class="doc-type-radio-group">
                            <label class="doc-type-card-label">
                                <input type="radio" name="document_type" value="ktp" {{ old('document_type', 'ktp') === 'ktp' ? 'checked' : '' }} required>
                                <div class="doc-type-card-box">
                                    <i class="fa-solid fa-id-card"></i>
                                    <span class="title">KTP</span>
                                    <span class="subtitle">Wajib Sewa</span>
                                </div>
                            </label>

                            <label class="doc-type-card-label">
                                <input type="radio" name="document_type" value="sim_c" {{ old('document_type') === 'sim_c' ? 'checked' : '' }}>
                                <div class="doc-type-card-box">
                                    <i class="fa-solid fa-motorcycle"></i>
                                    <span class="title">SIM C</span>
                                    <span class="subtitle">Sewa Motor</span>
                                </div>
                            </label>

                            <label class="doc-type-card-label">
                                <input type="radio" name="document_type" value="sim_a" {{ old('document_type') === 'sim_a' ? 'checked' : '' }}>
                                <div class="doc-type-card-box">
                                    <i class="fa-solid fa-car-side"></i>
                                    <span class="title">SIM A</span>
                                    <span class="subtitle">Sewa Mobil</span>
                                </div>
                            </label>

                            <label class="doc-type-card-label">
                                <input type="radio" name="document_type" value="passport" {{ old('document_type') === 'passport' ? 'checked' : '' }}>
                                <div class="doc-type-card-box">
                                    <i class="fa-solid fa-passport"></i>
                                    <span class="title">Paspor</span>
                                    <span class="subtitle">Turis WNA</span>
                                </div>
                            </label>
                        </div>
                        @error('document_type')
                            <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 2. Nomor Dokumen -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7 mb-1" id="docNumberLabel">
                            Nomor Dokumen (NIK / No. SIM / No. Paspor) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-hashtag"></i></span>
                            <input type="text" name="document_number" value="{{ old('document_number') }}"
                                   class="form-control @error('document_number') is-invalid @enderror"
                                   placeholder="Contoh: 3328xxxxxxxxxxxx" required>
                        </div>
                        <div class="form-text fs-8 text-muted">Nomor dokumen Anda dienkripsi dan dirahasiakan.</div>
                        @error('document_number')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 3. Masa Berlaku Dokumen (Opsional) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">
                            Masa Berlaku Dokumen <span class="text-muted fw-normal fs-8">(Opsional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-calendar"></i></span>
                            <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                                   class="form-control @error('expires_at') is-invalid @enderror">
                        </div>
                        <div class="form-text fs-8 text-muted">Biarkan kosong jika berlaku seumur hidup (KTP Elektronik).</div>
                        @error('expires_at')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 4. Berkas File Upload -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">
                            Foto / Scan Dokumen Asli <span class="text-danger">*</span>
                        </label>
                        <div class="file-dropzone-box" onclick="document.getElementById('docFileInput').click()" id="dropzoneArea">
                            <input type="file" name="file" id="docFileInput" class="d-none" accept=".jpg,.jpeg,.png,.pdf" required onchange="handleFileSelected(this)">
                            <div id="dropzonePrompt">
                                <div class="rounded-circle bg-white shadow-xs d-inline-flex align-items-center justify-content-center text-success mb-2" style="width: 48px; height: 48px; font-size: 22px;">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <div class="fw-bold text-dark fs-7">Pilih Berkas atau Tarik ke Sini</div>
                                <div class="text-muted fs-8 mt-0.5">Format: JPG, PNG, atau PDF (Maks. 8 MB)</div>
                            </div>
                            <div id="dropzonePreview" class="d-none">
                                <div class="d-flex align-items-center justify-content-center gap-2 text-success fw-bold fs-7">
                                    <i class="fa-solid fa-file-check fs-5"></i>
                                    <span id="fileNameDisplay">nama-file.jpg</span>
                                </div>
                                <small class="text-muted d-block mt-1" id="fileSizeDisplay">1.2 MB</small>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 mt-1 fs-8 text-decoration-none" onclick="event.stopPropagation(); resetFileInput();">
                                    <i class="fa-solid fa-xmark me-1"></i> Ganti Berkas
                                </button>
                            </div>
                        </div>
                        @error('file')
                            <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" class="btn btn-lokantara fw-bold rounded-pill w-100 py-2.5 shadow-sm">
                        <i class="fa-solid fa-upload me-1.5"></i> Unggah Dokumen Identitas
                    </button>
                </form>
            </x-content-card>
        </div>

        <!-- Kolom Kanan: Daftar Dokumen Tersimpan -->
        <div class="col-lg-7">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="fs-6 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-folder-open text-primary me-1.5"></i> Dokumen Tersimpan ({{ $documents->count() }})
                </h3>
                <span class="fs-8 text-muted">Diverifikasi untuk Keperluan Sewa</span>
            </div>

            @if($documents->isEmpty())
                <div class="content-card text-center py-5">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                        <i class="fa-regular fa-folder-open"></i>
                    </div>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Belum Ada Dokumen Identitas</h4>
                    <p class="text-muted fs-7 mb-3 mx-auto" style="max-width: 380px;">
                        Silakan unggah KTP dan SIM Anda melalui formulir di samping agar pesanan rental motor atau mobil Anda dapat diproses secara instan.
                    </p>
                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="document.getElementById('renterDocForm').scrollIntoView({behavior: 'smooth'})">
                        <i class="fa-solid fa-plus me-1"></i> Unggah Dokumen Sekarang
                    </button>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($documents as $doc)
                        @php
                            $docMeta = match($doc->document_type) {
                                'ktp' => ['label' => 'KTP (Kartu Tanda Penduduk)', 'icon' => 'fa-solid fa-id-card', 'color' => '#047857', 'bg' => '#ecfdf5'],
                                'sim_a' => ['label' => 'SIM A (Mobil Pribadi)', 'icon' => 'fa-solid fa-car-side', 'color' => '#1e40af', 'bg' => '#eff6ff'],
                                'sim_c' => ['label' => 'SIM C (Sepeda Motor)', 'icon' => 'fa-solid fa-motorcycle', 'color' => '#b45309', 'bg' => '#fffbeb'],
                                'passport' => ['label' => 'Paspor Internasional', 'icon' => 'fa-solid fa-passport', 'color' => '#6b21a8', 'bg' => '#faf5ff'],
                                default => ['label' => strtoupper($doc->document_type), 'icon' => 'fa-solid fa-file', 'color' => '#334155', 'bg' => '#f1f5f9'],
                            };
                            $statusVal = $doc->status->value ?? 'pending';
                        @endphp
                        <div class="saved-doc-card">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 pb-3 border-bottom mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="doc-icon-badge" style="background-color: {{ $docMeta['bg'] }}; color: {{ $docMeta['color'] }};">
                                        <i class="{{ $docMeta['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-7">{{ $docMeta['label'] }}</div>
                                        <small class="text-muted fs-8">
                                            Diupload: {{ $doc->created_at->format('d M Y, H:i') }}
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <x-status-badge :status="$statusVal" />
                                </div>
                            </div>

                            <!-- Document Content Info -->
                            <div class="row g-2 fs-8 mb-3">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Nomor Identitas Terdaftar:</span>
                                    <strong class="font-monospace text-dark fs-7">
                                        {{ $doc->document_number ? Str::mask($doc->document_number, '*', 4, -4) : 'Terenkripsi' }}
                                    </strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Masa Berlaku:</span>
                                    <span class="text-dark fw-semibold">
                                        @if($doc->expires_at)
                                            <i class="fa-regular fa-calendar-check text-success me-1"></i>{{ $doc->expires_at->format('d M Y') }}
                                        @else
                                            <i class="fa-solid fa-infinity text-muted me-1"></i> Seumur Hidup
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if($doc->rejection_reason && $statusVal === 'rejected')
                                <div class="p-2.5 rounded-3 bg-danger-subtle border border-danger-subtle text-danger-emphasis fs-8 mb-3">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                    <strong>Alasan Penolakan:</strong> {{ $doc->rejection_reason }}
                                </div>
                            @endif

                            @if($statusVal === 'approved')
                                <div class="p-2 rounded-3 bg-success-subtle border border-success-subtle text-success-emphasis fs-8 mb-3 d-flex align-items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Dokumen ini valid dan aktif untuk digunakan saat melakukan sewa armada di seluruh mitra rental terdaftar.</span>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <div class="text-muted fs-8">
                                    @if($doc->media)
                                        <i class="fa-solid fa-paperclip me-1"></i>{{ Str::limit($doc->media->original_name, 24) }}
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('consumer.renter-documents.download', $doc) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-dark fs-8 fw-semibold" title="Unduh Berkas Saya">
                                        <i class="fa-solid fa-download me-1 text-primary"></i> Unduh
                                    </a>

                                    @if(in_array($statusVal, ['pending', 'rejected']))
                                        <form method="POST" action="{{ route('consumer.renter-documents.destroy', $doc) }}" class="d-inline m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fs-8" title="Hapus Dokumen">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- 4. Panduan & Ketentuan Verifikasi Dokumen Rental -->
    <x-content-card title="Panduan Persyaratan Dokumen Rental">
        <div class="row g-3 fs-8">
            <div class="col-md-4">
                <div class="p-3 rounded-3 bg-light border h-100">
                    <div class="fw-bold text-dark fs-7 mb-1.5">
                        <i class="fa-solid fa-motorcycle text-warning me-1.5"></i> Rental Sepeda Motor
                    </div>
                    <p class="text-muted mb-0" style="line-height: 1.5;">
                        Wajib melampirkan <strong>KTP Asli</strong> dan <strong>SIM C Aktif</strong>. Pastikan foto terlihat jelas, tidak buram, dan tanpa pantulan cahaya yang menutupi teks.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 bg-light border h-100">
                    <div class="fw-bold text-dark fs-7 mb-1.5">
                        <i class="fa-solid fa-car text-primary me-1.5"></i> Rental Mobil Lepas Kunci
                    </div>
                    <p class="text-muted mb-0" style="line-height: 1.5;">
                        Wajib melampirkan <strong>KTP Asli</strong> dan <strong>SIM A Aktif</strong>. Mitra rental berhak meminta verifikasi fisik saat serah terima unit kendaraan.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 bg-light border h-100">
                    <div class="fw-bold text-dark fs-7 mb-1.5">
                        <i class="fa-solid fa-lock text-success me-1.5"></i> Keamanan Privasi Data
                    </div>
                    <p class="text-muted mb-0" style="line-height: 1.5;">
                        Data identitas Anda tidak pernah disebarluaskan ke pihak ketiga selain mitra rental yang sedang Anda sewa selama durasi pemesanan aktif.
                    </p>
                </div>
            </div>
        </div>
    </x-content-card>

    <script>
        function handleFileSelected(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('fileNameDisplay').textContent = file.name;
                
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                document.getElementById('fileSizeDisplay').textContent = sizeInMB + ' MB';
                
                document.getElementById('dropzonePrompt').classList.add('d-none');
                document.getElementById('dropzonePreview').classList.remove('d-none');
            }
        }

        function resetFileInput() {
            const input = document.getElementById('docFileInput');
            input.value = '';
            document.getElementById('dropzonePrompt').classList.remove('d-none');
            document.getElementById('dropzonePreview').classList.add('d-none');
        }

        // Drag and drop interaction
        const dropArea = document.getElementById('dropzoneArea');
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.remove('dragover');
            }, false);
        });

        dropArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                const input = document.getElementById('docFileInput');
                input.files = files;
                handleFileSelected(input);
            }
        }, false);
    </script>
@endsection
