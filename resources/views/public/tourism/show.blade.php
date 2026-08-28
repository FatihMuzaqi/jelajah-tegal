@extends('layouts.public')

@section('title', $tourism->name . ' — Jelajah Tegal')
@section('meta-description', str($tourism->description ?: 'Eksplorasi destinasi wisata ' . $tourism->name . ' di Tegal. Dapatkan informasi lokasi, tiket, jam buka, dan ulasan.')->limit(155))
@section('canonical', route('tourism.show', $tourism->slug))

@php
    $coverMedia = $tourism->media->where('pivot.role', 'cover')->first() ?? $tourism->media->first();
    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : null;
    $galleryMedia = $tourism->media->where('pivot.role', 'gallery');
    $lat = $tourism->location?->latitude ?? -6.8730933;
    $lng = $tourism->location?->longitude ?? 109.2541104;
    $dayOfWeek = now()->dayOfWeekIso; // 1 = Monday, 7 = Sunday
    $todayHours = $tourism->operatingHours->where('weekday', $dayOfWeek)->first();
@endphp

@if($coverUrl)
@push('head-extra')
<link rel="preload" as="image" href="{{ $coverUrl }}" fetchpriority="high">
@endpush
@endif

@section('content')


<style>
/* Custom Tourism Detail Styles */
.tourism-hero-section {
    position: relative;
    background: linear-gradient(135deg, #0d261e 0%, #174d3c 60%, #1f7a5c 100%);
    color: #ffffff;
    padding: clamp(35px, 6vw, 65px) 0 clamp(45px, 7vw, 85px);
    overflow: hidden;
}
.tourism-hero-bg {
    position: absolute;
    inset: 0;
    overflow: hidden;
}
/* Real img-based LCP element replacing CSS background */
.tourism-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    opacity: 0.22;
    filter: blur(8px) scale(1.05);
    transform: scale(1.05);
}
.tourism-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(13,38,30,0.6) 0%, rgba(13,38,30,0.95) 100%);
}
.tourism-breadcrumbs {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
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
    gap: 8px;
    align-items: center;
    margin-bottom: 16px;
}
.hero-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
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
    font-size: clamp(26px, 5.5vw, 42px);
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
    gap: 16px;
    font-size: 13px;
    color: rgba(255,255,255,0.9);
}
.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Quick Stats Bar */
.quick-stats-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: clamp(16px, 3vw, 24px);
    margin-top: -35px;
    position: relative;
    z-index: 10;
    box-shadow: 0 15px 35px rgba(17,26,24,0.08);
}
.quick-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 14px;
}
.quick-stat-box {
    display: flex;
    align-items: center;
    gap: 12px;
}
@media (max-width: 576px) {
    .quick-stats-card {
        margin-top: -20px;
    }
    .detail-card {
        padding: 20px 16px !important;
    }
    #tourismMap {
        height: 260px !important;
    }
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
        <div class="tourism-hero-bg">
            <img
                src="{{ $coverUrl }}"
                alt="{{ $tourism->name }}"
                width="1280" height="720"
                fetchpriority="high"
                decoding="sync"
                loading="eager"
            >
        </div>
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
                        <span class="hero-badge-pill badge-category"><i class="fa-solid fa-tag me-1"></i> {{ $tourism->category->name }}</span>
                    @endif
                    @if ($tourism->region)
                        <span class="hero-badge-pill badge-region"><i class="fa-solid fa-location-dot me-1"></i> {{ $tourism->region->name }}</span>
                    @endif
                    @if ($tourism->is_featured)
                        <span class="hero-badge-pill badge-featured"><i class="fa-solid fa-star me-1 text-warning"></i> Pilihan Wisata</span>
                    @endif
                    @if ($tourism->tourism?->is_hidden_gem)
                        <span class="hero-badge-pill badge-gem"><i class="fa-solid fa-gem me-1 text-info"></i> Hidden Gem Tegal</span>
                    @endif
                    @if ($tourism->tourism?->badge)
                        <span class="hero-badge-pill badge-category"><i class="fa-solid fa-wand-magic-sparkles me-1 text-warning"></i> {{ $tourism->tourism->badge }}</span>
                    @endif
                </div>

                <!-- Main Title -->
                <h1 class="tourism-main-title">{{ $tourism->name }}</h1>

                <!-- Meta Details -->
                <div class="tourism-hero-meta">
                    @if ($tourism->rating_count > 0)
                        <div class="meta-item">
                            <span class="text-warning"><i class="fa-solid fa-star"></i></span>
                            <strong>{{ number_format($tourism->rating_average, 1) }}</strong>
                            <span class="text-white-50">({{ $tourism->rating_count }} ulasan)</span>
                        </div>
                    @endif
                    <div class="meta-item">
                        <i class="fa-solid fa-building text-info"></i>
                        <span>Dikelola oleh <strong>{{ $tourism->mitra->display_name }}</strong></span>
                    </div>
                    @if ($tourism->tourism?->destination_type)
                        <div class="meta-item">
                            <i class="fa-solid fa-umbrella-beach text-warning"></i>
                            <span>Kategori: <strong>{{ str($tourism->tourism->destination_type)->headline() }}</strong></span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cover Image Preview -->
            @if ($coverUrl)
                <div class="col-lg-4 text-center">
                    <div style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4); border: 3px solid rgba(255,255,255,0.2); max-height: 260px;">
                        <img src="{{ $coverUrl }}" alt="{{ $tourism->name }}" style="width: 100%; height: 260px; object-fit: cover;" loading="lazy" decoding="async" width="600" height="260">
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
                <div class="quick-stat-icon"><i class="fa-solid fa-clock text-primary"></i></div>
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
                <div class="quick-stat-icon"><i class="fa-solid fa-hourglass-half text-warning"></i></div>
                <div class="quick-stat-info">
                    <h6>Estimasi Kunjungan</h6>
                    <p>{{ $tourism->tourism?->visit_duration_minutes ?? '120' }} Menit</p>
                </div>
            </div>
            <div class="quick-stat-box">
                <div class="quick-stat-icon"><i class="fa-solid fa-location-dot text-danger"></i></div>
                <div class="quick-stat-info">
                    <h6>Wilayah Destinasi</h6>
                    <p>{{ $tourism->region?->name ?? 'Kabupaten Tegal' }}</p>
                </div>
            </div>
            <div class="quick-stat-box">
                <div class="quick-stat-icon"><i class="fa-solid fa-shield-halved text-success"></i></div>
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
        @php
            $activeOffersList = $tourism->offers->whereIn('status', ['active', 'published']);
            $allOffersSoldOutToday = $activeOffersList->isNotEmpty() && $activeOffersList->every(function($offer) {
                $todayAvail = $offer->availabilities->where('service_date', now()->format('Y-m-d'))->first();
                $capacity = $todayAvail?->capacity ?? ($offer->ticketPackage?->quota_per_day ?? 100);
                $reserved = $todayAvail?->reserved_quantity ?? 0;
                return ($todayAvail && $todayAvail->status !== 'available') || (max(0, $capacity - $reserved) <= 0);
            });
        @endphp

        @if ($allOffersSoldOutToday)
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3.5 mb-4 d-flex align-items-center gap-3" role="alert">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 20px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-danger">Pemberitahuan: Kuota Tiket Masuk Hari Ini Telah Habis (Sold Out)</h6>
                    <p class="mb-0 text-muted fs-8">Seluruh tiket masuk untuk hari ini telah terjual habis. Anda tetap dapat melakukan pemesanan tiket untuk tanggal kunjungan berikutnya melalui tombol pemesanan di bawah.</p>
                </div>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Main Column (8 Cols) -->
            <div class="col-lg-8">
                <!-- Description Card -->
                <div class="detail-card">
                    <h2 class="detail-card-title"><span class="title-icon"><i class="fa-solid fa-book-open text-emerald"></i></span> Tentang Destinasi</h2>
                    <div style="font-size: 15px; line-height: 1.8; color: var(--lokantara-text);">
                        {!! nl2br(e($tourism->description ?: 'Nikmati keindahan dan pesona lokal di ' . $tourism->name . ', salah satu destinasi wisata unggulan di wilayah Tegal yang menawarkan pengalaman tak terlupakan.')) !!}
                    </div>

                    <!-- Gallery Photos if available -->
                    @if ($galleryMedia->isNotEmpty())
                        <hr class="my-4">
                        <h3 class="fs-6 fw-bold mb-2"><i class="fa-solid fa-images text-primary me-2"></i>Galeri Foto Destinasi</h3>
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
                    <h2 class="detail-card-title"><span class="title-icon"><i class="fa-solid fa-map-location-dot text-info"></i></span> Lokasi & Peta Interaktif</h2>
                    
                    <!-- Leaflet Map Container -->
                    <div id="tourismMap" style="height: 380px; width: 100%; border-radius: 16px; overflow: hidden; margin-bottom: 20px; background: #e9ecef; z-index: 1; border: 1px solid var(--lokantara-border);"></div>

                    <!-- Address & Direct Google Maps Action -->
                    <div class="map-address-box">
                        <div class="map-address-text">
                            <span class="fs-5 text-danger"><i class="fa-solid fa-location-dot"></i></span>
                            <div>
                                <strong>Alamat Lengkap:</strong>
                                <div>{{ $tourism->address ?: 'Kawasan Wisata ' . $tourism->name . ', ' . ($tourism->region?->name ?? 'Tegal') }}</div>
                                <small class="text-muted">Koordinat GPS: {{ number_format($lat, 6) }}, {{ number_format($lng, 6) }}</small>
                            </div>
                        </div>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lat }},{{ $lng }}" target="_blank" rel="noopener noreferrer" class="btn-gmaps">
                            <i class="fa-solid fa-diamond-turn-right me-1"></i>
                            Buka di Google Maps
                        </a>
                    </div>
                </div>

                <!-- Facilities Card -->
                <div class="detail-card">
                    <h2 class="detail-card-title"><span class="title-icon"><i class="fa-solid fa-wand-magic-sparkles text-warning"></i></span> Fasilitas Tersedia</h2>
                    @if ($tourism->facilities->isNotEmpty())
                        <div class="facility-grid">
                            @foreach ($tourism->facilities as $facility)
                                <div class="facility-pill">
                                    <i class="fa-solid fa-circle-check text-success me-1"></i>
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
                        <h2 class="detail-card-title"><span class="title-icon"><i class="fa-solid fa-calendar-days text-primary"></i></span> Jadwal Jam Operasional Mingguan</h2>
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
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="detail-card-title mb-0"><span class="title-icon"><i class="fa-solid fa-comments text-info"></i></span> Ulasan & Testimoni Pengunjung</h2>
                        <span class="badge bg-light text-muted border px-2.5 py-1.5 fs-8">{{ $tourism->reviews->count() }} Ulasan</span>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2 fs-8">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 fs-8">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <!-- Review Form Box directly in Reviews Section -->
                    <div class="mb-4" id="section-tulis-ulasan">
                        <x-review-form :action="route('tourism.reviews.store', $tourism->slug)" itemType="destinasi wisata" />
                    </div>

                    <h4 class="fs-6 fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-comments text-primary"></i>
                        <span>Semua Ulasan Wisatawan ({{ $tourism->reviews->count() }})</span>
                    </h4>

                    @forelse ($tourism->reviews as $review)
                        <div class="p-3 mb-3 rounded-4 shadow-sm bg-white border" style="transition: all 0.2s ease;">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2.5">
                                    @if($review->user?->profile?->avatar)
                                        <img src="{{ asset('storage/' . $review->user->profile->avatar->object_key) }}" alt="{{ $review->user->name }}" class="rounded-circle border shadow-sm flex-shrink-0" style="width: 42px; height: 42px; object-fit: cover;">
                                    @else
                                        <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #047857 0%, #10b981 100%); color: #fff; display: grid; place-items: center; font-weight: 700; font-size: 16px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(4,120,87,0.2);">
                                            {{ strtoupper(substr($review->user?->name ?? 'P', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <strong class="fs-7 text-dark">{{ $review->user?->name ?? 'Pengunjung Jelajah Tegal' }}</strong>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 mt-0.5">
                                            <div class="text-warning" style="font-size: 13px;">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2 py-0.5" style="font-size: 10.5px;">
                                                {{ $review->rating }}.0
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 11.5px;">{{ $review->created_at?->diffForHumans() }}</small>
                            </div>

                            @if ($review->title)
                                <h6 class="fw-bold mb-1 fs-7 text-dark">{{ $review->title }}</h6>
                            @endif
                            <p class="mb-2 text-secondary" style="font-size: 13.5px; line-height: 1.6;">{{ $review->body }}</p>

                            <!-- Nested Replies List -->
                            @if ($review->replies->isNotEmpty())
                                <div class="mt-3 pt-2.5 border-top d-flex flex-column gap-2" style="border-color: rgba(0,0,0,0.06) !important;">
                                    @foreach ($review->replies as $reply)
                                        <div class="p-2.5 rounded-3 ms-2 ms-md-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($reply->author?->profile?->avatar)
                                                        <img src="{{ asset('storage/' . $reply->author->profile->avatar->object_key) }}" alt="{{ $reply->author->name }}" class="rounded-circle border" style="width: 24px; height: 24px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 24px; height: 24px; font-size: 10px; background: #047857;">
                                                            {{ strtoupper(substr($reply->author?->name ?? 'P', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <strong class="fs-8 text-dark">{{ $reply->author?->name ?? 'Pengguna' }}</strong>
                                                    @if ($reply->mitra_id)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 9.5px;">
                                                            <i class="fa-solid fa-shield-halved me-0.5"></i> Pengelola
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 9.5px;">
                                                            <i class="fa-solid fa-user me-0.5"></i> Pengunjung
                                                        </span>
                                                    @endif
                                                </div>
                                                <small class="text-muted" style="font-size: 10.5px;">{{ $reply->created_at?->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0 text-muted" style="font-size: 12.5px; line-height: 1.5;">{{ $reply->body }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Action: Reply Toggle & Form -->
                            <div class="mt-2.5 pt-2 d-flex align-items-center justify-content-between">
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-primary fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 12px;" data-bs-toggle="collapse" data-bs-target="#replyBox-{{ $review->id }}" aria-expanded="false">
                                    <i class="fa-solid fa-reply"></i>
                                    <span>Balas Ulasan ({{ $review->replies->count() }})</span>
                                </button>
                            </div>

                            <div class="collapse mt-2.5" id="replyBox-{{ $review->id }}">
                                @auth
                                    <form method="POST" action="{{ route('public.reviews.replies.store', $review->id) }}" class="p-3 rounded-3 bg-white border shadow-sm">
                                        @csrf
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            @if(auth()->user()->profile?->avatar)
                                                <img src="{{ asset('storage/' . auth()->user()->profile->avatar->object_key) }}" alt="Avatar" class="rounded-circle border" style="width: 26px; height: 26px; object-fit: cover;">
                                            @endif
                                            <small class="fw-bold text-dark fs-8">Tulis tanggapan sebagai {{ auth()->user()->name }}:</small>
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="body" class="form-control form-control-sm" rows="2" placeholder="Tulis balasan atau tips tambahan Anda..." required style="font-size: 12.5px; border-radius: 8px;"></textarea>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-light py-1 px-3 border rounded-pill" style="font-size: 11.5px;" data-bs-toggle="collapse" data-bs-target="#replyBox-{{ $review->id }}">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-lokantara py-1 px-3 rounded-pill fw-bold" style="font-size: 11.5px;">Kirim Balasan</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="p-2.5 rounded-3 bg-light border text-center" style="font-size: 12px;">
                                        <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none"><i class="fa-regular fa-user me-1"></i>Masuk (Login)</a> untuk menulis balasan ulasan ini.
                                    </div>
                                @endauth
                            </div>
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
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="detail-card-title mb-0"><span class="title-icon"><i class="fa-solid fa-ticket text-danger"></i></span> Paket Tiket Masuk</h2>
                    </div>
                    
                    @forelse ($tourism->offers->whereIn('status', ['active', 'published']) as $offer)
                        @php
                            $todayAvailability = $offer->availabilities->where('service_date', now()->format('Y-m-d'))->first();
                            $capacity = $todayAvailability?->capacity ?? ($offer->ticketPackage?->quota_per_day ?? 100);
                            $reserved = $todayAvailability?->reserved_quantity ?? 0;
                            $remaining = max(0, $capacity - $reserved);
                            $isClosed = ($todayAvailability && $todayAvailability->status !== 'available');
                            $isSoldOut = $isClosed || ($remaining <= 0);
                        @endphp

                        <div class="ticket-offer-item">
                            <div class="ticket-offer-header">
                                <div>
                                    <h3 class="ticket-name">{{ $offer->name }}</h3>
                                    <div class="mt-1">
                                        @if ($isSoldOut)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 11px;">
                                                <i class="fa-solid fa-ban me-1"></i> Tiket Habis (Sold Out)
                                            </span>
                                        @elseif ($remaining <= 10)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1" style="font-size: 11px;">
                                                <i class="fa-solid fa-fire text-danger me-1"></i> Segera Habis! Sisa {{ $remaining }} tiket
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px;">
                                                <i class="fa-solid fa-circle-check text-success me-1"></i> Slot Tersedia Hari Ini
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ticket-price">Rp {{ number_format($offer->price, 0, ',', '.') }}</div>
                            </div>
                            <p class="text-muted mb-3" style="font-size: 12px;">{{ $offer->description ?: 'Tiket akses masuk destinasi wisata per orang/kunjungan.' }}</p>
                            
                            @if ($isSoldOut)
                                <button type="button" class="btn btn-secondary w-100 fw-bold py-2 disabled mb-2" disabled>
                                    <i class="fa-solid fa-ban me-1"></i> Tiket Habis Hari Ini
                                </button>
                                @auth
                                    <button type="button" class="btn btn-outline-success w-100 fw-bold py-2" data-bs-toggle="modal" data-bs-target="#bookModal-{{ $offer->id }}">
                                        <i class="fa-solid fa-calendar-day me-1"></i> Pesan untuk Tanggal Lain
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-success w-100 fw-bold py-2">
                                        Masuk untuk Pilih Tanggal Lain
                                    </a>
                                @endauth
                            @else
                                @auth
                                    <button type="button" class="btn btn-lokantara w-100 fw-bold py-2" data-bs-toggle="modal" data-bs-target="#bookModal-{{ $offer->id }}">
                                        <i class="fa-solid fa-ticket me-1"></i> Pesan Tiket Sekarang
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-lokantara w-100 fw-bold py-2">
                                        Masuk untuk Memesan
                                    </a>
                                @endauth
                            @endif
                        </div>
                    @empty
                        <div class="p-3 mb-3 rounded-3 text-center" style="background: var(--lokantara-background);">
                            <p class="text-muted mb-0" style="font-size: 14px;">Tiket dapat dibeli langsung di loket masuk lokasi wisata.</p>
                        </div>
                    @endforelse

                    <!-- Actions & Favorite -->
                    <div class="d-flex flex-column gap-2 mt-3">
                        @if($tourism->has_virtual_tour)
                            <a href="{{ route('public.virtual-tour.serve', ['domain' => 'tourism', 'slug' => $tourism->slug]) }}" rel="noopener noreferrer" class="btn btn-lokantara w-100 d-flex align-items-center justify-content-center gap-2 py-2 fw-bold" style="background: linear-gradient(135deg, #0f766e, #047857); border: none;">
                                <i class="fa-solid fa-vr-cardboard"></i> Lihat Virtual Tour 360
                            </a>
                        @endif
                        @auth
                            <form method="POST" action="{{ route('tourism.favorite', $tourism->slug) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-lokantara w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                                    <i class="fa-solid fa-heart text-danger"></i> Simpan ke Favorit
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

                    <!-- Review CTA in Sidebar -->
                    <hr class="my-4">
                    <div class="p-3 rounded-4 border text-center" style="background: linear-gradient(135deg, rgba(4,120,87,0.05) 0%, rgba(16,185,129,0.08) 100%); border-color: rgba(4,120,87,0.15) !important;">
                        <div class="d-flex align-items-center justify-content-center gap-1 text-warning mb-1.5" style="font-size: 16px;">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1 fs-7">Pernah Berkunjung ke Sini?</h6>
                        <p class="text-muted small mb-2.5" style="font-size: 11.5px;">Bantu wisatawan lain dengan membagikan penilaian & ulasan pengalaman Anda.</p>
                        <a href="#section-tulis-ulasan" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3 py-1.5 w-100 shadow-sm">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Tulis Ulasan Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leaflet.js: Lazy-loaded only when map comes into viewport -->
<script>
(function() {
    const lat = {{ $lat }};
    const lng = {{ $lng }};
    const destinationTitle = "{{ addslashes($tourism->name) }}";
    const address = "{{ addslashes($tourism->address ?: 'Kawasan Wisata ' . $tourism->name) }}";

    function initLeafletMap() {
        if (typeof window.initLokantaraMap === 'function') {
            if (window._mapInitialized) return;
            window._mapInitialized = true;
            window.initLokantaraMap('tourismMap', lat, lng, destinationTitle, address, 'tourism');
        } else {
            setTimeout(initLeafletMap, 50);
        }
    }

    // Observe map container - load Leaflet only when it enters viewport
    const mapEl = document.getElementById('tourismMap');
    if (mapEl && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                observer.disconnect();
                initLeafletMap();
            }
        }, { rootMargin: '200px' });
        observer.observe(mapEl);
    } else if (mapEl) {
        // Fallback for browsers without IntersectionObserver
        initLeafletMap();
    }
})();
</script>


@auth
    @foreach ($tourism->offers->whereIn('status', ['active', 'published']) as $offer)
        @php
            $todayAvailModal = $offer->availabilities->where('service_date', now()->format('Y-m-d'))->first();
            $capModal = $todayAvailModal?->capacity ?? ($offer->ticketPackage?->quota_per_day ?? 100);
            $resModal = $todayAvailModal?->reserved_quantity ?? 0;
            $remModal = max(0, $capModal - $resModal);
            $maxBookable = min(10, max(1, $remModal));
        @endphp

        <!-- Booking Modal for Offer {{ $offer->name }} -->
        <div class="modal fade" id="bookModal-{{ $offer->id }}" tabindex="-1" aria-labelledby="bookModalLabel-{{ $offer->id }}" aria-hidden="true" style="z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg text-start">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-extrabold text-dark fs-5" id="bookModalLabel-{{ $offer->id }}">
                            <i class="fa-solid fa-ticket text-success me-2"></i> Pemesanan Tiket
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('consumer.checkout.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
                        <input type="hidden" name="domain" value="tourism">
                        <input type="hidden" name="reference_id" value="{{ $offer->id }}">

                        <div class="modal-body pt-3">
                            <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <h6 class="fw-bold mb-1 text-dark">{{ $offer->name }}</h6>
                                <p class="text-muted small mb-2">{{ $tourism->name }} — {{ $tourism->region?->name ?? 'Kabupaten Tegal' }}</p>
                                <div class="fs-5 fw-extrabold text-success">
                                    Rp {{ number_format($offer->price, 0, ',', '.') }} <span class="fs-8 fw-normal text-muted">/ orang</span>
                                </div>
                            </div>

                            <!-- Tanggal Kunjungan -->
                            <div class="mb-3">
                                <label class="form-label fw-bold fs-7 text-dark">Tanggal Kunjungan <span class="text-danger">*</span></label>
                                <input type="date" id="serviceDate-{{ $offer->id }}" name="service_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" onchange="updateDateQuota_{{ $offer->id }}(this.value)" required>
                            </div>

                            <!-- Sold Out Alert for Selected Date -->
                            <div id="soldOutAlert-{{ $offer->id }}" class="alert alert-danger py-2 px-3 rounded-3 fs-8 fw-bold d-none mb-3" role="alert">
                                <i class="fa-solid fa-ban me-1"></i> Maaf, kuota tiket pada tanggal ini telah habis (Sold Out). Silakan pilih tanggal lain.
                            </div>

                            <!-- Jumlah Tiket -->
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label fw-bold fs-7 text-dark mb-0">Jumlah Tiket <span class="text-danger">*</span></label>
                                    <span id="stockInfo-{{ $offer->id }}">
                                        @if ($remModal <= 0)
                                            <small class="text-danger fw-bold"><i class="fa-solid fa-ban me-1"></i>Tiket Habis (Sold Out)</small>
                                        @elseif ($remModal <= 10)
                                            <small class="text-danger fw-bold"><i class="fa-solid fa-fire me-1"></i>Sisa {{ $remModal }} tiket</small>
                                        @else
                                            <small class="text-success fw-semibold"><i class="fa-solid fa-circle-check me-1"></i>Slot tersedia</small>
                                        @endif
                                    </span>
                                </div>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary px-3" onclick="let q = document.getElementById('qty-{{ $offer->id }}'); if(parseInt(q.value) > 1) { q.value = parseInt(q.value) - 1; calcTotal{{ $offer->id }}(); }">-</button>
                                    <input type="number" id="qty-{{ $offer->id }}" name="quantity" class="form-control text-center fw-bold" value="{{ $remModal > 0 ? 1 : 0 }}" min="1" max="{{ $maxBookable }}" onchange="calcTotal{{ $offer->id }}()" {{ $remModal <= 0 ? 'disabled' : '' }} required>
                                    <button type="button" class="btn btn-outline-secondary px-3" onclick="let q = document.getElementById('qty-{{ $offer->id }}'); let max = parseInt(q.max) || 10; if(parseInt(q.value || 0) < max) { q.value = parseInt(q.value || 0) + 1; calcTotal{{ $offer->id }}(); }">+</button>
                                </div>
                            </div>

                            <!-- Kode Voucher (Opsional) -->
                            <div class="mb-3">
                                <label class="form-label fw-bold fs-7 text-dark">Kode Voucher (Opsional)</label>
                                <input type="text" name="voucher_code" class="form-control rounded-3 text-uppercase" placeholder="Contoh: TEGALHEMAT">
                            </div>

                            <!-- Total Cost Summary -->
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <span class="fw-bold text-muted fs-7">Total Pembayaran</span>
                                <span class="fs-4 fw-extrabold text-success" id="totalDisplay-{{ $offer->id }}">
                                    Rp {{ number_format($offer->price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="submitBtn-{{ $offer->id }}" class="btn btn-success rounded-pill px-4 fw-bold text-white" {{ $remModal <= 0 ? 'disabled' : '' }}>
                                Lanjutkan Pembayaran <i class="fa-solid fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            const availabilities_{{ $offer->id }} = @json($offer->availabilities->mapWithKeys(fn($a) => [
                $a->service_date instanceof \Carbon\CarbonInterface ? $a->service_date->toDateString() : (string)$a->service_date => [
                    'capacity' => (int) $a->capacity,
                    'reserved' => (int) $a->reserved_quantity,
                    'status' => $a->status
                ]
            ]));
            const defaultQuota_{{ $offer->id }} = {{ (int) ($offer->ticketPackage?->quota_per_day ?? 100) }};

            function updateDateQuota_{{ $offer->id }}(dateVal) {
                let avail = availabilities_{{ $offer->id }}[dateVal];
                let cap = avail ? avail.capacity : defaultQuota_{{ $offer->id }};
                let res = avail ? avail.reserved : 0;
                let status = avail ? avail.status : (cap > 0 ? 'available' : 'sold_out');
                let rem = (status === 'available') ? Math.max(0, cap - res) : 0;
                
                let infoElem = document.getElementById('stockInfo-{{ $offer->id }}');
                let qtyInput = document.getElementById('qty-{{ $offer->id }}');
                let submitBtn = document.getElementById('submitBtn-{{ $offer->id }}');
                let soldOutAlert = document.getElementById('soldOutAlert-{{ $offer->id }}');
                
                if (rem <= 0) {
                    if (infoElem) infoElem.innerHTML = '<small class="text-danger fw-bold"><i class="fa-solid fa-ban me-1"></i>Tiket Habis (Sold Out)</small>';
                    if (soldOutAlert) soldOutAlert.classList.remove('d-none');
                    if (qtyInput) { qtyInput.disabled = true; qtyInput.value = 0; }
                    if (submitBtn) submitBtn.disabled = true;
                } else {
                    if (soldOutAlert) soldOutAlert.classList.add('d-none');
                    if (qtyInput) {
                        qtyInput.disabled = false;
                        qtyInput.max = Math.min(10, rem);
                        if (parseInt(qtyInput.value) <= 0 || parseInt(qtyInput.value) > rem) {
                            qtyInput.value = 1;
                        }
                    }
                    if (submitBtn) submitBtn.disabled = false;
                    
                    if (infoElem) {
                        if (rem <= 10) {
                            infoElem.innerHTML = `<small class="text-danger fw-bold"><i class="fa-solid fa-fire me-1"></i>Sisa ${rem} tiket</small>`;
                        } else {
                            infoElem.innerHTML = `<small class="text-success fw-semibold"><i class="fa-solid fa-circle-check me-1"></i>Slot tersedia (${rem} tiket)</small>`;
                        }
                    }
                }
                calcTotal{{ $offer->id }}();
            }

            function calcTotal{{ $offer->id }}() {
                let price = {{ $offer->price }};
                let qtyInput = document.getElementById('qty-{{ $offer->id }}');
                let qty = qtyInput && !qtyInput.disabled ? (parseInt(qtyInput.value) || 0) : 0;
                let total = price * qty;
                let display = document.getElementById('totalDisplay-{{ $offer->id }}');
                if (display) {
                    display.innerText = 'Rp ' + total.toLocaleString('id-ID');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                let dateInput = document.getElementById('serviceDate-{{ $offer->id }}');
                if (dateInput && dateInput.value) {
                    updateDateQuota_{{ $offer->id }}(dateInput.value);
                }
            });
        </script>
    @endforeach
@endauth
@endsection
