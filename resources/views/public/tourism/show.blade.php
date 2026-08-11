@extends('layouts.public')

@section('title', $tourism->name . ' — Jelajah Tegal')
@section('meta-description', str($tourism->description ?: 'Eksplorasi destinasi wisata ' . $tourism->name . ' di Tegal. Dapatkan informasi lokasi, tiket, jam buka, dan ulasan.')->limit(155))
@section('canonical', route('tourism.show', $tourism->slug))

@section('content')
@php
    $coverMedia = $tourism->media->where('pivot.role', 'cover')->first() ?? $tourism->media->first();
    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : null;
    $galleryMedia = $tourism->media->where('pivot.role', 'gallery');
    $lat = $tourism->location?->latitude ?? -6.8730933;
    $lng = $tourism->location?->longitude ?? 109.2541104;
    $dayOfWeek = now()->dayOfWeekIso; // 1 = Monday, 7 = Sunday
    $todayHours = $tourism->operatingHours->where('weekday', $dayOfWeek)->first();
@endphp

<!-- Leaflet.js CSS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<style>
/* Custom Tourism Detail Styles */
.tourism-hero-section {
    position: relative;
    background: linear-gradient(135deg, #0d261e 0%, #174d3c 60%, #1f7a5c 100%);
    color: #ffffff;
    padding: 60px 0 80px;
    overflow: hidden;
}
.tourism-hero-bg {
    position: absolute;
    inset: 0;
    opacity: 0.22;
    background-size: cover;
    background-position: center;
    filter: blur(8px) scale(1.05);
}
.tourism-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(13,38,30,0.6) 0%, rgba(13,38,30,0.95) 100%);
}
.tourism-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.75);
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}
.tourism-breadcrumbs a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: color 0.2s;
}
.tourism-breadcrumbs a:hover {
    color: #f2a93b;
}
.tourism-hero-content {
    position: relative;
    z-index: 2;
}
.tourism-badge-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-bottom: 16px;
}
.hero-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.02em;
    backdrop-filter: blur(8px);
}
.badge-category {
    background: rgba(242,169,59,0.2);
    color: #fbd38d;
    border: 1px solid rgba(242,169,59,0.4);
}
.badge-region {
    background: rgba(45,140,168,0.25);
    color: #90cdf4;
    border: 1px solid rgba(45,140,168,0.4);
}
.badge-featured {
    background: linear-gradient(135deg, #e53e3e, #dd6b20);
    color: #fff;
    box-shadow: 0 4px 12px rgba(229,62,62,0.35);
}
.badge-gem {
    background: linear-gradient(135deg, #805ad5, #d53f8c);
    color: #fff;
    box-shadow: 0 4px 12px rgba(128,90,213,0.35);
}
.tourism-main-title {
    font-size: 40px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    margin: 0 0 16px;
    letter-spacing: -0.02em;
}
.tourism-hero-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 24px;
    font-size: 14px;
    color: rgba(255,255,255,0.9);
}
.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Quick Stats Bar */
.quick-stats-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 24px;
    margin-top: -45px;
    position: relative;
    z-index: 10;
    box-shadow: 0 15px 35px rgba(17,26,24,0.08);
}
.quick-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}
.quick-stat-box {
    display: flex;
    align-items: center;
    gap: 14px;
}
.quick-stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: rgba(31,122,92,0.1);
    color: var(--lokantara-primary);
    display: grid;
    place-items: center;
    font-size: 20px;
    flex-shrink: 0;
}
.quick-stat-info h6 {
    margin: 0;
    font-size: 12px;
    color: var(--lokantara-muted);
    font-weight: 600;
    text-transform: uppercase;
}
.quick-stat-info p {
    margin: 2px 0 0;
    font-size: 14px;
    font-weight: 700;
    color: var(--lokantara-text);
}

/* Section Cards */
.detail-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(17,26,24,0.03);
}
.detail-card-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--lokantara-text);
    margin: 0 0 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.detail-card-title span.title-icon {
    color: var(--lokantara-primary);
    font-size: 22px;
}

/* Map Section Styles */
#tourismMap {
    height: 380px;
    width: 100%;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--lokantara-border);
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    z-index: 1;
}
.map-address-box {
    margin-top: 18px;
    padding: 16px 20px;
    border-radius: 14px;
    background: var(--lokantara-background);
    border: 1px solid var(--lokantara-border);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
}
.map-address-text {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 14px;
    color: var(--lokantara-text);
    flex: 1;
    min-width: 250px;
}
.btn-gmaps {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 12px;
    background: #1a73e8;
    color: #ffffff;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(26,115,232,0.25);
}
.btn-gmaps:hover {
    background: #1557b0;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(26,115,232,0.35);
}

/* Facility Pills */
.facility-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
}
.facility-pill {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    background: var(--lokantara-background);
    border: 1px solid var(--lokantara-border);
    font-size: 13px;
    font-weight: 600;
    color: var(--lokantara-text);
}

/* Operating Hours Table */
.schedule-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
}
.schedule-row {
    background: var(--lokantara-background);
    border-radius: 10px;
    transition: all 0.2s;
}
.schedule-row.is-today {
    background: rgba(31,122,92,0.08);
    border: 1px solid var(--lokantara-primary);
}
.schedule-row td {
    padding: 12px 16px;
    font-size: 13px;
    color: var(--lokantara-text);
}
.schedule-row td:first-child {
    border-radius: 10px 0 0 10px;
    font-weight: 700;
}
.schedule-row td:last-child {
    border-radius: 0 10px 10px 0;
    text-align: right;
}

/* Ticket Offers Card */
.ticket-offer-item {
    padding: 18px;
    border-radius: 14px;
    background: var(--lokantara-background);
    border: 1px solid var(--lokantara-border);
    margin-bottom: 14px;
    transition: all 0.2s;
}
.ticket-offer-item:hover {
    border-color: var(--lokantara-primary);
    box-shadow: 0 6px 18px rgba(31,122,92,0.1);
}
.ticket-offer-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
}
.ticket-name {
    font-size: 15px;
    font-weight: 800;
    color: var(--lokantara-text);
    margin: 0;
}
.ticket-price {
    font-size: 18px;
    font-weight: 800;
    color: var(--lokantara-primary);
    white-space: nowrap;
}

/* Gallery Grid */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 12px;
    margin-top: 14px;
}
.gallery-thumb {
    height: 100px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--lokantara-border);
}
.gallery-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.gallery-thumb img:hover {
    transform: scale(1.08);
}
</style>

<!-- Hero Banner Header -->
<section class="tourism-hero-section">
    @if ($coverUrl)
        <div class="tourism-hero-bg" style="background-image: url('{{ $coverUrl }}');"></div>
    @endif
    <div class="tourism-hero-overlay"></div>

    <div class="container public-container tourism-hero-content">
        <!-- Breadcrumbs -->
        <nav class="tourism-breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('tourism.index') }}">Wisata</a>
            <span>/</span>
            <span>{{ $tourism->region?->name ?? 'Tegal' }}</span>
            <span>/</span>
            <span class="text-white fw-semibold">{{ $tourism->name }}</span>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <!-- Badges -->
                <div class="tourism-badge-row">
                    @if ($tourism->category)
                        <span class="hero-badge-pill badge-category">🏷️ {{ $tourism->category->name }}</span>
                    @endif
                    @if ($tourism->region)
                        <span class="hero-badge-pill badge-region">📍 {{ $tourism->region->name }}</span>
                    @endif
                    @if ($tourism->is_featured)
                        <span class="hero-badge-pill badge-featured">⭐ Pilihan Wisata</span>
                    @endif
                    @if ($tourism->tourism?->is_hidden_gem)
                        <span class="hero-badge-pill badge-gem">💎 Hidden Gem Tegal</span>
                    @endif
                    @if ($tourism->tourism?->badge)
                        <span class="hero-badge-pill badge-category">✨ {{ $tourism->tourism->badge }}</span>
                    @endif
                </div>

                <!-- Main Title -->
                <h1 class="tourism-main-title">{{ $tourism->name }}</h1>

                <!-- Meta Details -->
                <div class="tourism-hero-meta">
                    @if ($tourism->rating_count > 0)
                        <div class="meta-item">
                            <span class="text-warning">★</span>
                            <strong>{{ number_format($tourism->rating_average, 1) }}</strong>
                            <span class="text-white-50">({{ $tourism->rating_count }} ulasan)</span>
                        </div>
                    @endif
                    <div class="meta-item">
                        <span>🏢</span>
                        <span>Dikelola oleh <strong>{{ $tourism->mitra->display_name }}</strong></span>
                    </div>
                    @if ($tourism->tourism?->destination_type)
                        <div class="meta-item">
                            <span>🏖️</span>
                            <span>Kategori: <strong>{{ str($tourism->tourism->destination_type)->headline() }}</strong></span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cover Image Preview -->
            @if ($coverUrl)
                <div class="col-lg-4 text-center">
                    <div style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4); border: 3px solid rgba(255,255,255,0.2); max-height: 260px;">
                        <img src="{{ $coverUrl }}" alt="{{ $tourism->name }}" style="width: 100%; height: 260px; object-fit: cover;">
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Quick Stats Bar -->
<div class="container public-container">
    <div class="quick-stats-card">
        <div class="quick-stats-grid">
            <div class="quick-stat-box">
                <div class="quick-stat-icon">⏰</div>
                <div class="quick-stat-info">
                    <h6>Jam Operasional</h6>
                    <p>
                        @if ($todayHours)
                            {{ substr($todayHours->opens_at, 0, 5) }} - {{ substr($todayHours->closes_at, 0, 5) }} WIB
                        @else
                            Setiap Hari (Buka)
                        @endif
                    </p>
                </div>
            </div>
            <div class="quick-stat-box">
                <div class="quick-stat-icon">⏱️</div>
                <div class="quick-stat-info">
                    <h6>Estimasi Kunjungan</h6>
                    <p>{{ $tourism->tourism?->visit_duration_minutes ?? '120' }} Menit</p>
                </div>
            </div>
            <div class="quick-stat-box">
                <div class="quick-stat-icon">📍</div>
                <div class="quick-stat-info">
                    <h6>Wilayah Destinasi</h6>
                    <p>{{ $tourism->region?->name ?? 'Kabupaten Tegal' }}</p>
                </div>
            </div>
            <div class="quick-stat-box">
                <div class="quick-stat-icon">🛡️</div>
                <div class="quick-stat-info">
                    <h6>Status Destinasi</h6>
                    <p class="text-success">Terverifikasi Resmi</p>
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
                <div class="detail-card">
                    <h2 class="detail-card-title"><span class="title-icon">📖</span> Tentang Destinasi</h2>
                    <div style="font-size: 15px; line-height: 1.8; color: var(--lokantara-text);">
                        {!! nl2br(e($tourism->description ?: 'Nikmati keindahan dan pesona lokal di ' . $tourism->name . ', salah satu destinasi wisata unggulan di wilayah Tegal yang menawarkan pengalaman tak terlupakan.')) !!}
                    </div>

                    <!-- Gallery Photos if available -->
                    @if ($galleryMedia->isNotEmpty())
                        <hr class="my-4">
                        <h3 class="fs-6 fw-bold mb-2">Galeri Foto Destinasi</h3>
                        <div class="gallery-grid">
                            @foreach ($galleryMedia as $media)
                                <div class="gallery-thumb">
                                    <img src="{{ asset('storage/' . $media->object_key) }}" alt="Galeri {{ $tourism->name }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Interactive Map Card -->
                <div class="detail-card">
                    <h2 class="detail-card-title"><span class="title-icon">🗺️</span> Lokasi & Peta Interaktif</h2>
                    
                    <!-- Leaflet Map Container -->
                    <div id="tourismMap"></div>

                    <!-- Address & Direct Google Maps Action -->
                    <div class="map-address-box">
                        <div class="map-address-text">
                            <span class="fs-5">📌</span>
                            <div>
                                <strong>Alamat Lengkap:</strong>
                                <div>{{ $tourism->address ?: 'Kawasan Wisata ' . $tourism->name . ', ' . ($tourism->region?->name ?? 'Tegal') }}</div>
                                <small class="text-muted">Koordinat GPS: {{ number_format($lat, 6) }}, {{ number_format($lng, 6) }}</small>
                            </div>
                        </div>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lat }},{{ $lng }}" target="_blank" rel="noopener noreferrer" class="btn-gmaps">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
                            </svg>
                            Buka di Google Maps
                        </a>
                    </div>
                </div>

                <!-- Facilities Card -->
                <div class="detail-card">
                    <h2 class="detail-card-title"><span class="title-icon">✨</span> Fasilitas Tersedia</h2>
                    @if ($tourism->facilities->isNotEmpty())
                        <div class="facility-grid">
                            @foreach ($tourism->facilities as $facility)
                                <div class="facility-pill">
                                    <span>✔</span>
                                    <span>{{ $facility->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Fasilitas umum: Parkir Kendaraan, Musholla, Toilet, Spot Foto, dan Warung Makan.</p>
                    @endif
                </div>

                <!-- Operating Hours Schedule Card -->
                @if ($tourism->operatingHours->isNotEmpty())
                    <div class="detail-card">
                        <h2 class="detail-card-title"><span class="title-icon">📅</span> Jadwal Jam Operasional Mingguan</h2>
                        <table class="schedule-table">
                            <tbody>
                                @php
                                    $dayNames = [
                                        1 => 'Senin',
                                        2 => 'Selasa',
                                        3 => 'Rabu',
                                        4 => 'Kamis',
                                        5 => 'Jumat',
                                        6 => 'Sabtu',
                                        7 => 'Minggu',
                                    ];
                                @endphp
                                @foreach ($tourism->operatingHours->sortBy('weekday') as $hour)
                                    @php $isToday = $hour->weekday == $dayOfWeek; @endphp
                                    <tr class="schedule-row {{ $isToday ? 'is-today' : '' }}">
                                        <td>
                                            {{ $dayNames[$hour->weekday] ?? 'Hari ' . $hour->weekday }}
                                            @if ($isToday)
                                                <span class="badge bg-success text-white ms-2 px-2 py-1" style="font-size: 10px;">Hari Ini</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($hour->is_closed)
                                                <span class="text-danger fw-bold">Tutup</span>
                                            @else
                                                <span class="fw-semibold">{{ substr($hour->opens_at, 0, 5) }} - {{ substr($hour->closes_at, 0, 5) }} WIB</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Visitor Reviews Card -->
                <div class="detail-card">
                    <h2 class="detail-card-title"><span class="title-icon">💬</span> Ulasan & Testimoni Pengunjung</h2>
                    
                    @forelse ($tourism->reviews as $review)
                        <div class="p-3 mb-3 rounded-3" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <strong class="fs-6">{{ $review->user?->name ?? 'Pengunjung Jelajah Tegal' }}</strong>
                                    <div class="text-warning" style="font-size: 13px;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                        <span class="text-dark fw-bold ms-1">{{ $review->rating }}/5</span>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $review->created_at?->diffForHumans() }}</small>
                            </div>
                            @if ($review->title)
                                <h6 class="fw-bold mb-1">{{ $review->title }}</h6>
                            @endif
                            <p class="mb-0 text-muted" style="font-size: 14px;">{{ $review->body }}</p>
                        </div>
                    @empty
                        <x-empty-state title="Belum ada ulasan publik" description="Jadilah pengunjung pertama yang membagikan pengalaman seru berwisata di sini." compact />
                    @endforelse
                </div>
            </div>

            <!-- Right Sidebar Column (4 Cols) -->
            <div class="col-lg-4">
                <!-- Ticket Booking Card -->
                <div class="detail-card" style="position: sticky; top: 90px;">
                    <h2 class="detail-card-title"><span class="title-icon">🎟️</span> Paket Tiket Masuk</h2>
                    
                    @forelse ($tourism->offers->where('status', 'active') as $offer)
                        <div class="ticket-offer-item">
                            <div class="ticket-offer-header">
                                <h3 class="ticket-name">{{ $offer->name }}</h3>
                                <div class="ticket-price">Rp {{ number_format($offer->price, 0, ',', '.') }}</div>
                            </div>
                            <p class="text-muted mb-3" style="font-size: 12px;">{{ $offer->description ?: 'Tiket akses masuk destinasi wisata per orang/kunjungan.' }}</p>
                            
                            @auth
                                <a href="{{ route('orders.index') }}" class="btn btn-lokantara w-100 fw-bold py-2">
                                    Pesan Tiket Sekarang
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-lokantara w-100 fw-bold py-2">
                                    Masuk untuk Memesan
                                </a>
                            @endauth
                        </div>
                    @empty
                        <div class="p-3 mb-3 rounded-3 text-center" style="background: var(--lokantara-background);">
                            <p class="text-muted mb-0" style="font-size: 14px;">Tiket dapat dibeli langsung di loket masuk lokasi wisata.</p>
                        </div>
                    @endforelse

                    <!-- Actions & Favorite -->
                    <div class="d-flex flex-column gap-2 mt-3">
                        @auth
                            <form method="POST" action="{{ route('tourism.favorite', $tourism->slug) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-lokantara w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                                    <span>❤️</span> Simpan ke Favorit
                                </button>
                            </form>
                        @endauth
                    </div>

                    <!-- Mitra / Organiser Card -->
                    <hr class="my-4">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, var(--lokantara-primary), var(--lokantara-accent)); color: #fff; display: grid; place-items: center; font-weight: 800; font-size: 18px;">
                            {{ substr($tourism->mitra->display_name, 0, 1) }}
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Mitra Pengelola</small>
                            <strong class="fs-6">{{ $tourism->mitra->display_name }}</strong>
                            <div class="text-success" style="font-size: 12px;">✔ Mitra Terverifikasi</div>
                        </div>
                    </div>

                    <!-- Review Submission Form for Authenticated Users -->
                    @auth
                        <hr class="my-4">
                        <h3 class="fs-6 fw-bold mb-3">Tulis Ulasan Pengalaman</h3>
                        <form method="POST" action="{{ route('tourism.reviews.store', $tourism->slug) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-semibold">Rating Bintang (1 - 5)</label>
                                <select class="form-select" name="rating" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Memuaskan)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 - Bagus)</option>
                                    <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                                    <option value="2">⭐⭐ (2 - Kurang)</option>
                                    <option value="1">⭐ (1 - Buruk)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-semibold">Judul Ulasan (Opsional)</label>
                                <input class="form-control" type="text" name="title" placeholder="Contoh: Pantai Bersih dan Seru">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-semibold">Ceritakan Pengalaman Anda</label>
                                <textarea class="form-control" name="body" rows="3" placeholder="Bagikan tips atau pengalaman seru berwisata di sini..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-lokantara w-100 fw-bold py-2">
                                Kirim Ulasan Saya
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leaflet.js Interactive Map Script -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $lat }};
    const lng = {{ $lng }};
    const destinationTitle = "{{ addslashes($tourism->name) }}";
    const address = "{{ addslashes($tourism->address ?: 'Kawasan Wisata ' . $tourism->name) }}";

    // Initialize Leaflet Map
    const map = L.map('tourismMap', {
        center: [lat, lng],
        zoom: 15,
        zoomControl: true,
        scrollWheelZoom: false
    });

    // Add Tile Layer (OpenStreetMap CartoDB Voyager for clean, modern look)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // Custom Marker Icon
    const customIcon = L.divIcon({
        className: 'custom-map-pin',
        html: `
            <div style="
                background: linear-gradient(135deg, #1f7a5c, #13352c);
                width: 38px;
                height: 38px;
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                border: 3px solid #ffffff;
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <span style="transform: rotate(45deg); font-size: 16px; color: #ffffff;">🏖️</span>
            </div>
        `,
        iconSize: [38, 38],
        iconAnchor: [19, 38],
        popupAnchor: [0, -38]
    });

    // Add Marker & Popup
    const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
    
    const popupContent = `
        <div style="font-family: inherit; padding: 4px;">
            <strong style="font-size: 14px; color: #174d3c; display: block; margin-bottom: 4px;">${destinationTitle}</strong>
            <p style="font-size: 12px; color: #4a5568; margin: 0 0 8px;">${address}</p>
            <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" style="
                display: inline-block;
                background: #1f7a5c;
                color: #ffffff;
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 11px;
                font-weight: bold;
                text-decoration: none;
            ">Petunjuk Arah &rarr;</a>
        </div>
    `;

    marker.bindPopup(popupContent).openPopup();

    // Invalidate map size after render to prevent tile clipping
    setTimeout(() => {
        map.invalidateSize();
    }, 400);
});
</script>
@endsection
