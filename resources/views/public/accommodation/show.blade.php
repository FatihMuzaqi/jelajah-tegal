@extends('layouts.public')

@section('title', $accommodation->name . ' — Jelajah Tegal')
@section('meta-description', str($accommodation->description ?: 'Pesan kamar di ' . $accommodation->name . ', penginapan terbaik di ' . ($accommodation->region?->name ?? 'Tegal') . '. Cek harga promo, fasilitas, foto, dan ketersediaan kamar.')->limit(155))
@section('canonical', route('accommodation.show', $accommodation->slug))

@section('content')
@php
    $coverMedia = $accommodation->media->where('pivot.role', 'cover')->first() ?? $accommodation->media->first();
    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : null;
    $galleryMedia = $accommodation->media->where('pivot.role', 'gallery');
    $lat = $accommodation->location?->latitude ?? -6.8987097;
    $lng = $accommodation->location?->longitude ?? 109.3137293;
    $minPrice = $accommodation->accommodation->rooms->min('offer.price') ?? 0;
@endphp

<!-- Leaflet.js CSS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<style>
/* Custom Accommodation Detail Styles */
.hotel-hero-section {
    position: relative;
    background: linear-gradient(135deg, #0d261e 0%, #154737 55%, #1b634b 100%);
    color: #ffffff;
    padding: 55px 0 75px;
    overflow: hidden;
}
.hotel-hero-bg {
    position: absolute;
    inset: 0;
    opacity: 0.22;
    background-size: cover;
    background-position: center;
    filter: blur(6px) scale(1.05);
}
.hotel-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(13,38,30,0.65) 0%, rgba(13,38,30,0.96) 100%);
}
.hotel-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.75);
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}
.hotel-breadcrumbs a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: color 0.2s;
}
.hotel-breadcrumbs a:hover {
    color: #f2a93b;
}
.hotel-hero-content {
    position: relative;
    z-index: 2;
}
.hotel-badge-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-bottom: 16px;
}
.hotel-badge-pill {
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
.badge-property-type {
    background: rgba(242,169,59,0.25);
    color: #fbd38d;
    border: 1px solid rgba(242,169,59,0.45);
}
.badge-hotel-region {
    background: rgba(45,140,168,0.25);
    color: #90cdf4;
    border: 1px solid rgba(45,140,168,0.4);
}
.badge-star-rating {
    background: linear-gradient(135deg, #d69e2e, #b7791f);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(214,158,46,0.35);
}
.hotel-main-title {
    font-size: 40px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    margin: 0 0 16px;
    letter-spacing: -0.02em;
}
.hotel-hero-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 22px;
    font-size: 14px;
    color: rgba(255,255,255,0.9);
}
.hotel-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Quick Stats Bar */
.hotel-stats-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 24px;
    margin-top: -45px;
    position: relative;
    z-index: 10;
    box-shadow: 0 15px 35px rgba(17,26,24,0.08);
}
.hotel-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}
.hotel-stat-box {
    display: flex;
    align-items: center;
    gap: 14px;
}
.hotel-stat-icon {
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
.hotel-stat-info h6 {
    margin: 0;
    font-size: 12px;
    color: var(--lokantara-muted);
    font-weight: 600;
    text-transform: uppercase;
}
.hotel-stat-info p {
    margin: 2px 0 0;
    font-size: 14px;
    font-weight: 700;
    color: var(--lokantara-text);
}

/* Section Cards */
.hotel-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(17,26,24,0.03);
}
.hotel-card-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--lokantara-text);
    margin: 0 0 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.hotel-card-title span.title-icon {
    color: var(--lokantara-primary);
    font-size: 22px;
}

/* Room Items Listing */
.room-card-item {
    background: var(--lokantara-background);
    border: 1px solid var(--lokantara-border);
    border-radius: 16px;
    padding: 22px;
    margin-bottom: 18px;
    transition: all 0.25s ease;
}
.room-card-item:hover {
    border-color: var(--lokantara-primary);
    box-shadow: 0 8px 24px rgba(31,122,92,0.12);
    transform: translateY(-2px);
}
.room-price-display {
    font-size: 22px;
    font-weight: 800;
    color: var(--lokantara-primary);
    line-height: 1;
}

/* Facility Grid */
.hotel-facility-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
    gap: 12px;
}
.hotel-facility-pill {
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

/* Leaflet Map */
#hotelMap {
    height: 360px;
    width: 100%;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--lokantara-border);
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    z-index: 1;
}
.hotel-map-address-box {
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
</style>

<!-- Hero Banner Header -->
<section class="hotel-hero-section">
    @if ($coverUrl)
        <div class="hotel-hero-bg" style="background-image: url('{{ $coverUrl }}');"></div>
    @endif
    <div class="hotel-hero-overlay"></div>

    <div class="container public-container hotel-hero-content">
        <!-- Breadcrumbs -->
        <nav class="hotel-breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('accommodation.index') }}">Penginapan</a>
            <span>/</span>
            <span>{{ $accommodation->region?->name ?? 'Tegal' }}</span>
            <span>/</span>
            <span class="text-white fw-semibold">{{ $accommodation->name }}</span>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <!-- Badges -->
                <div class="hotel-badge-row">
                    <span class="hotel-badge-pill badge-property-type">
                        <i class="fa-solid fa-hotel me-1 text-info"></i> {{ str($accommodation->accommodation->property_type ?? 'Hotel')->headline() }}
                    </span>
                    @if ($accommodation->region)
                        <span class="hotel-badge-pill badge-hotel-region">
                            <i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $accommodation->region->name }}
                        </span>
                    @endif
                    @if ($accommodation->accommodation->star_rating)
                        <span class="hotel-badge-pill badge-star-rating">
                            <i class="fa-solid fa-star me-1 text-warning"></i> Bintang {{ $accommodation->accommodation->star_rating }}
                        </span>
                    @endif
                    @if ($accommodation->is_featured)
                        <span class="hotel-badge-pill" style="background: linear-gradient(135deg, #e53e3e, #dd6b20); color: #fff;">
                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Rekomendasi Unggulan
                        </span>
                    @endif
                </div>

                <!-- Main Title -->
                <h1 class="hotel-main-title">{{ $accommodation->name }}</h1>

                <!-- Meta Details -->
                <div class="hotel-hero-meta">
                    <div class="hotel-meta-item">
                        <span class="text-warning"><i class="fa-solid fa-star"></i></span>
                        <strong>{{ number_format($accommodation->rating_average, 1) }}</strong>
                        <span class="text-white-50">({{ $accommodation->rating_count }} ulasan)</span>
                    </div>
                    <div class="hotel-meta-item">
                        <i class="fa-solid fa-building text-info"></i>
                        <span>Dikelola oleh <strong>{{ $accommodation->mitra->display_name }}</strong></span>
                    </div>
                    <div class="hotel-meta-item">
                        <i class="fa-solid fa-location-dot text-danger"></i>
                        <span>{{ $accommodation->address ?: 'Kawasan ' . ($accommodation->region?->name ?? 'Tegal') }}</span>
                    </div>
                </div>
            </div>

            <!-- Cover Image Preview -->
            @if ($coverUrl)
                <div class="col-lg-4 text-center">
                    <div style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4); border: 3px solid rgba(255,255,255,0.2); max-height: 260px;">
                        <img src="{{ $coverUrl }}" alt="{{ $accommodation->name }}" style="width: 100%; height: 260px; object-fit: cover;">
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Quick Stats Bar -->
<div class="container public-container">
    <div class="hotel-stats-card">
        <div class="hotel-stats-grid">
            <div class="hotel-stat-box">
                <div class="hotel-stat-icon"><i class="fa-solid fa-bell-concierge text-primary"></i></div>
                <div class="hotel-stat-info">
                    <h6>Waktu Check-In</h6>
                    <p>{{ $accommodation->accommodation->check_in_time ? substr($accommodation->accommodation->check_in_time, 0, 5) . ' WIB' : '14:00 WIB' }}</p>
                </div>
            </div>
            <div class="hotel-stat-box">
                <div class="hotel-stat-icon"><i class="fa-solid fa-door-open text-danger"></i></div>
                <div class="hotel-stat-info">
                    <h6>Waktu Check-Out</h6>
                    <p>{{ $accommodation->accommodation->check_out_time ? substr($accommodation->accommodation->check_out_time, 0, 5) . ' WIB' : '12:00 WIB' }}</p>
                </div>
            </div>
            <div class="hotel-stat-box">
                <div class="hotel-stat-icon"><i class="fa-solid fa-bed text-info"></i></div>
                <div class="hotel-stat-info">
                    <h6>Pilihan Kamar</h6>
                    <p>{{ $accommodation->accommodation->rooms->count() }} Tipe Kamar</p>
                </div>
            </div>
            <div class="hotel-stat-box">
                <div class="hotel-stat-icon"><i class="fa-solid fa-tag text-success"></i></div>
                <div class="hotel-stat-info">
                    <h6>Harga Mulai Dari</h6>
                    <p class="text-success">
                        @if ($minPrice > 0)
                            Rp {{ number_format($minPrice, 0, ',', '.') }} <small class="text-muted fw-normal">/mlm</small>
                        @else
                            Hubungi Pengelola
                        @endif
                    </p>
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
                <div class="hotel-card">
                    <h2 class="hotel-card-title"><span class="title-icon"><i class="fa-solid fa-book-open text-emerald"></i></span> Tentang Penginapan</h2>
                    <div style="font-size: 15px; line-height: 1.8; color: var(--lokantara-text);">
                        {!! nl2br(e($accommodation->description ?: 'Selamat datang di ' . $accommodation->name . '. Nikmati pengalaman menginap yang nyaman dan istirahat berkualitas dengan fasilitas lengkap dan pelayanan ramah di kawasan ' . ($accommodation->region?->name ?? 'Tegal') . '.')) !!}
                    </div>

                    <!-- Gallery Photos if available -->
                    @if ($galleryMedia->isNotEmpty())
                        <hr class="my-4">
                        <h3 class="fs-6 fw-bold mb-2"><i class="fa-solid fa-images text-primary me-2"></i>Galeri Foto Penginapan</h3>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach ($galleryMedia as $media)
                                <div style="width: 140px; height: 100px; border-radius: 12px; overflow: hidden; border: 1px solid var(--lokantara-border);">
                                    <img src="{{ asset('storage/' . $media->object_key) }}" alt="Galeri {{ $accommodation->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Rooms Selection Card -->
                <div class="hotel-card">
                    <h2 class="hotel-card-title"><span class="title-icon"><i class="fa-solid fa-bed text-info"></i></span> Pilihan Tipe Kamar Tersedia</h2>
                    <p class="text-muted fs-7 mb-4">Pilih tipe kamar yang paling sesuai dengan kebutuhan menginap Anda:</p>

                    @forelse ($accommodation->accommodation->rooms as $room)
                        @php
                            $roomCover = $room->media->first();
                            $roomCoverUrl = $roomCover ? asset('storage/' . $roomCover->object_key) : null;
                        @endphp
                        <div class="room-card-item">
                            <div class="row align-items-center g-3">
                                @if ($roomCoverUrl)
                                    <div class="col-md-4">
                                        <div style="height: 140px; border-radius: 12px; overflow: hidden;">
                                            <img src="{{ $roomCoverUrl }}" alt="{{ $room->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                @endif
                                <div class="{{ $roomCoverUrl ? 'col-md-8' : 'col-md-12' }}">
                                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                                        <div>
                                            <h3 class="fs-5 fw-bold mb-1" style="color: var(--lokantara-text);">{{ $room->name }}</h3>
                                            <div class="d-flex flex-wrap gap-2 text-muted" style="font-size: 13px;">
                                                <span><i class="fa-solid fa-users text-primary me-1"></i> {{ $room->capacity_adults }} Dewasa, {{ $room->capacity_children }} Anak</span>
                                                <span>·</span>
                                                <span><i class="fa-solid fa-building text-info me-1"></i> {{ $room->total_units }} Unit Tersedia</span>
                                                @if ($room->bed_type)
                                                    <span>·</span>
                                                    <span><i class="fa-solid fa-bed text-secondary me-1"></i> {{ $room->bed_type }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-md-end">
                                            <div class="room-price-display">
                                                Rp {{ number_format($room->offer->price, 0, ',', '.') }}
                                            </div>
                                            <small class="text-muted" style="font-size: 11px;">per malam / kamar</small>
                                        </div>
                                    </div>

                                    @if ($room->description)
                                        <p class="text-muted mb-3" style="font-size: 13px; line-height: 1.5;">
                                            {{ str($room->description)->limit(120) }}
                                        </p>
                                    @endif

                                    <!-- Room Facilities preview -->
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @forelse ($room->facilities->take(4) as $facility)
                                            <span class="badge text-bg-light border" style="font-size: 11px;"><i class="fa-solid fa-circle-check text-success me-1"></i> {{ $facility->name }}</span>
                                        @empty
                                            <span class="badge text-bg-light border" style="font-size: 11px;"><i class="fa-solid fa-circle-check text-success me-1"></i> Fasilitas Kamar Standar Nyaman</span>
                                        @endforelse
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                        <a href="{{ route('accommodation.rooms.show', [$accommodation->slug, $room]) }}" class="btn btn-sm btn-outline-lokantara fw-semibold px-3">
                                            <i class="fa-solid fa-magnifying-glass me-1"></i> Lihat Rincian Kamar
                                        </a>
                                        @auth
                                            <a href="{{ route('consumer.orders.index') }}" class="btn btn-sm btn-lokantara fw-bold px-4">
                                                Pesan Kamar Ini
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-sm btn-lokantara fw-bold px-4">
                                                Masuk untuk Memesan
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-empty-state title="Kamar belum tersedia" description="Saat ini belum ada kamar yang dipublikasikan untuk penginapan ini." compact />
                    @endforelse
                </div>

                <!-- Facilities Card -->
                <div class="hotel-card">
                    <h2 class="hotel-card-title"><span class="title-icon"><i class="fa-solid fa-wand-magic-sparkles text-warning"></i></span> Fasilitas Utama Properti</h2>
                    @if ($accommodation->facilities->isNotEmpty())
                        <div class="hotel-facility-grid">
                            @foreach ($accommodation->facilities as $facility)
                                <div class="hotel-facility-pill">
                                    <i class="fa-solid fa-circle-check text-success me-1"></i>
                                    <span>{{ $facility->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="hotel-facility-grid">
                            <div class="hotel-facility-pill"><i class="fa-solid fa-circle-check text-success me-1"></i><span>Parkir Kendaraan Luas</span></div>
                            <div class="hotel-facility-pill"><i class="fa-solid fa-circle-check text-success me-1"></i><span>Resepsionis 24 Jam</span></div>
                            <div class="hotel-facility-pill"><i class="fa-solid fa-circle-check text-success me-1"></i><span>Akses Wi-Fi Cepat</span></div>
                            <div class="hotel-facility-pill"><i class="fa-solid fa-circle-check text-success me-1"></i><span>Pendingin Ruangan (AC)</span></div>
                        </div>
                    @endif
                </div>

                <!-- Interactive Map Card -->
                <div class="hotel-card">
                    <h2 class="hotel-card-title"><span class="title-icon"><i class="fa-solid fa-map-location-dot text-info"></i></span> Lokasi & Peta Arah</h2>
                    <div id="hotelMap"></div>

                    <div class="hotel-map-address-box">
                        <div style="flex: 1; min-width: 240px;">
                            <strong>Alamat Lengkap:</strong>
                            <div style="font-size: 14px; color: var(--lokantara-text);">
                                {{ $accommodation->address ?: 'Kawasan ' . $accommodation->name . ', ' . ($accommodation->region?->name ?? 'Tegal') }}
                            </div>
                            <small class="text-muted">Koordinat: {{ number_format($lat, 6) }}, {{ number_format($lng, 6) }}</small>
                        </div>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lat }},{{ $lng }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary fw-semibold px-3 py-2" style="border-radius: 10px;">
                            <i class="fa-solid fa-map-location-dot me-1"></i> Petunjuk Arah Google Maps
                        </a>
                    </div>
                </div>

                <!-- Nearby Tourism Card (Radius 25 km) -->
                <div class="hotel-card">
                    <h2 class="hotel-card-title"><span class="title-icon"><i class="fa-solid fa-umbrella-beach text-primary"></i></span> Destinasi Wisata Terdekat</h2>
                    <p class="text-muted mb-3" style="font-size: 14px;">Eksplorasi tempat wisata menarik di sekitar penginapan ini dalam radius 25 km:</p>
                    <a class="btn btn-outline-lokantara fw-bold" href="{{ route('tourism.index', ['latitude' => $lat, 'longitude' => $lng, 'radius' => 25]) }}">
                        <i class="fa-solid fa-compass me-1"></i> Cari Destinasi Wisata Sekitar (25 km)
                    </a>
                </div>

                <!-- Visitor Reviews Card -->
                <div class="hotel-card">
                    <h2 class="hotel-card-title"><span class="title-icon"><i class="fa-solid fa-comments text-info"></i></span> Ulasan & Pengalaman Menginap</h2>
                    @forelse ($accommodation->reviews as $review)
                        <div class="p-3 mb-3 rounded-3" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <strong class="fs-6">{{ $review->user?->name ?? 'Tamu Penginapan' }}</strong>
                                    <div class="text-warning" style="font-size: 13px;">
                                        <i class="fa-solid fa-star"></i> {{ $review->rating }}/5
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
                        <x-empty-state title="Belum ada ulasan terpublikasi" description="Jadilah tamu pertama yang memberikan ulasan pengalaman menginap di sini." compact />
                    @endforelse
                </div>
            </div>

            <!-- Right Sidebar Column (4 Cols) -->
            <div class="col-lg-4">
                <!-- Stay Overview Card -->
                <div class="hotel-card" style="position: sticky; top: 90px;">
                    <h2 class="hotel-card-title"><span class="title-icon"><i class="fa-solid fa-clipboard-list text-primary"></i></span> Informasi Menginap</h2>

                    <div class="p-3 rounded-3 mb-3" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted fs-7">Waktu Check-In</span>
                            <strong class="fs-7">{{ $accommodation->accommodation->check_in_time ? substr($accommodation->accommodation->check_in_time, 0, 5) . ' WIB' : '14:00 WIB' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted fs-7">Waktu Check-Out</span>
                            <strong class="fs-7">{{ $accommodation->accommodation->check_out_time ? substr($accommodation->accommodation->check_out_time, 0, 5) . ' WIB' : '12:00 WIB' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted fs-7">Tipe Akomodasi</span>
                            <strong class="fs-7">{{ str($accommodation->accommodation->property_type ?? 'Hotel')->headline() }}</strong>
                        </div>
                    </div>

                    <!-- Actions & Favorite -->
                    <div class="d-flex flex-column gap-2 mb-3">
                        @auth
                            <form method="POST" action="{{ route('accommodation.favorite', $accommodation->slug) }}">
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
                            {{ substr($accommodation->mitra->display_name, 0, 1) }}
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Mitra Pengelola</small>
                            <strong class="fs-6">{{ $accommodation->mitra->display_name }}</strong>
                            <div class="text-success" style="font-size: 12px;">✔ Mitra Terverifikasi</div>
                        </div>
                    </div>

                    <!-- Review Submission Form for Authenticated Users -->
                    @auth
                        <hr class="my-4">
                        <h3 class="fs-6 fw-bold mb-3">Tulis Ulasan Penginapan</h3>
                        <form method="POST" action="{{ route('accommodation.reviews.store', $accommodation->slug) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-semibold">Rating Bintang (1 - 5)</label>
                                <select class="form-select" name="rating" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Puas)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 - Bagus & Bersih)</option>
                                    <option value="3">⭐⭐⭐ (3 - Cukup Nyaman)</option>
                                    <option value="2">⭐⭐ (2 - Perlu Peningkatan)</option>
                                    <option value="1">⭐ (1 - Kurang Memuaskan)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-semibold">Judul Ulasan</label>
                                <input class="form-control" type="text" name="title" placeholder="Contoh: Kamar Nyaman & Pelayanan Ramah">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-semibold">Pengalaman Menginap</label>
                                <textarea class="form-control" name="body" rows="3" placeholder="Ceritakan kebersihan kamar, suasana, dan pelayanan penginapan..."></textarea>
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
    const hotelTitle = "{{ addslashes($accommodation->name) }}";
    const address = "{{ addslashes($accommodation->address ?: 'Kawasan ' . $accommodation->name) }}";

    // Initialize Leaflet Map
    const map = L.map('hotelMap', {
        center: [lat, lng],
        zoom: 15,
        zoomControl: true,
        scrollWheelZoom: false
    });

    // Add Tile Layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // Custom Hotel Marker Icon
    const customIcon = L.divIcon({
        className: 'custom-map-pin',
        html: `
            <div style="
                background: linear-gradient(135deg, #1b634b, #0d261e);
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
                <span style="transform: rotate(45deg); font-size: 16px; color: #ffffff;">🏨</span>
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
            <strong style="font-size: 14px; color: #154737; display: block; margin-bottom: 4px;">${hotelTitle}</strong>
            <p style="font-size: 12px; color: #4a5568; margin: 0 0 8px;">${address}</p>
            <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" style="
                display: inline-block;
                background: #1b634b;
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

    setTimeout(() => {
        map.invalidateSize();
    }, 400);
});
</script>
@endsection
