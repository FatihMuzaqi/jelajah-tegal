@extends('layouts.public')

@section('title', 'Pendaftaran Mitra Baru — Jelajah Tegal')
@section('meta-description', 'Daftarkan bisnis wisata, penginapan, kuliner, rental, atau event Anda secara mandiri di platform pariwisata resmi Jelajah Tegal.')

@push('head-extra')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .mitra-reg-container {
        max-width: 960px;
        margin: 0 auto;
        padding: 36px 16px 80px;
    }
    .mitra-reg-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 10px 40px -10px rgba(15, 23, 42, 0.07);
        overflow: hidden;
    }
    
    /* Stepper Navigation */
    .stepper-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 24px 20px;
    }
    .stepper-track {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        gap: 8px;
    }
    .stepper-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
        position: relative;
        z-index: 2;
        cursor: pointer;
        user-select: none;
    }
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        color: #64748b;
        font-weight: 800;
        font-size: 14px;
        display: grid;
        place-items: center;
        transition: all 0.25s ease;
        margin-bottom: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .stepper-step.active .step-circle {
        background: #047857;
        border-color: #047857;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(4, 120, 87, 0.18);
        transform: scale(1.08);
    }
    .stepper-step.completed .step-circle {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
    }
    .step-title {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        line-height: 1.25;
        transition: color 0.2s ease;
    }
    .stepper-step.active .step-title {
        color: #047857;
        font-weight: 800;
    }
    .stepper-step.completed .step-title {
        color: #0f172a;
    }
    
    .progress-bar-container {
        height: 6px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
        margin-top: 18px;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #047857 0%, #10b981 100%);
        width: 16.66%;
        transition: width 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Form Panels */
    .step-pane {
        display: none;
        padding: 32px 28px;
        animation: fadeInPane 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .step-pane.active {
        display: block;
    }
    @keyframes fadeInPane {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-section-title {
        font-size: 19px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-section-desc {
        font-size: 13.5px;
        color: #64748b;
        margin-bottom: 24px;
    }

    .custom-file-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 24px 16px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .custom-file-dropzone:hover {
        border-color: #047857;
        background: #f0fdf4;
    }

    #leaflet-pin-map {
        height: 320px;
        width: 100%;
        border-radius: 16px;
        border: 1px solid #cbd5e1;
        z-index: 1;
    }

    .preview-thumb-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 10px;
        margin-top: 12px;
    }
    .preview-thumb-item {
        position: relative;
        height: 80px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .preview-thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (max-width: 767.98px) {
        .stepper-header {
            padding: 18px 12px;
        }
        .step-title {
            display: none;
        }
        .step-circle {
            width: 34px;
            height: 34px;
            font-size: 13px;
        }
        .step-pane {
            padding: 24px 18px;
        }
        .mitra-reg-container {
            padding: 20px 12px 60px;
        }
    }
</style>
@endpush

@section('content')
<div class="mitra-reg-container">
    <!-- Breadcrumb & Header Hero -->
    <div class="mb-4 text-center">
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold fs-8 mb-2">
            <i class="fa-solid fa-handshake me-1"></i> Program Kemitraan Resmi
        </span>
        <h1 class="fw-bold fs-3 text-dark mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">Pendaftaran Mitra Baru Jelajah Tegal</h1>
        <p class="text-muted small mx-auto" style="max-width: 620px; font-size: 13.5px;">
            Bergabunglah bersama ratusan pengelola destinasi, penginapan, kuliner, rental, dan penyelenggara event untuk memperluas jangkauan wisatawan di Kabupaten & Kota Tegal.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 p-3 mb-4 shadow-sm">
            <div class="d-flex align-items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation text-danger mt-1 fs-6 flex-shrink-0"></i>
                <div>
                    <strong class="d-block text-dark fs-7 mb-1">Terdapat data yang belum lengkap atau tidak valid:</strong>
                    <ul class="mb-0 ps-3 small text-danger" style="font-size: 12.5px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="mitra-reg-card">
        <!-- 1. Stepper Header -->
        <div class="stepper-header">
            <div class="stepper-track">
                <div class="stepper-step active" data-step="1" onclick="jumpToStep(1)">
                    <div class="step-circle"><i class="fa-solid fa-user"></i></div>
                    <div class="step-title">1. Akun & Pemilik</div>
                </div>
                <div class="stepper-step" data-step="2" onclick="jumpToStep(2)">
                    <div class="step-circle"><i class="fa-solid fa-store"></i></div>
                    <div class="step-title">2. Profil Usaha</div>
                </div>
                <div class="stepper-step" data-step="3" onclick="jumpToStep(3)">
                    <div class="step-circle"><i class="fa-solid fa-certificate"></i></div>
                    <div class="step-title">3. Legalitas</div>
                </div>
                <div class="stepper-step" data-step="4" onclick="jumpToStep(4)">
                    <div class="step-circle"><i class="fa-solid fa-images"></i></div>
                    <div class="step-title">4. Dokumen & Galeri</div>
                </div>
                <div class="stepper-step" data-step="5" onclick="jumpToStep(5)">
                    <div class="step-circle"><i class="fa-solid fa-credit-card"></i></div>
                    <div class="step-title">5. Rekening Bank</div>
                </div>
                <div class="stepper-step" data-step="6" onclick="jumpToStep(6)">
                    <div class="step-circle"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="step-title">6. Konfirmasi</div>
                </div>
            </div>
            
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="stepperProgressBar"></div>
            </div>
        </div>

        <!-- 2. Form Multi-Step Body -->
        <form method="POST" action="{{ route('mitra.register.store') }}" enctype="multipart/form-data" id="mitraRegisterForm" class="needs-validation" novalidate>
            @csrf

            <!-- STEP 1: DATA AKUN & PENANGGUNG JAWAB -->
            <div class="step-pane active" id="pane-step-1">
                <div class="form-section-title">
                    <i class="fa-solid fa-id-card text-success"></i>
                    <span>Langkah 1: Data Akun & Penanggung Jawab</span>
                </div>
                <p class="form-section-desc">Identitas pemilik atau penanggung jawab resmi yang akan mengelola akun kemitraan di platform Jelajah Tegal.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">Nama Lengkap Penanggung Jawab <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-regular fa-user"></i></span>
                            <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ old('owner_name') }}" required placeholder="Nama lengkap sesuai KTP">
                        </div>
                        <div class="invalid-feedback">Nama penanggung jawab wajib diisi.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-address-card"></i></span>
                            <input type="text" name="owner_nik" id="owner_nik" class="form-control" value="{{ old('owner_nik') }}" required minlength="16" maxlength="16" pattern="[0-9]{16}" placeholder="16 digit nomor KTP">
                        </div>
                        <small class="text-muted fs-8 d-block mt-0.5">Digunakan untuk verifikasi keaslian pengelola.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-brands fa-whatsapp text-success"></i></span>
                            <input type="tel" name="owner_phone" id="owner_phone" class="form-control" value="{{ old('owner_phone') }}" required placeholder="Contoh: 081234567890">
                        </div>
                        <small class="text-muted fs-8 d-block mt-0.5">Untuk koordinasi kurasi & informasi reservasi/tiket.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">Alamat Email Aktif <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" name="owner_email" id="owner_email" class="form-control" value="{{ old('owner_email') }}" required placeholder="email@contoh.com">
                        </div>
                        <small class="text-muted fs-8 d-block mt-0.5">Akan digunakan sebagai email login akun mitra Anda.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">Kata Sandi Akun <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" id="mitra_pwd" class="form-control" required minlength="8" placeholder="Minimal 8 karakter">
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleMitraPwd('mitra_pwd', this)"><i class="fa-regular fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">Ulangi Kata Sandi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock-open"></i></span>
                            <input type="password" name="password_confirmation" id="mitra_pwd_conf" class="form-control" required minlength="8" placeholder="Ulangi kata sandi">
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleMitraPwd('mitra_pwd_conf', this)"><i class="fa-regular fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7">Upload Foto KTP Penanggung Jawab <span class="text-danger">*</span></label>
                        <div class="custom-file-dropzone" onclick="document.getElementById('ktp_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up text-success fs-3 mb-2"></i>
                            <div class="fw-bold text-dark fs-7">Klik untuk memilih foto KTP atau seret ke sini</div>
                            <div class="text-muted fs-8">Format JPG, PNG, atau PDF. Maksimal 3 MB. Pastikan data KTP terbaca jelas.</div>
                            <input type="file" name="ktp_file" id="ktp_file" class="d-none" accept="image/*,application/pdf" required onchange="previewKtp(this)">
                        </div>
                        <div id="ktpPreviewBox" class="mt-2.5 p-2 rounded-3 border bg-light d-none align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-file-shield text-success fs-5"></i>
                                <span class="fs-8 fw-semibold text-dark" id="ktpFileName">ktp.jpg</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fs-8" onclick="removeKtp()">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: DATA USAHA / MITRA & LOKASI -->
            <div class="step-pane" id="pane-step-2">
                <div class="form-section-title">
                    <i class="fa-solid fa-store text-success"></i>
                    <span>Langkah 2: Data Usaha & Titik Lokasi</span>
                </div>
                <p class="form-section-desc">Informasi profil unit usaha, kategori layanan wisata, dan koordinat peta tempat usaha Anda.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">Nama Usaha / Merk Dagang <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" id="display_name" class="form-control" value="{{ old('display_name') }}" required placeholder="Contoh: Guci Forest Glamping, Cafe Teras Slawi">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">Nama Legal Badan Usaha <span class="text-muted fw-normal fs-8">(Opsional, jika PT/CV/Koperasi)</span></label>
                        <input type="text" name="legal_name" id="legal_name" class="form-control" value="{{ old('legal_name') }}" placeholder="Contoh: PT Guci Alam Lestari">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark fs-7">Jenis Layanan <span class="text-danger">*</span></label>
                        <select name="service_type_id" id="service_type_id" class="form-select" required onchange="handleServiceTypeChange()">
                            <option value="" disabled selected>-- Pilih Jenis Layanan --</option>
                            @foreach ($serviceTypes as $st)
                                <option value="{{ $st->id }}" data-code="{{ $st->code }}" {{ old('service_type_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark fs-7">Kategori Spesifik <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">-- Pilih Jenis Layanan Dahulu --</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark fs-7">Tahun Berdiri Usaha</label>
                        <input type="number" name="founded_year" id="founded_year" class="form-control" value="{{ old('founded_year', date('Y')) }}" min="1900" max="{{ date('Y') }}" placeholder="Tahun, misal: 2018">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">Wilayah / Kecamatan di Tegal <span class="text-danger">*</span></label>
                        <select name="region_id" id="region_id" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Wilayah / Kecamatan --</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">Deskripsi Singkat Usaha <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="2" required placeholder="Ceritakan konsep tempat usaha, keunggulan fasilitas, dan keunikan produk Anda...">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7">Alamat Lengkap Tempat Usaha <span class="text-danger">*</span></label>
                        <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}" required placeholder="Jl. Raya / Dusun, RT/RW, Desa/Kelurahan, Kode Pos">
                    </div>

                    <!-- Interactive Leaflet Map Pinpoint -->
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1.5">
                            <label class="form-label fw-bold text-dark fs-7 mb-0">
                                <i class="fa-solid fa-map-pin text-danger me-1"></i> Titik Lokasi Peta (Maps Pin)
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fs-8 fw-semibold" onclick="locateUserPosition()">
                                <i class="fa-solid fa-crosshairs me-1"></i> Gunakan Lokasi GPS Saya
                            </button>
                        </div>
                        <div id="leaflet-pin-map"></div>
                        <div class="d-flex align-items-center gap-3 mt-2 flex-wrap">
                            <div class="d-flex align-items-center gap-1.5 fs-8 text-muted">
                                <span>Latitude:</span>
                                <input type="text" name="latitude" id="map_lat" class="form-control form-control-sm font-mono py-0.5 px-2 text-center" style="width: 110px;" value="{{ old('latitude', '-6.8797000') }}" readonly>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 fs-8 text-muted">
                                <span>Longitude:</span>
                                <input type="text" name="longitude" id="map_lng" class="form-control form-control-sm font-mono py-0.5 px-2 text-center" style="width: 110px;" value="{{ old('longitude', '109.1256000') }}" readonly>
                            </div>
                            <small class="text-muted fs-8"><i class="fa-solid fa-circle-info me-1"></i> Geser penanda merah atau klik peta untuk menentukan titik koordinat presisi.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: LEGALITAS USAHA (BERTINGKAT) -->
            <div class="step-pane" id="pane-step-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-stamp text-success"></i>
                    <span>Langkah 3: Legalitas & Kredibilitas Usaha</span>
                </div>
                <p class="form-section-desc">Kelengkapan legalitas mempercepat kurasi dan memberikan lencana resmi <strong>"Terverifikasi"</strong> untuk meningkatkan kepercayaan wisatawan.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <label class="form-label fw-bold text-dark fs-7">
                                NIB (Nomor Induk Berusaha)
                                <span class="badge bg-warning-subtle text-warning-emphasis border rounded-pill px-2 py-0.5 fs-8 ms-1">Nilai Tambah Badge</span>
                            </label>
                            <input type="text" name="nib" id="nib" class="form-control bg-white" value="{{ old('nib') }}" placeholder="13 digit nomor NIB OSS (jika ada)">
                            <small class="text-muted fs-8 d-block mt-1">Opsional untuk UMKM kecil. Wajib jika ingin langsung berstatus Terverifikasi.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <label class="form-label fw-bold text-dark fs-7">NPWP Usaha / Pribadi <span class="text-muted fs-8">(Opsional)</span></label>
                            <input type="text" name="npwp" id="npwp" class="form-control bg-white" value="{{ old('npwp') }}" placeholder="15-16 digit nomor NPWP">
                            <small class="text-muted fs-8 d-block mt-1">Dapat diisi NPWP badan atau NPWP penanggung jawab.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">
                            Dokumen Izin Usaha Spesifik
                            <span class="text-muted fs-8">(Izin Pariwisata / Sertifikat Halal / SIUP)</span>
                        </label>
                        <div class="custom-file-dropzone py-3" onclick="document.getElementById('business_license_file').click()">
                            <i class="fa-solid fa-file-invoice text-success fs-4 mb-1"></i>
                            <div class="fw-bold text-dark fs-8">Upload Dokumen Izin Usaha</div>
                            <div class="text-muted fs-8">PDF atau Gambar (Maks 5 MB)</div>
                            <input type="file" name="business_license_file" id="business_license_file" class="d-none" accept="image/*,application/pdf" onchange="displayGenericFileName(this, 'licenseFileName')">
                        </div>
                        <div id="licenseFileName" class="small text-success mt-1 fw-semibold d-none"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-7">
                            Surat Izin Tempat Usaha (SITU) / Keterangan Domisili
                            <span class="text-muted fs-8">(Opsional)</span>
                        </label>
                        <div class="custom-file-dropzone py-3" onclick="document.getElementById('situ_file').click()">
                            <i class="fa-solid fa-building-circle-check text-success fs-4 mb-1"></i>
                            <div class="fw-bold text-dark fs-8">Upload Surat SITU / Domisili Usaha</div>
                            <div class="text-muted fs-8">PDF atau Gambar (Maks 5 MB)</div>
                            <input type="file" name="situ_file" id="situ_file" class="d-none" accept="image/*,application/pdf" onchange="displayGenericFileName(this, 'situFileName')">
                        </div>
                        <div id="situFileName" class="small text-success mt-1 fw-semibold d-none"></div>
                    </div>
                </div>
            </div>

            <!-- STEP 4: DOKUMEN PENDUKUNG & GALERI -->
            <div class="step-pane" id="pane-step-4">
                <div class="form-section-title">
                    <i class="fa-solid fa-camera text-success"></i>
                    <span>Langkah 4: Foto Lokasi & Produk Usaha</span>
                </div>
                <p class="form-section-desc">Foto tempat fisik dan produk digunakan tim verifikator untuk memastikan keberadaan usaha nyata di lapangan.</p>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7">
                            Foto Lokasi Usaha Tampak Depan & Fisik <span class="text-danger">* (Minimal 2 - 3 Foto)</span>
                        </label>
                        <div class="custom-file-dropzone" onclick="document.getElementById('location_photos').click()">
                            <i class="fa-solid fa-images text-success fs-3 mb-2"></i>
                            <div class="fw-bold text-dark fs-7">Klik untuk memilih 2 - 6 foto lokasi usaha tampak depan</div>
                            <div class="text-muted fs-8">Format JPG, PNG, WEBP. Maks 3 MB per foto. Perlihatkan plang nama atau tampak luar.</div>
                            <input type="file" name="location_photos[]" id="location_photos" class="d-none" accept="image/*" multiple required onchange="previewMultiImages(this, 'locPreviewGrid')">
                        </div>
                        <div class="preview-thumb-grid" id="locPreviewGrid"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark fs-7">
                            Foto Produk / Fasilitas Layanan yang Ditawarkan <span class="text-muted fs-8">(Opsional, Disarankan)</span>
                        </label>
                        <div class="custom-file-dropzone py-3" onclick="document.getElementById('product_photos').click()">
                            <i class="fa-solid fa-layer-group text-success fs-4 mb-1"></i>
                            <div class="fw-bold text-dark fs-8">Klik untuk memilih foto produk, kamar, menu, atau armada</div>
                            <div class="text-muted fs-8">Bisa upload hingga 8 foto.</div>
                            <input type="file" name="product_photos[]" id="product_photos" class="d-none" accept="image/*" multiple onchange="previewMultiImages(this, 'prodPreviewGrid')">
                        </div>
                        <div class="preview-thumb-grid" id="prodPreviewGrid"></div>
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded-3 border bg-light">
                            <label class="form-label fw-bold text-dark fs-7">
                                Dokumen Kepemilikan Aset Usaha
                                <span class="text-muted fs-8">(Khusus Rental: STNK/BPKB Armada; Khusus Penginapan: Bukti Hak Milik / Sewa)</span>
                            </label>
                            <input type="file" name="asset_ownership_file" id="asset_ownership_file" class="form-control" accept="image/*,application/pdf">
                            <small class="text-muted fs-8 d-block mt-1">Diperlukan untuk unit bisnis rental kendaraan atau penginapan guna mencegah sengketa properti.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 5: DATA REKENING & PEMBAYARAN -->
            <div class="step-pane" id="pane-step-5">
                <div class="form-section-title">
                    <i class="fa-solid fa-wallet text-success"></i>
                    <span>Langkah 5: Rekening Bank Penarikan Saldo</span>
                </div>
                <p class="form-section-desc">Rekening bank ini digunakan untuk penyaluran dana hasil penjualan tiket, reservasi, dan transaksi pengunjung.</p>

                <div class="alert alert-info border d-flex align-items-start gap-2 mb-3 py-2.5 px-3 rounded-3" style="font-size: 12.5px;">
                    <i class="fa-solid fa-shield-halved text-primary mt-0.5 flex-shrink-0"></i>
                    <div>
                        <strong>Penting untuk Keamanan Transaksi:</strong> Nama pada pemilik rekening bank sebaiknya sama dengan nama penanggung jawab atau badan usaha untuk mencegah penolakan penarikan dana / fraud.
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark fs-7">Nama Bank <span class="text-danger">*</span></label>
                        <select name="bank_code" id="bank_code" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Bank --</option>
                            @foreach ($banks as $code => $name)
                                <option value="{{ $code }}" {{ old('bank_code') == $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark fs-7">Nomor Rekening <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" id="account_number" class="form-control font-mono" value="{{ old('account_number') }}" required placeholder="Contoh: 1234567890">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark fs-7">Nama Pemilik Rekening <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" id="account_name" class="form-control" value="{{ old('account_name') }}" required placeholder="Nama tertera di buku tabungan">
                    </div>
                </div>
            </div>

            <!-- STEP 6: PERSETUJUAN & RINGKASAN DATA -->
            <div class="step-pane" id="pane-step-6">
                <div class="form-section-title">
                    <i class="fa-solid fa-clipboard-check text-success"></i>
                    <span>Langkah 6: Persetujuan & Konfirmasi Pendaftaran</span>
                </div>
                <p class="form-section-desc">Mohon tinjau kembali ringkasan data yang telah diisi sebelum mengirimkan pendaftaran ke tim kurasi admin.</p>

                <!-- Ringkasan Singkat Review Box -->
                <div class="p-3.5 rounded-4 border mb-4" style="background: #f8fafc;">
                    <h6 class="fw-bold text-dark fs-7 mb-2 border-bottom pb-1.5"><i class="fa-solid fa-list-check me-1 text-primary"></i> Ringkasan Pendaftaran:</h6>
                    <div class="row g-2" style="font-size: 13px;">
                        <div class="col-sm-6">
                            <span class="text-muted d-block fs-8">Penanggung Jawab:</span>
                            <strong class="text-dark" id="summary_owner">—</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block fs-8">Email & WhatsApp:</span>
                            <span class="text-dark" id="summary_contact">—</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block fs-8">Nama Usaha:</span>
                            <strong class="text-success" id="summary_business">—</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block fs-8">Jenis Layanan:</span>
                            <span class="badge bg-light text-dark border" id="summary_service">—</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block fs-8">Alamat Tempat Usaha:</span>
                            <span class="text-dark" id="summary_address">—</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block fs-8">Rekening Pencairan:</span>
                            <span class="font-mono text-dark" id="summary_bank">—</span>
                        </div>
                    </div>
                </div>

                <!-- 3 Checkbox Persetujuan -->
                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="agree_truth" id="agree_truth" required {{ old('agree_truth') ? 'checked' : '' }}>
                        <label class="form-check-label text-dark fs-7" for="agree_truth">
                            <strong>Pernyataan Kebenaran Data:</strong> Saya menyatakan dengan sungguh-sungguh bahwa seluruh informasi dan dokumen yang diunggah adalah benar, sah, milik usaha kami, dan dapat dipertanggungjawabkan secara hukum.
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="agree_terms" id="agree_terms" required {{ old('agree_terms') ? 'checked' : '' }}>
                        <label class="form-check-label text-dark fs-7" for="agree_terms">
                            <strong>Syarat & Ketentuan:</strong> Saya menyetujui <a href="{{ route('public.terms') }}" target="_blank" class="text-success fw-bold text-decoration-none">Syarat & Ketentuan Kemitraan Jelajah Tegal</a> serta bersedia mematuhi standar pelayanan pariwisata daerah.
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="agree_commission" id="agree_commission" required {{ old('agree_commission') ? 'checked' : '' }}>
                        <label class="form-check-label text-dark fs-7" for="agree_commission">
                            <strong>Tata Kelola & Biaya Transaksi:</strong> Saya menyetujui ketentuan biaya pemrosesan gerbang pembayaran (payment gateway) dan skema operasional platform yang transparan.
                        </label>
                    </div>
                </div>
            </div>

            <!-- 3. Form Footer Controls (Next / Prev / Submit) -->
            <div class="p-3.5 bg-light border-top d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-light border rounded-pill px-4 fw-bold fs-7" id="btnPrevStep" onclick="prevStep()" style="display: none;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Sebelumnya
                </button>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-lokantara rounded-pill px-4 fw-bold fs-7" id="btnNextStep" onclick="nextStep()">
                        Lanjut ke Langkah Berikutnya <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold fs-7 shadow-sm" id="btnSubmitForm" style="display: none;">
                        <i class="fa-solid fa-paper-plane me-1.5"></i> Kirim Pendaftaran Mitra
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Master data categories per service type (JSON)
    const rawServiceTypes = @json($serviceTypes);
    let currentStep = 1;
    const totalSteps = 6;

    // Leaflet map & marker instance
    let map = null;
    let marker = null;

    document.addEventListener('DOMContentLoaded', () => {
        initCategoryDropdown();
        updateStepperUI();
    });

    function initCategoryDropdown() {
        const serviceSelect = document.getElementById('service_type_id');
        if (serviceSelect && serviceSelect.value) {
            handleServiceTypeChange();
        }
    }

    function handleServiceTypeChange() {
        const serviceSelect = document.getElementById('service_type_id');
        const categorySelect = document.getElementById('category_id');
        const selectedId = parseInt(serviceSelect.value, 10);
        
        categorySelect.innerHTML = '<option value="" disabled selected>-- Pilih Kategori Spesifik --</option>';

        const found = rawServiceTypes.find(st => st.id === selectedId);
        if (found && found.categories && found.categories.length > 0) {
            found.categories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.id;
                opt.textContent = cat.name;
                categorySelect.appendChild(opt);
            });
            categorySelect.disabled = false;
        } else {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Umum / ' + (found ? found.name : 'Layanan');
            categorySelect.appendChild(opt);
            categorySelect.disabled = false;
        }
    }

    function updateStepperUI() {
        // Update Step Panes
        for (let i = 1; i <= totalSteps; i++) {
            const pane = document.getElementById(`pane-step-${i}`);
            const stepItem = document.querySelector(`.stepper-step[data-step="${i}"]`);
            if (pane) {
                pane.classList.toggle('active', i === currentStep);
            }
            if (stepItem) {
                stepItem.classList.remove('active', 'completed');
                if (i === currentStep) {
                    stepItem.classList.add('active');
                } else if (i < currentStep) {
                    stepItem.classList.add('completed');
                }
            }
        }

        // Progress bar percentage
        const pct = Math.round((currentStep / totalSteps) * 100);
        const bar = document.getElementById('stepperProgressBar');
        if (bar) bar.style.width = pct + '%';

        // Prev & Next Buttons
        const prevBtn = document.getElementById('btnPrevStep');
        const nextBtn = document.getElementById('btnNextStep');
        const submitBtn = document.getElementById('btnSubmitForm');

        if (prevBtn) prevBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
        if (nextBtn) nextBtn.style.display = currentStep < totalSteps ? 'inline-flex' : 'none';
        if (submitBtn) submitBtn.style.display = currentStep === totalSteps ? 'inline-flex' : 'none';

        // Initialize Map on Step 2
        if (currentStep === 2) {
            setTimeout(initLeafletPinMap, 200);
        }

        // Build Summary on Step 6
        if (currentStep === 6) {
            populateSummary();
        }

        window.scrollTo({ top: 180, behavior: 'smooth' });
    }

    function validateCurrentStep() {
        const currentPane = document.getElementById(`pane-step-${currentStep}`);
        if (!currentPane) return true;

        const inputs = currentPane.querySelectorAll('input, select, textarea');
        let isValid = true;

        inputs.forEach(input => {
            if (input.required) {
                if (input.type === 'file') {
                    if (input.files.length === 0 && !input.dataset.existing) {
                        isValid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                } else if (input.type === 'checkbox') {
                    if (!input.checked) {
                        isValid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                } else {
                    if (!input.value || input.value.trim() === '') {
                        isValid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                }
            }
        });

        // Special check: password confirmation on step 1
        if (currentStep === 1) {
            const pwd = document.getElementById('mitra_pwd').value;
            const pwdConf = document.getElementById('mitra_pwd_conf').value;
            if (pwd && pwdConf && pwd !== pwdConf) {
                alert('Konfirmasi kata sandi tidak cocok. Mohon periksa kembali.');
                document.getElementById('mitra_pwd_conf').classList.add('is-invalid');
                return false;
            }
        }

        if (!isValid) {
            alert('Mohon lengkapi seluruh kolom wajib yang bertanda bintang (*) pada langkah ini sebelum melanjutkan.');
        }

        return isValid;
    }

    function nextStep() {
        if (!validateCurrentStep()) return;
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepperUI();
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            updateStepperUI();
        }
    }

    function jumpToStep(targetStep) {
        if (targetStep < currentStep) {
            currentStep = targetStep;
            updateStepperUI();
        } else if (targetStep > currentStep) {
            if (validateCurrentStep()) {
                currentStep = targetStep;
                updateStepperUI();
            }
        }
    }

    // Leaflet Pin Map Setup
    function initLeafletPinMap() {
        if (map) {
            map.invalidateSize();
            return;
        }

        const defaultLat = parseFloat(document.getElementById('map_lat').value) || -6.8797;
        const defaultLng = parseFloat(document.getElementById('map_lng').value) || 109.1256;

        map = L.map('leaflet-pin-map', {
            center: [defaultLat, defaultLng],
            zoom: 13,
            scrollWheelZoom: false,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);

        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });
    }

    function updateCoords(lat, lng) {
        document.getElementById('map_lat').value = lat.toFixed(7);
        document.getElementById('map_lng').value = lng.toFixed(7);
    }

    function locateUserPosition() {
        if (!navigator.geolocation) {
            alert('Fitur geolokasi tidak didukung oleh browser Anda.');
            return;
        }
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            if (map && marker) {
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                updateCoords(lat, lng);
            }
        }, err => {
            alert('Tidak dapat mendeteksi lokasi GPS Anda: ' + err.message);
        });
    }

    // Previews & File helpers
    function previewKtp(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            document.getElementById('ktpFileName').textContent = file.name + ` (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            const box = document.getElementById('ktpPreviewBox');
            box.classList.remove('d-none');
            box.classList.add('d-flex');
        }
    }

    function removeKtp() {
        const input = document.getElementById('ktp_file');
        input.value = '';
        const box = document.getElementById('ktpPreviewBox');
        box.classList.add('d-none');
        box.classList.remove('d-flex');
    }

    function displayGenericFileName(input, targetId) {
        const el = document.getElementById(targetId);
        if (input.files && input.files[0] && el) {
            el.textContent = 'File terpilih: ' + input.files[0].name;
            el.classList.remove('d-none');
        }
    }

    function previewMultiImages(input, gridId) {
        const grid = document.getElementById(gridId);
        grid.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const div = document.createElement('div');
                        div.className = 'preview-thumb-item';
                        div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                        grid.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    function toggleMitraPwd(fieldId, btn) {
        const field = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'fa-regular fa-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'fa-regular fa-eye';
        }
    }

    function populateSummary() {
        document.getElementById('summary_owner').textContent = document.getElementById('owner_name').value || '—';
        document.getElementById('summary_contact').textContent = (document.getElementById('owner_email').value || '') + ' | ' + (document.getElementById('owner_phone').value || '');
        document.getElementById('summary_business').textContent = document.getElementById('display_name').value || '—';
        
        const serviceSelect = document.getElementById('service_type_id');
        const serviceText = serviceSelect.options[serviceSelect.selectedIndex]?.text || '—';
        document.getElementById('summary_service').textContent = serviceText;

        document.getElementById('summary_address').textContent = document.getElementById('address').value || '—';

        const bankSelect = document.getElementById('bank_code');
        const bankName = bankSelect.options[bankSelect.selectedIndex]?.text || '';
        const accNum = document.getElementById('account_number').value || '';
        const accName = document.getElementById('account_name').value || '';
        document.getElementById('summary_bank').textContent = `${bankName} - ${accNum} (a.n. ${accName})`;
    }
</script>
@endpush
@endsection
