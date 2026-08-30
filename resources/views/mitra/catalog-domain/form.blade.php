@extends('layouts.mitra')

@php
    $editing = isset($item);
    $detail = $editing ? $item->{$domain} : null;
    
    $domainMeta = [
        'culinary' => [
            'icon' => 'fa-utensils',
            'theme' => '#d97706',
            'bg' => '#fef3c7',
            'label' => 'Tempat Kuliner',
            'example_name' => 'Sate Kambing Muda Cempe Lemu',
            'example_slug' => 'sate-kambing-cempe-lemu',
            'desc_placeholder' => 'Ceritakan kelezatan menu andalan, racikan bumbu khas Tegal, dan suasana bersantap...',
        ],
        'event' => [
            'icon' => 'fa-ticket',
            'theme' => '#dc2626',
            'bg' => '#fee2e2',
            'label' => 'Event / Acara',
            'example_name' => 'Festival Pesisir Pantai Alam Indah 2026',
            'example_slug' => 'festival-pesisir-pai-2026',
            'desc_placeholder' => 'Jelaskan rangkuman agenda acara, bintang tamu/performer, rundown, dan keunikan festival ini...',
        ],
        'rental' => [
            'icon' => 'fa-car',
            'theme' => '#2563eb',
            'bg' => '#dbeafe',
            'label' => 'Armada Rental',
            'example_name' => 'Toyota All New Avanza 1.5 G Manual',
            'example_slug' => 'all-new-avanza-manual',
            'desc_placeholder' => 'Jelaskan kondisi kendaraan, performa mesin, kebersihan kabin, dan kenyamanan berkendara...',
        ],
    ];
    $meta = $domainMeta[$domain] ?? [
        'icon' => 'fa-layer-group',
        'theme' => '#047857',
        'bg' => '#ecfdf5',
        'label' => $title,
        'example_name' => 'Nama Layanan',
        'example_slug' => 'nama-layanan',
        'desc_placeholder' => 'Jelaskan detail layanan...',
    ];
@endphp

@section('title', ($editing ? 'Edit ' : 'Tambah ') . $title . ($editing ? ': ' . $item->name : ''))
@section('page-title', ($editing ? 'Edit ' : 'Tambah ') . $title . ' Baru')
@section('page-description', 'Kelola informasi ' . strtolower($meta['label']) . ', spesifikasi, dan titik koordinat peta.')

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

<form method="POST" action="{{ $editing ? route($routePrefix . '.update', $item) : route($routePrefix . '.store') }}">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <!-- 1. IDENTITAS & KATEGORI -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: {{ $meta['bg'] }}; color: {{ $meta['theme'] }};">
                <i class="fa-solid {{ $meta['icon'] }}"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark fs-6">Identitas {{ $meta['label'] }}</h5>
                <small class="text-muted" style="font-size: 12px;">Nama resmi, slug URL sistem, dan wilayah administrasi.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Nama {{ $meta['label'] }} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-pen-nib"></i></span>
                    <input type="text" name="name" id="domain_name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="Contoh: {{ $meta['example_name'] }}" value="{{ old('name', $item->name ?? '') }}" required oninput="generateSlug(this.value)">
                </div>
                @error('name') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Slug URL Sistem <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-link"></i></span>
                    <input type="text" name="slug" id="domain_slug" class="form-control @error('slug') is-invalid @enderror"
                           placeholder="contoh: {{ $meta['example_slug'] }}" value="{{ old('slug', $item->slug ?? '') }}" required>
                </div>
                @error('slug') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            @if(isset($categories) && $categories->isNotEmpty())
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size: 13px;">
                        Kategori Layanan <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-layer-group"></i></span>
                        <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                            <option value="">Pilih Kategori Layanan</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $item->category_id ?? null) == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('category_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                </div>
            @endif

            <div class="{{ isset($categories) && $categories->isNotEmpty() ? 'col-md-6' : 'col-md-12' }}">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Wilayah / Kecamatan <span class="text-danger">*</span>
                </label>
                <select class="form-select @error('region_id') is-invalid @enderror" name="region_id" required>
                    <option value="">Pilih Wilayah</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @selected(old('region_id', $item->region_id ?? null) == $region->id)>{{ $region->name }}</option>
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
                <h5 class="fw-bold mb-0 text-dark fs-6">Deskripsi & Lokasi Alamat</h5>
                <small class="text-muted" style="font-size: 12px;">Deskripsi lengkap untuk memikat minat pengunjung dan alamat fisik yang jelas.</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Deskripsi Lengkap <span class="text-danger">*</span>
                </label>
                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required
                          placeholder="{{ $meta['desc_placeholder'] }}">{{ old('description', $item->description ?? '') }}</textarea>
                <small class="text-muted d-block mt-1" style="font-size: 11px;">Minimal 20 karakter penjelasan.</small>
                @error('description') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-bold" style="font-size: 13px;">
                    Alamat Fisik / Lokasi Jalan <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-location-arrow"></i></span>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" required
                           placeholder="Contoh: Jl. Kapten Sudibyo No. 45, Kemandungan, Kota Tegal" value="{{ old('address', $item->address ?? '') }}">
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
                    <small class="text-muted" style="font-size: 12px;">Diperlukan untuk akurasi peta rute navigasi dan petunjuk jalan pengunjung.</small>
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
                           placeholder="Contoh: -6.8797000" value="{{ old('latitude', $item?->location?->latitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
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
                           placeholder="Contoh: 109.1256000" value="{{ old('longitude', $item?->location?->longitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
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

    <!-- 4. SPESIFIKASI KHUSUS DOMAIN -->
    @if ($domain === 'culinary')
        <!-- SPESIFIKASI KULINER -->
        <div class="form-section-card">
            <div class="form-section-header">
                <div class="form-section-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6">Operasional & Reservasi Meja Kuliner</h5>
                    <small class="text-muted" style="font-size: 12px;">Jenis tempat makan, kontak reservasi, dan aturan pemesanan tempat.</small>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size: 13px;">Jenis Venue Kuliner <span class="text-danger">*</span></label>
                    <select class="form-select" name="venue_type" required>
                        @foreach (['restaurant' => 'Restoran / Rumah Makan', 'cafe' => 'Kafe & Tongkrongan', 'street_food' => 'Kuliner Kaki Lima / Tradisional', 'food_court' => 'Food Court / Sentra Kuliner', 'bakery' => 'Bakery & Toko Oleh-oleh', 'other' => 'Lainnya'] as $vKey => $vLabel)
                            <option value="{{ $vKey }}" @selected(old('venue_type', $detail?->venue_type ?? 'restaurant') === $vKey)>{{ $vLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size: 13px;">Nomor Telepon / WhatsApp Operasional</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-phone"></i></span>
                        <input type="text" class="form-control" name="phone" placeholder="Contoh: 081234567890"
                               value="{{ old('phone', $detail?->phone) }}">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" style="font-size: 13px;">Catatan / Ketentuan Khusus Reservasi</label>
                    <input type="text" class="form-control" name="reservation_notes" placeholder="Contoh: Minimal pemesanan H-1, toleransi keterlambatan 15 menit."
                           value="{{ old('reservation_notes', $detail?->reservation_notes) }}">
                </div>

                <div class="col-12">
                    <div class="p-3 rounded-3 border d-flex align-items-center" style="background: #f8fafc;">
                        <input type="hidden" name="accepts_reservations" value="0">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="accepts_reservations" value="1" id="switch_res"
                                   @checked(old('accepts_reservations', $detail?->accepts_reservations))>
                            <label class="form-check-label fw-semibold" for="switch_res">
                                <i class="fa-solid fa-chair text-primary me-1"></i> Buka Layanan Reservasi Meja Online untuk Pengunjung
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($domain === 'event')
        <!-- SPESIFIKASI EVENT -->
        <div class="form-section-card">
            <div class="form-section-header">
                <div class="form-section-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6">Jadwal Pelaksanaan & Waktu Event</h5>
                    <small class="text-muted" style="font-size: 12px;">Tanggal mulai, tanggal selesai, dan batas registrasi tiket event.</small>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size: 13px;">Jenis / Kategori Event <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('event_type') is-invalid @enderror" name="event_type"
                           placeholder="Contoh: Konser Musik / Pameran UMKM / Lomba Budaya" value="{{ old('event_type', $detail?->event_type) }}" required>
                    @error('event_type') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size: 13px;">Nama Panggung / Gedung Venue</label>
                    <input type="text" class="form-control" name="venue_name" placeholder="Contoh: Panggung Utama Lapangan Tegal"
                           value="{{ old('venue_name', $detail?->venue_name) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size: 13px;">Waktu Acara Dimulai <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" name="starts_at"
                           value="{{ old('starts_at', $detail?->starts_at?->format('Y-m-d\TH:i')) }}" required>
                    @error('starts_at') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size: 13px;">Waktu Acara Berakhir <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror" name="ends_at"
                           value="{{ old('ends_at', $detail?->ends_at?->format('Y-m-d\TH:i')) }}" required>
                    @error('ends_at') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size: 13px;">Batas Akhir Penjualan Tiket</label>
                    <input type="datetime-local" class="form-control @error('registration_deadline') is-invalid @enderror" name="registration_deadline"
                           value="{{ old('registration_deadline', $detail?->registration_deadline?->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" style="font-size: 13px;">Informasi Penting Pengunjung (Know Before You Go)</label>
                    <textarea name="know_before_you_go" rows="2" class="form-control"
                              placeholder="Contoh: Dilarang membawa senjata tajam, kamera profesional, dan botol kaca. Pintu dibuka 1 jam sebelum acara.">{{ old('know_before_you_go', $detail?->know_before_you_go) }}</textarea>
                </div>
            </div>
        </div>
    @elseif ($domain === 'rental')
        <!-- SPESIFIKASI RENTAL -->
        <div class="form-section-card">
            <div class="form-section-header">
                <div class="form-section-icon" style="background: #dbeafe; color: #2563eb;">
                    <i class="fa-solid fa-car"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6">Spesifikasi & Ketentuan Sewa Kendaraan</h5>
                    <small class="text-muted" style="font-size: 12px;">Merek, transmisi, kapasitas kursi, deposit jaminan, dan opsi sopir.</small>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size: 13px;">Tipe Kendaraan <span class="text-danger">*</span></label>
                    <select class="form-select" name="vehicle_type" required>
                        @foreach (['car' => 'Mobil (City Car / MPV / SUV)', 'motorcycle' => 'Sepeda Motor', 'minibus' => 'Minibus / Hiace / Elf', 'bus' => 'Bus Pariwisata', 'bicycle' => 'Sepeda'] as $vKey => $vLabel)
                            <option value="{{ $vKey }}" @selected(old('vehicle_type', $detail?->vehicle_type ?? 'car') === $vKey)>{{ $vLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size: 13px;">Merek Kendaraan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="brand" placeholder="Contoh: Toyota / Honda / Mitsubishi"
                           value="{{ old('brand', $detail?->brand) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size: 13px;">Model & Varian <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="model" placeholder="Contoh: Avanza 1.5 G / Brio Satya"
                           value="{{ old('model', $detail?->model) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 13px;">Tahun Pembuatan</label>
                    <input type="number" class="form-control" name="year" placeholder="Contoh: 2023"
                           value="{{ old('year', $detail?->year ?? date('Y')) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 13px;">Nomor Polisi (Plat) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="plate_number" placeholder="Contoh: G 1234 AB"
                           value="{{ old('plate_number', $detail?->plate_number) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 13px;">Jenis Transmisi</label>
                    <select class="form-select" name="transmission">
                        <option value="manual" @selected(old('transmission', $detail?->transmission ?? 'manual') === 'manual')>Manual</option>
                        <option value="automatic" @selected(old('transmission', $detail?->transmission) === 'automatic')>Otomatis (Matic)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 13px;">Jumlah Kursi (Seats) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="seats" placeholder="Contoh: 7"
                           value="{{ old('seats', $detail?->seats ?? 7) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size: 13px;">Uang Deposit Jaminan (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">Rp</span>
                        <input type="number" class="form-control" name="deposit_amount" placeholder="Contoh: 500000"
                               value="{{ old('deposit_amount', $detail?->deposit_amount ?? 0) }}" required>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 11px;">Isi 0 jika tidak memerlukan deposit jaminan.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size: 13px;">Kebijakan Bahan Bakar (BBM)</label>
                    <input type="text" class="form-control" name="fuel_policy" placeholder="Contoh: Same to Same (Kembali sesuai saat serah terima)"
                           value="{{ old('fuel_policy', $detail?->fuel_policy) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" style="font-size: 13px;">Petunjuk Serah Terima / Pengambilan Kendaraan</label>
                    <input type="text" class="form-control" name="pickup_instructions" placeholder="Contoh: Bisa diantar ke Stasiun Tegal atau diambil langsung di garasi mitra."
                           value="{{ old('pickup_instructions', $detail?->pickup_instructions) }}">
                </div>

                <div class="col-12">
                    <div class="p-3 rounded-3 border d-flex flex-wrap align-items-center gap-4" style="background: #f8fafc;">
                        <input type="hidden" name="self_drive_available" value="0">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="self_drive_available" value="1" id="switch_self_drive"
                                   @checked(old('self_drive_available', $detail?->self_drive_available ?? true))>
                            <label class="form-check-label fw-semibold" for="switch_self_drive">
                                <i class="fa-solid fa-key text-primary me-1"></i> Tersedia Lepas Kunci (Self-Drive)
                            </label>
                        </div>

                        <input type="hidden" name="driver_available" value="0">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="driver_available" value="1" id="switch_driver"
                                   @checked(old('driver_available', $detail?->driver_available ?? true))>
                            <label class="form-check-label fw-semibold" for="switch_driver">
                                <i class="fa-solid fa-user-tie text-success me-1"></i> Tersedia Dengan Sopir (With Driver)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 5. FASILITAS TERSEDIA (Untuk Domain yang memiliki Facilities) -->
    @if(isset($facilities) && $facilities->isNotEmpty())
        <div class="form-section-card">
            <div class="form-section-header">
                <div class="form-section-icon" style="background: #f0fdf4; color: #16a34a;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6">Fasilitas & Sarana Pendukung</h5>
                    <small class="text-muted" style="font-size: 12px;">Pilih fasilitas yang tersedia untuk mempermudah kenyamanan pengunjung.</small>
                </div>
            </div>

            <div class="mb-2">
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($facilities as $itemFac)
                        @php($isChecked = in_array($itemFac->id, old('facilities', $editing ? $item->facilities->pluck('id')->all() : [])))
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
    @endif

    <!-- 6. ACTION BUTTONS -->
    <div class="d-flex align-items-center justify-content-between gap-3 pt-2 mb-5">
        <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <button type="submit" class="btn btn-lokantara rounded-pill px-5 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>{{ $editing ? 'Perbarui ' . $title : 'Simpan Draft ' . $title }}</span>
        </button>
    </div>
</form>

@push('scripts')
<script>
    function generateSlug(text) {
        const slugInput = document.getElementById('domain_slug');
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
