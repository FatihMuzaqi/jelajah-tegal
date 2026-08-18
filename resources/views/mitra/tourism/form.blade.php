@extends('layouts.mitra')

@php($editing = isset($tourism))

@section('title', $editing ? 'Edit Wisata: ' . $tourism->name : 'Tambah Destinasi Wisata')
@section('page-title', $editing ? 'Edit Destinasi Wisata' : 'Tambah Destinasi Wisata Baru')
@section('page-description', 'Kelola informasi destinasi, fasilitas, dan titik koordinat peta sebelum diajukan.')

@section('content')
<style>
    .form-section-card {
        background: #ffffff;
        border: 1px solid var(--lokantara-border, #e2e8f0);
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 22px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    }
    .form-section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .form-section-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 17px;
        flex-shrink: 0;
    }
    .facility-chip-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        transition: all 0.2s ease;
        user-select: none;
    }
    .facility-chip-label:hover {
        border-color: #047857;
        background: #f0fdf4;
    }
    .facility-chip-checkbox:checked + .facility-chip-label {
        background: #ecfdf5;
        border-color: #047857;
        color: #065f46;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(4, 120, 87, 0.15);
    }
</style>

<form method="POST" action="{{ $editing ? route('mitra.tourism.update', $tourism) : route('mitra.tourism.store') }}">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <!-- 1. IDENTITAS & KATEGORI DESTINASI -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #ecfdf5; color: #047857;">
                <i class="fa-solid fa-umbrella-beach"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Identitas & Wilayah Destinasi</h5>
                <small class="text-muted" style="font-size: 12px;">Nama resmi tempat wisata, slug URL, dan kategori sektor.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Nama Destinasi Wisata <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-monument"></i></span>
                    <input type="text" name="name" id="tourism_name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="Contoh: Taman Wisata Air Panas Guci" value="{{ old('name', $tourism->name ?? '') }}" required oninput="generateSlug(this.value)">
                </div>
                @error('name') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Slug URL Sistem <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-link"></i></span>
                    <input type="text" name="slug" id="tourism_slug" class="form-control @error('slug') is-invalid @enderror"
                           placeholder="contoh: taman-wisata-guci" value="{{ old('slug', $tourism->slug ?? '') }}" required>
                </div>
                @error('slug') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Kategori Wisata <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-tags"></i></span>
                    <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}" @selected(old('category_id', $tourism->category_id ?? null) == $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('category_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Wilayah / Kecamatan <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-map"></i></span>
                    <select class="form-select @error('region_id') is-invalid @enderror" name="region_id" required>
                        <option value="">Pilih Wilayah</option>
                        @foreach ($regions as $item)
                            <option value="{{ $item->id }}" @selected(old('region_id', $tourism->region_id ?? null) == $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('region_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <!-- 2. DESKRIPSI & ALAMAT LENGKAP -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #eef2ff; color: #4f46e5;">
                <i class="fa-solid fa-align-left"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Deskripsi & Alamat Lokasi</h5>
                <small class="text-muted" style="font-size: 12px;">Uraikan keunggulan destinasi serta alamat lengkap yang mudah diakses wisatawan.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold" style="font-size: 13px;">Deskripsi Lengkap Destinasi</label>
                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                          placeholder="Jelaskan daya tarik, keindahan alam, atraksi utama, dan fasilitas destinasi wisata ini...">{{ old('description', $tourism->description ?? '') }}</textarea>
                @error('description') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-bold" style="font-size: 13px;">Alamat Fisik / Lokasi Jalan</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-location-arrow"></i></span>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                           placeholder="Contoh: Jl. Objek Wisata Guci No. 12, Kalisari, Bumijawa, Tegal" value="{{ old('address', $tourism->address ?? '') }}">
                </div>
                @error('address') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <!-- 3. TITIK KOORDINAT GPS (INTERACTIVE MAP READY) -->
    <div class="form-section-card">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="form-section-icon" style="background: #fef2f2; color: #dc2626;">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6">Titik Koordinat Lokasi (GPS)</h5>
                    <small class="text-muted" style="font-size: 12px;">Diperlukan untuk akurasi peta rute navigasi dan petunjuk arah pengunjung.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-success rounded-3 fw-bold d-inline-flex align-items-center gap-1.5" onclick="detectGPSLocation(this)">
                    <i class="fa-solid fa-crosshairs"></i> Gunakan Lokasi GPS Saya
                </button>
                <a id="gmaps-preview-btn" href="#" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3 fw-bold d-none align-items-center gap-1.5" rel="noopener noreferrer">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Cek di Google Maps
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Latitude (Garis Lintang) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-arrows-up-down"></i></span>
                    <input type="number" step="any" name="latitude" id="coord_latitude" class="form-control @error('latitude') is-invalid @enderror"
                           placeholder="Contoh: -7.1954321" value="{{ old('latitude', $tourism->location->latitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
                </div>
                @error('latitude') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                <small class="text-muted d-block mt-1" style="font-size: 11px;">Wilayah Tegal & sekitarnya berkisar antara -6.8 s/d -7.2</small>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Longitude (Garis Bujur) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-arrows-left-right"></i></span>
                    <input type="number" step="any" name="longitude" id="coord_longitude" class="form-control @error('longitude') is-invalid @enderror"
                           placeholder="Contoh: 109.1678901" value="{{ old('longitude', $tourism->location->longitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
                </div>
                @error('longitude') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                <small class="text-muted d-block mt-1" style="font-size: 11px;">Wilayah Tegal & sekitarnya berkisar antara 109.0 s/d 109.3</small>
            </div>
        </div>

        <div class="alert alert-light border d-flex align-items-start gap-2 mt-3 mb-0 py-2.5 px-3 rounded-3" style="font-size: 12px; background: #f8fafc;">
            <i class="fa-solid fa-circle-info text-primary mt-0.5 flex-shrink-0"></i>
            <div>
                <strong>Tips Salin Koordinat Google Maps:</strong> Buka Google Maps di browser, klik kanan pada titik lokasi Anda, lalu klik angka koordinat di baris paling atas untuk otomatis menyalin Latitude & Longitude.
            </div>
        </div>
    </div>

    <!-- 4. SPESIFIKASI WISATA -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Karakteristik & Durasi Wisata</h5>
                <small class="text-muted" style="font-size: 12px;">Tipe destinasi, estimasi durasi kunjungan, dan badge promosi.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Tipe Karakteristik <span class="text-danger">*</span></label>
                <select class="form-select @error('destination_type') is-invalid @enderror" name="destination_type" required>
                    @foreach (['nature' => 'Wisata Alam & Pegunungan/Pantai', 'culture' => 'Wisata Budaya & Sejarah', 'recreation' => 'Rekreasi & Hiburan Keluarga', 'education' => 'Edukasi & Sains', 'religious' => 'Wisata Religi', 'other' => 'Lainnya'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('destination_type', $tourism->tourism->destination_type ?? 'nature') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('destination_type') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Durasi Kunjungan Rata-rata</label>
                <div class="input-group">
                    <input type="number" name="visit_duration_minutes" class="form-control" placeholder="120"
                           value="{{ old('visit_duration_minutes', $tourism->tourism->visit_duration_minutes ?? '') }}">
                    <span class="input-group-text bg-white">Menit</span>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Badge Promosi / Tagline</label>
                <input type="text" name="badge" class="form-control" placeholder="Contoh: Populer / Eksotis"
                       value="{{ old('badge', $tourism->tourism->badge ?? '') }}">
            </div>
        </div>
    </div>

    <!-- 5. FASILITAS & FITUR UNGGULAN -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #f0fdf4; color: #16a34a;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Fasilitas Tersedia & Status Unggulan</h5>
                <small class="text-muted" style="font-size: 12px;">Pilih fasilitas umum yang tersedia di area destinasi wisata Anda.</small>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold d-block mb-2" style="font-size: 13px;">Pilihan Fasilitas:</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($facilities as $item)
                    @php($isChecked = in_array($item->id, old('facilities', $editing ? $tourism->facilities->pluck('id')->all() : [])))
                    <div>
                        <input type="checkbox" name="facilities[]" value="{{ $item->id }}" id="facility_{{ $item->id }}" class="facility-chip-checkbox d-none" @checked($isChecked)>
                        <label for="facility_{{ $item->id }}" class="facility-chip-label">
                            <i class="fa-solid fa-check text-success {{ $isChecked ? '' : 'opacity-25' }}"></i>
                            <span>{{ $item->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-3 rounded-3 border d-flex flex-wrap align-items-center gap-4" style="background: #f8fafc;">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_hidden_gem" value="1" id="switch_hidden_gem"
                       @checked(old('is_hidden_gem', $tourism->tourism->is_hidden_gem ?? false))>
                <label class="form-check-label fw-semibold" for="switch_hidden_gem">
                    <i class="fa-solid fa-gem text-primary me-1"></i> Hidden Gem (Wisata Tersembunyi Eksotis)
                </label>
            </div>

            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_featured" value="1" id="switch_featured"
                       @checked(old('is_featured', $tourism->is_featured ?? false))>
                <label class="form-check-label fw-semibold" for="switch_featured">
                    <i class="fa-solid fa-star text-warning me-1"></i> Destinasi Pilihan (Featured Destination)
                </label>
            </div>
        </div>
    </div>

    <!-- 6. ACTION BUTTONS -->
    <div class="d-flex align-items-center justify-content-between gap-3 pt-2 mb-5">
        <a href="{{ route('mitra.tourism.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <button type="submit" class="btn btn-lokantara rounded-pill px-5 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>{{ $editing ? 'Perbarui Destinasi' : 'Simpan Draft Destinasi' }}</span>
        </button>
    </div>
</form>

@push('scripts')
<script>
    function generateSlug(text) {
        const slugInput = document.getElementById('tourism_slug');
        if (slugInput && (!slugInput.value || !{{ $editing ? 'true' : 'false' }})) {
            slugInput.value = text.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
        }
    }

    function updateGmapsPreviewLink() {
        const lat = document.getElementById('coord_latitude')?.value?.trim();
        const lng = document.getElementById('coord_longitude')?.value?.trim();
        const btn = document.getElementById('gmaps-preview-btn');
        if (btn) {
            if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                btn.href = `https://www.google.com/maps?q=${lat},${lng}`;
                btn.classList.remove('d-none');
                btn.classList.add('d-inline-flex');
            } else {
                btn.classList.add('d-none');
                btn.classList.remove('d-inline-flex');
            }
        }
    }

    function detectGPSLocation(btn) {
        if (!navigator.geolocation) {
            alert('Fitur Geolocation tidak didukung oleh browser Anda.');
            return;
        }
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mendeteksi...';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('coord_latitude').value = position.coords.latitude.toFixed(7);
                document.getElementById('coord_longitude').value = position.coords.longitude.toFixed(7);
                updateGmapsPreviewLink();
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check text-success"></i> Lokasi Terdeteksi!';
                setTimeout(() => { btn.innerHTML = origHtml; }, 3000);
            },
            function(error) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                let msg = 'Gagal mendeteksi lokasi GPS.';
                if (error.code === error.PERMISSION_DENIED) {
                    msg = 'Izin akses lokasi ditolak oleh browser. Silakan izinkan akses lokasi atau masukkan koordinat secara manual.';
                }
                alert(msg);
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    document.addEventListener('DOMContentLoaded', updateGmapsPreviewLink);
</script>
@endpush
@endsection
