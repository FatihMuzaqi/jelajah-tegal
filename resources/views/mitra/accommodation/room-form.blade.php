@extends('layouts.mitra')

@section('title', 'Edit Tipe Kamar: ' . $room->name)
@section('page-title', 'Edit Tipe Kamar: ' . $room->name)
@section('page-description', 'Properti: ' . $accommodation->name . ' — Kelola tarif per malam, kapasitas tamu, unit, dan fasilitas kamar.')

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

<!-- Action Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded"
     style="background: var(--lokantara-surface); border: 1px solid var(--lokantara-border);">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('mitra.accommodation.show', $accommodation) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Properti
        </a>
        <span class="text-muted">|</span>
        <span class="fw-bold fs-6">{{ $room->name }}</span>
        <x-status-badge :status="$room->status" />
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('mitra.accommodation.rooms.calendar', [$accommodation, $room]) }}" class="btn btn-sm btn-outline-lokantara fw-bold">
            <i class="fa-solid fa-calendar-days me-1"></i> Kalender Ketersediaan & Tarif
        </a>
    </div>
</div>

<form method="POST" action="{{ route('mitra.accommodation.rooms.update', [$accommodation, $room]) }}">
    @csrf
    @method('PUT')

    <!-- 1. IDENTITAS KAMAR & TARIF HARGA -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #ede9fe; color: #7c3aed;">
                <i class="fa-solid fa-bed"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Identitas Kamar & Tarif Sewa</h5>
                <small class="text-muted" style="font-size: 12px;">Nama tipe kamar, tarif sewa reguler per malam, dan jenis ruangan.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Nama Tipe Kamar <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-door-open"></i></span>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="Contoh: Deluxe King Room with Balcony" value="{{ old('name', $room->name) }}" required>
                </div>
                @error('name') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Tarif Sewa per Malam (Rp) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white fw-bold text-success">Rp</span>
                    <input type="number" step="0.01" min="0" name="nightly_price" class="form-control @error('nightly_price') is-invalid @enderror"
                           placeholder="Contoh: 450000" value="{{ old('nightly_price', $room->offer->price) }}" required>
                </div>
                @error('nightly_price') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Status Publikasi <span class="text-danger">*</span>
                </label>
                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                    <option value="active" @selected(old('status', $room->status) === 'active')> Aktif (Siap Dipesan)</option>
                    <option value="draft" @selected(old('status', $room->status) === 'draft')> Draft (Disembunyikan)</option>
                </select>
                @error('status') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Klasifikasi / Tipe Kamar <span class="text-danger">*</span>
                </label>
                <input type="text" name="room_type" class="form-control @error('room_type') is-invalid @enderror"
                       placeholder="Contoh: Deluxe / Standard / Suite / Family" value="{{ old('room_type', $room->room_type) }}" required>
                @error('room_type') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Jenis Ruang <span class="text-danger">*</span>
                </label>
                <select class="form-select @error('kind') is-invalid @enderror" name="kind" required>
                    <option value="room" @selected(old('kind', $room->kind) === 'room')> Kamar Bangunan (Room)</option>
                    <option value="tent_plot" @selected(old('kind', $room->kind) === 'tent_plot')> Kavling Lahan Tenda (Tent Plot)</option>
                </select>
                @error('kind') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-bold" style="font-size: 13px;">Deskripsi Fasilitas & Suasana Kamar</label>
                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                          placeholder="Jelaskan ukuran kamar, pemandangan jendela, fasilitas kamar mandi, dan kenyamanan lainnya...">{{ old('description', $room->description) }}</textarea>
                @error('description') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <!-- 2. KAPASITAS & JUMLAH UNIT KAMAR -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Kapasitas Tamu & Jumlah Kamar</h5>
                <small class="text-muted" style="font-size: 12px;">Jumlah tamu maksimal yang diperbolehkan menginap dan total stok unit kamar fisik.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Kapasitas Tamu Dewasa <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" min="1" max="100" name="capacity_adults" class="form-control"
                           value="{{ old('capacity_adults', $room->capacity_adults) }}" required>
                    <span class="input-group-text bg-white">Orang</span>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Kapasitas Tamu Anak <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" min="0" max="100" name="capacity_children" class="form-control"
                           value="{{ old('capacity_children', $room->capacity_children) }}" required>
                    <span class="input-group-text bg-white">Anak</span>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Total Stok Unit Kamar <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" min="1" max="10000" name="total_units" class="form-control"
                           value="{{ old('total_units', $room->total_units) }}" required>
                    <span class="input-group-text bg-white">Unit Kamar</span>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Minimum Menginap</label>
                <div class="input-group">
                    <input type="number" min="1" max="365" name="min_stay_nights" class="form-control"
                           value="{{ old('min_stay_nights', $room->min_stay_nights) }}">
                    <span class="input-group-text bg-white">Malam</span>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Maksimum Menginap</label>
                <div class="input-group">
                    <input type="number" min="1" max="365" name="max_stay_nights" class="form-control"
                           value="{{ old('max_stay_nights', $room->max_stay_nights) }}">
                    <span class="input-group-text bg-white">Malam</span>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Batas Pesan di Muka (Advance Booking)</label>
                <div class="input-group">
                    <input type="number" min="0" max="1095" name="advance_booking_days" class="form-control"
                           value="{{ old('advance_booking_days', $room->advance_booking_days) }}">
                    <span class="input-group-text bg-white">Hari Sebelum</span>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold" style="font-size: 13px;">Catatan Ketersediaan Khusus</label>
                <input type="text" name="availability_notes" class="form-control"
                       placeholder="Contoh: Termasuk sarapan untuk 2 orang. Tambahan extrabed dikenakan Rp 100.000."
                       value="{{ old('availability_notes', $room->availability_notes) }}">
            </div>
        </div>
    </div>

    <!-- 3. FASILITAS KHUSUS KAMAR -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: #f0fdf4; color: #16a34a;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Fasilitas Khusus Tipe Kamar Ini</h5>
                <small class="text-muted" style="font-size: 12px;">Pilih fasilitas yang tersedia di dalam unit kamar ini.</small>
            </div>
        </div>

        <div class="mb-2">
            <div class="d-flex flex-wrap gap-2">
                @foreach ($facilities as $itemFac)
                    @php($isChecked = in_array($itemFac->id, old('facilities', $room->facilities->pluck('id')->all())))
                    <div>
                        <input type="checkbox" name="facilities[]" value="{{ $itemFac->id }}" id="fac_{{ $itemFac->id }}" class="facility-chip-checkbox d-none" @checked($isChecked)>
                        <label for="fac_{{ $itemFac->id }}" class="facility-chip-label">
                            <i class="fa-solid fa-check text-success {{ $isChecked ? '' : 'opacity-25' }}"></i>
                            <span>{{ $itemFac->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 4. ACTION BUTTONS -->
    <div class="d-flex align-items-center justify-content-between gap-3 pt-2 mb-5">
        <a href="{{ route('mitra.accommodation.show', $accommodation) }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Batal & Kembali
        </a>
        <button type="submit" class="btn btn-lokantara rounded-pill px-5 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Simpan Perubahan Kamar</span>
        </button>
    </div>
</form>

<!-- Media & Foto Kamar -->
<div class="form-section-card mt-4">
    <div class="form-section-header">
        <div class="form-section-icon" style="background: #fef3c7; color: #d97706;">
            <i class="fa-solid fa-images"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-0 text-dark fs-6">Foto & Galeri Kamar Ini ({{ $room->media->count() }})</h5>
            <small class="text-muted" style="font-size: 12px;">Unggah foto interior dan kamar mandi untuk menarik perhatian calon tamu.</small>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('mitra.accommodation.rooms.media', [$accommodation, $room]) }}" class="mb-4">
        @csrf
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <input class="form-control form-control-sm" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple required>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="role">
                    <option value="cover">Foto Cover Kamar</option>
                    <option value="gallery">Galeri Kamar</option>
                </select>
            </div>
            <div class="col-md-2">
                <input class="form-control form-control-sm" name="caption" placeholder="Caption (opsional)">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-lokantara w-100 fw-bold">
                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Unggah Foto
                </button>
            </div>
        </div>
    </form>

    @if($room->media->isNotEmpty())
        <div class="row g-2">
            @foreach($room->media as $media)
                <div class="col-md-3 col-6">
                    <div class="card h-100 border rounded-3 overflow-hidden">
                        <img src="{{ asset('storage/' . $media->object_key) }}" alt="Foto Kamar" style="height: 120px; width: 100%; object-fit: cover;">
                        <div class="p-2 d-flex justify-content-between align-items-center" style="font-size: 11px;">
                            <span class="badge {{ $media->pivot->role === 'cover' ? 'bg-warning text-dark' : 'bg-dark' }}">{{ strtoupper($media->pivot->role) }}</span>
                            <small class="text-muted">{{ number_format($media->size_bytes / 1024, 0) }} KB</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
