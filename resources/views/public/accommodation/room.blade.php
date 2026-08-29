@extends('layouts.public')

@section('title', $room->name . ' — ' . $accommodation->name . ' — Jelajah Tegal')
@section('meta-description', str($room->description ?: 'Detail fasilitas dan harga sewa kamar ' . $room->name . ' di ' . $accommodation->name . ', Tegal.')->limit(155))
@section('canonical', route('accommodation.rooms.show', [$accommodation->slug, $room]))

@section('content')
@php
    $coverMedia = $room->media->first() ?? $accommodation->media->first();
    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : null;
    $galleryMedia = $room->media;
@endphp

<style>
/* Room Detail Header */
.room-hero-section {
    position: relative;
    background: linear-gradient(135deg, #0d261e 0%, #154737 55%, #1b634b 100%);
    color: #ffffff;
    padding: 50px 0 70px;
    overflow: hidden;
}
.room-hero-bg {
    position: absolute;
    inset: 0;
    opacity: 0.2;
    background-size: cover;
    background-position: center;
    filter: blur(8px) scale(1.05);
}
.room-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(13,38,30,0.65) 0%, rgba(13,38,30,0.96) 100%);
}
.room-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.75);
    margin-bottom: 16px;
    position: relative;
    z-index: 2;
}
.room-breadcrumbs a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: color 0.2s;
}
.room-breadcrumbs a:hover {
    color: #f2a93b;
}
.room-hero-content {
    position: relative;
    z-index: 2;
}
.room-main-title {
    font-size: 38px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 12px;
    letter-spacing: -0.02em;
}

/* Quick Room Highlights */
.room-stats-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 24px;
    margin-top: -45px;
    position: relative;
    z-index: 10;
    box-shadow: 0 15px 35px rgba(17,26,24,0.08);
}
.room-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 18px;
}
.room-stat-box {
    display: flex;
    align-items: center;
    gap: 12px;
}
.room-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(31,122,92,0.1);
    color: var(--lokantara-primary);
    display: grid;
    place-items: center;
    font-size: 18px;
    flex-shrink: 0;
}
.room-stat-info h6 {
    margin: 0;
    font-size: 11px;
    color: var(--lokantara-muted);
    font-weight: 600;
    text-transform: uppercase;
}
.room-stat-info p {
    margin: 2px 0 0;
    font-size: 14px;
    font-weight: 700;
    color: var(--lokantara-text);
}

/* Detail Section Cards */
.room-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(17,26,24,0.03);
}
.room-card-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--lokantara-text);
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Facility Grid */
.room-facility-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
}
.room-facility-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 10px;
    background: var(--lokantara-background);
    border: 1px solid var(--lokantara-border);
    font-size: 13px;
    font-weight: 600;
    color: var(--lokantara-text);
}
</style>

<!-- Hero Banner Header -->
<section class="room-hero-section">
    @if ($coverUrl)
        <div class="room-hero-bg" style="background-image: url('{{ $coverUrl }}');"></div>
    @endif
    <div class="room-hero-overlay"></div>

    <div class="container public-container room-hero-content">
        <!-- Breadcrumbs -->
        <nav class="room-breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('accommodation.index') }}">Penginapan</a>
            <span>/</span>
            <a href="{{ route('accommodation.show', $accommodation->slug) }}">{{ $accommodation->name }}</a>
            <span>/</span>
            <span class="text-white fw-semibold">{{ $room->name }}</span>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <!-- Badges -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge" style="background: rgba(242,169,59,0.25); color: #fbd38d; border: 1px solid rgba(242,169,59,0.4); padding: 6px 12px; border-radius: 99px;">
                        <i class="fa-solid fa-bed text-info me-1"></i> Tipe Kamar
                    </span>
                    <span class="badge" style="background: rgba(45,140,168,0.25); color: #90cdf4; border: 1px solid rgba(45,140,168,0.4); padding: 6px 12px; border-radius: 99px;">
                        <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $accommodation->region?->name ?? 'Tegal' }}
                    </span>
                </div>

                <!-- Main Title -->
                <h1 class="room-main-title">Kamar {{ $room->name }}</h1>
                <p class="mb-0 fs-6 text-white-50">
                    Bagian dari penginapan resmi <strong><a href="{{ route('accommodation.show', $accommodation->slug) }}" class="text-white text-decoration-underline">{{ $accommodation->name }}</a></strong>
                </p>
            </div>

            <!-- Cover Image Preview -->
            @if ($coverUrl)
                <div class="col-lg-4 text-center">
                    <div style="border-radius: 18px; overflow: hidden; box-shadow: 0 16px 36px rgba(0,0,0,0.4); border: 2px solid rgba(255,255,255,0.2); max-height: 220px;">
                        <img src="{{ $coverUrl }}" alt="Kamar {{ $room->name }}" style="width: 100%; height: 220px; object-fit: cover;">
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Quick Stats Bar -->
<div class="container public-container">
    <div class="room-stats-card">
        <div class="room-stats-grid">
            <div class="room-stat-box">
                <div class="room-stat-icon">👥</div>
                <div class="room-stat-info">
                    <h6>Kapasitas Tamu</h6>
                    <p>{{ $room->capacity_adults }} Dewasa, {{ $room->capacity_children }} Anak</p>
                </div>
            </div>
            <div class="room-stat-box">
                <div class="room-stat-icon">🛏️</div>
                <div class="room-stat-info">
                    <h6>Tipe Ranjang</h6>
                    <p>{{ $room->bed_type ?: 'Double Bed Nyaman' }}</p>
                </div>
            </div>
            <div class="room-stat-box">
                <div class="room-stat-icon">📐</div>
                <div class="room-stat-info">
                    <h6>Luas Ruangan</h6>
                    <p>{{ $room->room_size_sqm ? $room->room_size_sqm . ' m²' : 'Standar Nyaman' }}</p>
                </div>
            </div>
            <div class="room-stat-box">
                <div class="room-stat-icon">🏢</div>
                <div class="room-stat-info">
                    <h6>Ketersediaan</h6>
                    <p class="text-success">{{ $room->total_units }} Unit Kamar</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content & Sidebar Grid -->
<section class="public-section pt-4">
    <div class="container public-container">
        <div class="row g-4">
            <!-- Left Main Column (8 Cols) -->
            <div class="col-lg-8">
                <!-- Description Card -->
                <div class="room-card">
                    <h2 class="room-card-title"><span class="fs-5">📖</span> Deskripsi & Suasana Kamar</h2>
                    <div style="font-size: 15px; line-height: 1.8; color: var(--lokantara-text);">
                        {!! nl2br(e($room->description ?: 'Nikmati kenyamanan istirahat maksimal di kamar ' . $room->name . '. Didesain dengan tata ruang yang bersih, kasur empuk berkualitas, dan pencahayaan hangat untuk memastikan pengalaman menginap Anda di Tegal terasa menyenangkan.')) !!}
                    </div>

                    <!-- Gallery Photos if available -->
                    @if ($galleryMedia->isNotEmpty())
                        <hr class="my-4">
                        <h3 class="fs-6 fw-bold mb-2">Foto Kamar</h3>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach ($galleryMedia as $media)
                                <div style="width: 140px; height: 100px; border-radius: 12px; overflow: hidden; border: 1px solid var(--lokantara-border);">
                                    <img src="{{ asset('storage/' . $media->object_key) }}" alt="Foto Kamar {{ $room->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Facilities Card -->
                <div class="room-card">
                    <h2 class="room-card-title"><span class="fs-5"><i class="fa-solid fa-wand-magic-sparkles text-warning"></i></span> Fasilitas Kamar</h2>
                    @if ($room->facilities->isNotEmpty())
                        <div class="room-facility-grid">
                            @foreach ($room->facilities as $facility)
                                <div class="room-facility-pill">
                                    <span class="text-success">✔</span>
                                    <span>{{ $facility->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="room-facility-grid">
                            <div class="room-facility-pill"><span class="text-success">✔</span><span>Pendingin Ruangan (AC)</span></div>
                            <div class="room-facility-pill"><span class="text-success">✔</span><span>Akses Wi-Fi Cepat</span></div>
                            <div class="room-facility-pill"><span class="text-success">✔</span><span>Kamar Mandi Dalam & Shower</span></div>
                            <div class="room-facility-pill"><span class="text-success">✔</span><span>Perlengkapan Mandi Gratis</span></div>
                            <div class="room-facility-pill"><span class="text-success">✔</span><span>Air Mineral Botol</span></div>
                            <div class="room-facility-pill"><span class="text-success">✔</span><span>Meja & Kursi Kerja</span></div>
                        </div>
                    @endif
                </div>

                <!-- Policies Card -->
                <div class="room-card">
                    <h2 class="room-card-title"><span class="fs-5">📜</span> Kebijakan & Ketentuan Menginap</h2>
                    <div class="p-3 rounded-3" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                        <ul class="mb-0 ps-3" style="font-size: 14px; line-height: 1.8; color: var(--lokantara-text);">
                            <li><strong>Durasi Menginap Minimum:</strong> {{ $room->min_stay_nights ?? 1 }} malam.</li>
                            @if ($room->max_stay_nights)
                                <li><strong>Durasi Menginap Maksimum:</strong> {{ $room->max_stay_nights }} malam.</li>
                            @endif
                            @if ($room->advance_booking_days !== null)
                                <li><strong>Pemesanan di Muka:</strong> Dapat dipesan hingga {{ $room->advance_booking_days }} hari sebelumnya.</li>
                            @endif
                            <li><strong>Waktu Check-In:</strong> {{ $accommodation->accommodation->check_in_time ? substr($accommodation->accommodation->check_in_time, 0, 5) . ' WIB' : '14:00 WIB' }}.</li>
                            <li><strong>Waktu Check-Out:</strong> {{ $accommodation->accommodation->check_out_time ? substr($accommodation->accommodation->check_out_time, 0, 5) . ' WIB' : '12:00 WIB' }}.</li>
                            <li>Dilarang merokok di dalam kamar non-smoking dan dilarang membawa hewan peliharaan tanpa izin.</li>
                        </ul>
                    </div>
                </div>

                <!-- Upcoming Availabilities Table -->
                @if ($room->offer && $room->offer->availabilities->isNotEmpty())
                    <div class="room-card">
                        <h2 class="room-card-title"><span class="fs-5"><i class="fa-regular fa-calendar-days text-primary"></i></span> Jadwal Ketersediaan Mendatang</h2>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal Menginap</th>
                                        <th>Status Ketersediaan</th>
                                        <th>Sisa Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($room->offer->availabilities->where('service_date', '>=', today())->take(10) as $row)
                                        <tr>
                                            <td class="fw-bold">{{ $row->service_date->translatedFormat('d F Y') }}</td>
                                            <td>
                                                @if ($row->status === 'available')
                                                    <span class="badge bg-success">Tersedia</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ str($row->status)->headline() }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-semibold">
                                                {{ max(0, $row->capacity - $row->reserved_quantity) }} Unit Tersisa
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar Column (4 Cols) -->
            <div class="col-lg-4">
                <!-- Booking Price Card -->
                <div class="room-card" style="position: sticky; top: 90px;">
                    <div class="d-flex align-items-baseline justify-content-between mb-3">
                        <span class="text-muted fs-7">Harga Sewa</span>
                        <div class="text-end">
                            <span class="fs-3 fw-bold" style="color: var(--lokantara-primary);">
                                Rp {{ number_format($room->offer->price, 0, ',', '.') }}
                            </span>
                            <small class="text-muted d-block" style="font-size: 11px;">per kamar / malam</small>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 mb-4" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                        <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 13px;">
                            <span class="text-success">✔</span>
                            <span>Konfirmasi Instan</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 13px;">
                            <span class="text-success">✔</span>
                            <span>Bebas Biaya Pemesanan</span>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="font-size: 13px;">
                            <span class="text-success">✔</span>
                            <span>Jaminan Kamar Bersih & Nyaman</span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    @auth
                        <a href="{{ route('consumer.orders.index') }}" class="btn btn-lokantara w-100 fw-bold py-3 mb-2 fs-6">
                            Pesan Kamar Ini Sekarang
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-lokantara w-100 fw-bold py-3 mb-2 fs-6">
                            Masuk untuk Memesan Kamar
                        </a>
                    @endauth

                    <a href="{{ route('accommodation.show', $accommodation->slug) }}" class="btn btn-outline-secondary w-100 fw-semibold py-2 fs-7">
                        &larr; Kembali ke Rincian Hotel
                    </a>

                    <!-- Mitra / Organiser Card -->
                    <hr class="my-4">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, var(--lokantara-primary), var(--lokantara-accent)); color: #fff; display: grid; place-items: center; font-weight: 800; font-size: 17px;">
                            {{ substr($accommodation->mitra->display_name, 0, 1) }}
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Pengelola Penginapan</small>
                            <strong class="fs-6">{{ $accommodation->mitra->display_name }}</strong>
                            <div class="text-success" style="font-size: 12px;">✔ Mitra Terverifikasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
