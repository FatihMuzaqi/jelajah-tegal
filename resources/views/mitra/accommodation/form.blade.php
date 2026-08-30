@extends('layouts.mitra')

@php($editing = isset($accommodation))

@section('title', $editing ? 'Edit Penginapan: ' . $accommodation->name : 'Tambah Penginapan')
@section('page-title', $editing ? 'Edit Penginapan' : 'Tambah Penginapan Baru')
@section('page-description', 'Kelola informasi properti penginapan, fasilitas, dan titik koordinat peta sebelum diajukan.')

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
        border-color: #8b5cf6;
        background: #f5f3ff;
    }
    .facility-chip-checkbox:checked + .facility-chip-label {
        background: #ede9fe;
        border-color: #8b5cf6;
        color: #5b21b6;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(139, 92, 246, 0.15);
    }
</style>

<form method="POST" action="{{ $editing ? route('mitra.accommodation.update', $accommodation) : route('mitra.accommodation.store') }}">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <!-- 1. IDENTITAS & TIPE PROPERTI -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #f5f3ff; color: #8b5cf6;">
                <i class="fa-solid fa-hotel"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Identitas & Tipe Properti</h5>
                <small class="text-muted" style="font-size: 12px;">Nama resmi hotel/penginapan, tipe properti, dan wilayah.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Nama Properti / Hotel <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-building"></i></span>
                    <input type="text" name="name" id="acc_name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="Contoh: Hotel Bahari Inn Tegal" value="{{ old('name', $accommodation->name ?? '') }}" required oninput="generateSlug(this.value)">
                </div>
                @error('name') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Slug URL Sistem <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-link"></i></span>
                    <input type="text" name="slug" id="acc_slug" class="form-control @error('slug') is-invalid @enderror"
                           placeholder="contoh: hotel-bahari-inn" value="{{ old('slug', $accommodation->slug ?? '') }}" required>
                </div>
                @error('slug') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Tipe Properti <span class="text-danger">*</span>
                </label>
                <select class="form-select @error('property_type') is-invalid @enderror" name="property_type" required>
                    @foreach (['hotel' => 'Hotel', 'homestay' => 'Homestay', 'villa' => 'Villa', 'resort' => 'Resort', 'camping_ground' => 'Camping Ground'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('property_type', $accommodation->accommodation->property_type ?? 'hotel') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('property_type') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Kategori Layanan <span class="text-danger">*</span>
                </label>
                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $item)
                        <option value="{{ $item->id }}" @selected(old('category_id', $accommodation->category_id ?? null) == $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Wilayah / Kecamatan <span class="text-danger">*</span>
                </label>
                <select class="form-select @error('region_id') is-invalid @enderror" name="region_id" required>
                    <option value="">Pilih Wilayah</option>
                    @foreach ($regions as $item)
                        <option value="{{ $item->id }}" @selected(old('region_id', $accommodation->region_id ?? null) == $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
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
                <small class="text-muted" style="font-size: 12px;">Uraikan suasana tempat menginap, tipe layanan, serta alamat detail.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold" style="font-size: 13px;">Deskripsi Penginapan</label>
                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                          placeholder="Jelaskan kenyamanan kamar, panorama sekitar, keramahan staf, dan daya tarik properti Anda...">{{ old('description', $accommodation->description ?? '') }}</textarea>
                @error('description') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-bold" style="font-size: 13px;">Alamat Fisik Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-location-arrow"></i></span>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                           placeholder="Contoh: Jl. Dr. Wahidin Sudirohusodo No. 1, Kota Tegal" value="{{ old('address', $accommodation->address ?? '') }}">
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
                    <small class="text-muted" style="font-size: 12px;">Diperlukan untuk panduan peta rute navigasi tamu ke penginapan Anda.</small>
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
                           placeholder="Contoh: -6.8612345" value="{{ old('latitude', $accommodation->location->latitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
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
                           placeholder="Contoh: 109.1412345" value="{{ old('longitude', $accommodation->location->longitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
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

    <!-- 4. OPERASIONAL & BINTANG PROPERTI -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Waktu Check-In, Check-Out & Bintang</h5>
                <small class="text-muted" style="font-size: 12px;">Tentukan jadwal jam kedatangan, kepulangan, serta kelas bintang properti.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Waktu Check-In</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-clock"></i></span>
                    <input type="time" name="check_in_time" class="form-control"
                           value="{{ old('check_in_time', $accommodation->accommodation->check_in_time ?? '14:00') }}">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Waktu Check-Out</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-clock"></i></span>
                    <input type="time" name="check_out_time" class="form-control"
                           value="{{ old('check_out_time', $accommodation->accommodation->check_out_time ?? '12:00') }}">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Klasifikasi Bintang (1 - 5)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-warning"><i class="fa-solid fa-star"></i></span>
                    <select class="form-select" name="star_rating">
                        <option value="">Non-Bintang</option>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" @selected(old('star_rating', $accommodation->accommodation->star_rating ?? null) == $i)>
                                {{ $i }} Bintang ({{ str_repeat('', $i) }})
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. FASILITAS PROPERTI & STATUS UNGGULAN -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #f0fdf4; color: #16a34a;">
                <i class="fa-solid fa-bell-concierge"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Fasilitas Properti & Status Pilihan</h5>
                <small class="text-muted" style="font-size: 12px;">Pilih fasilitas umum yang dapat dinikmati para tamu hotel.</small>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold d-block mb-2" style="font-size: 13px;">Fasilitas Properti:</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($facilities as $item)
                    @php($isChecked = in_array($item->id, old('facilities', $editing ? $accommodation->facilities->pluck('id')->all() : [])))
                    <div>
                        <input type="checkbox" name="facilities[]" value="{{ $item->id }}" id="facility_{{ $item->id }}" class="facility-chip-checkbox d-none" @checked($isChecked)>
                        <label for="facility_{{ $item->id }}" class="facility-chip-label">
                            <i class="fa-solid fa-check text-purple {{ $isChecked ? '' : 'opacity-25' }}"></i>
                            <span>{{ $item->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-3 rounded-3 border d-flex align-items-center" style="background: #f8fafc;">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_featured" value="1" id="switch_featured"
                       @checked(old('is_featured', $accommodation->is_featured ?? false))>
                <label class="form-check-label fw-semibold" for="switch_featured">
                    <i class="fa-solid fa-star text-warning me-1"></i> Properti Pilihan Utama (Featured Accommodation)
                </label>
            </div>
        </div>
    </div>

    <!-- 6. ACTION BUTTONS -->
    <div class="d-flex align-items-center justify-content-between gap-3 pt-2 mb-5">
        <a href="{{ route('mitra.accommodation.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <button type="submit" class="btn btn-lokantara rounded-pill px-5 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>{{ $editing ? 'Perbarui Penginapan' : 'Simpan Draft Penginapan' }}</span>
        </button>
    </div>
</form>

@push('scripts')
<script>
    function generateSlug(text) {
        const slugInput = document.getElementById('acc_slug');
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
