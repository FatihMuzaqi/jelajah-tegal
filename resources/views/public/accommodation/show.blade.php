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
    opacity: 0.45;
    background-size: cover;
    background-position: center;
    filter: blur(2px) scale(1.03);
}
.hotel-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(13,38,30,0.32) 0%, rgba(13,38,30,0.78) 100%);
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
                    <div id="hotelMap" style="height: 380px; width: 100%; border-radius: 16px; overflow: hidden; margin-bottom: 20px; background: #e9ecef; z-index: 1; border: 1px solid var(--lokantara-border);"></div>

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

                <!-- Visitor Reviews Section -->
                <div class="mt-4 pt-2">
                    @if (session('status'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2.5 px-3 mb-3 d-flex align-items-center gap-2 fs-8">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2.5 px-3 mb-3 fs-8">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- 1. Review Form Box -->
                    <div class="mb-4" id="section-tulis-ulasan">
                        <x-review-form :action="route('accommodation.reviews.store', $accommodation->slug)" itemType="penginapan" />
                    </div>

                    <!-- 2. Section Heading -->
                    <h5 class="fw-bold text-dark mb-3 mt-4" style="font-size: 16px;">
                        Semua Ulasan Tamu ({{ $accommodation->reviews->count() }})
                    </h5>

                    <!-- 3. Reviews List -->
                    @forelse ($accommodation->reviews as $review)
                        <div class="p-3.5 mb-3 rounded-4 shadow-sm bg-white border" style="border-color: #e2e8f0 !important; transition: all 0.2s ease;">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2.5">
                                    @if($review->user?->profile?->avatar)
                                        <img src="{{ asset('storage/' . $review->user->profile->avatar->object_key) }}" alt="{{ $review->user->name }}" class="rounded-circle border shadow-sm flex-shrink-0" style="width: 38px; height: 38px; object-fit: cover;">
                                    @else
                                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0d9488; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0;">
                                            {{ strtoupper(substr($review->user?->name ?? 'T', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="text-dark fw-bold" style="font-size: 14px; line-height: 1.2;">{{ $review->user?->name ?? 'Tamu Penginapan' }}</strong>
                                        <div class="d-flex align-items-center gap-0.5 mt-0.5" style="color: #f59e0b; font-size: 11px;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <span class="text-muted" style="font-size: 11px;">{{ $review->created_at?->diffForHumans() }}</span>
                            </div>

                            <p class="text-dark mt-2.5 mb-2" style="font-size: 13px; line-height: 1.5; color: #334155;">{{ $review->body }}</p>

                            <!-- Review Photos Gallery -->
                            @if (!empty($review->photos) && is_array($review->photos))
                                <div class="d-flex flex-wrap gap-2 mt-2 mb-2.5 review-photos-gallery">
                                    @foreach ($review->photos as $photo)
                                        <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="review-photo-thumbnail rounded-3 overflow-hidden border shadow-2xs d-inline-block position-relative" style="width: 72px; height: 72px; background: #f8fafc;" title="Klik untuk perbesar foto ulasan">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Foto ulasan {{ $review->user?->name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.08)';" onmouseout="this.style.transform='scale(1)';">
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Nested Replies (Discussion Thread) -->
                            @if ($review->replies->isNotEmpty())
                                <div class="mt-3 ps-3 border-start d-flex flex-column gap-2" style="border-left: 2px solid #e2e8f0 !important; margin-left: 14px;">
                                    @foreach ($review->replies as $reply)
                                        <div class="py-1">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                    @if($reply->author?->profile?->avatar)
                                                        <img src="{{ asset('storage/' . $reply->author->profile->avatar->object_key) }}" alt="{{ $reply->author->name }}" class="rounded-circle border" style="width: 24px; height: 24px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 24px; height: 24px; font-size: 10px; background: #475569;">
                                                            {{ strtoupper(substr($reply->author?->name ?? 'P', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <strong class="text-dark fw-bold ms-1" style="font-size: 12.5px;">{{ $reply->author?->name ?? 'Pengguna' }}</strong>
                                                    @if ($reply->mitra_id)
                                                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 9.5px; font-weight: 600; padding: 2px 6px; border-radius: 4px;">Pengelola</span>
                                                    @else
                                                        <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 9.5px; font-weight: 600; padding: 2px 6px; border-radius: 4px;">Pengunjung</span>
                                                    @endif
                                                </div>
                                                <span class="text-muted" style="font-size: 10.5px;">{{ $reply->created_at?->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-dark ps-4 ms-2" style="font-size: 12.5px; line-height: 1.5; color: #1e293b;">
                                                {{ $reply->body }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Action: Reply Toggle & Form -->
                            <div class="mt-2.5 pt-1 d-flex align-items-center justify-content-between">
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-muted fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11.5px;" data-bs-toggle="collapse" data-bs-target="#replyBox-acc-{{ $review->id }}" aria-expanded="false">
                                    <i class="fa-solid fa-reply"></i>
                                    <span>Balas Ulasan ({{ $review->replies->count() }})</span>
                                </button>
                            </div>

                            <div class="collapse mt-2.5" id="replyBox-acc-{{ $review->id }}">
                                @auth
                                    <form method="POST" action="{{ route('public.reviews.replies.store', $review->id) }}" class="p-3 rounded-3 bg-white border shadow-sm" style="border-color: #e2e8f0 !important;">
                                        @csrf
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            @if(auth()->user()->profile?->avatar)
                                                <img src="{{ asset('storage/' . auth()->user()->profile->avatar->object_key) }}" alt="Avatar" class="rounded-circle border" style="width: 24px; height: 24px; object-fit: cover;">
                                            @endif
                                            <small class="fw-bold text-dark fs-8">Tulis tanggapan sebagai {{ auth()->user()->name }}:</small>
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="body" class="form-control form-control-sm rounded-3" rows="2" placeholder="Tulis tanggapan atau saran Anda..." required style="font-size: 12.5px; border-color: #cbd5e1; background: #f8fafc;"></textarea>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-light py-1 px-3 border rounded-3" style="font-size: 11.5px;" data-bs-toggle="collapse" data-bs-target="#replyBox-acc-{{ $review->id }}">Batal</button>
                                            <button type="submit" class="btn btn-sm text-white py-1 px-3 rounded-3 fw-bold" style="background: #0d9488; font-size: 11.5px; border: none;">Kirim Balasan</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="p-2.5 rounded-3 bg-light border text-center" style="font-size: 12px; border-color: #e2e8f0 !important;">
                                        <a href="{{ route('login') }}" class="text-emerald fw-bold text-decoration-none"><i class="fa-regular fa-user me-1"></i>Masuk (Login)</a> untuk menulis balasan ulasan ini.
                                    </div>
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="p-4 rounded-4 bg-white border text-center shadow-sm" style="border-color: #e2e8f0 !important;">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 44px; height: 44px; background: #f0fdf4; color: #047857; font-size: 18px;">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1 fs-7">Belum Ada Ulasan</h6>
                            <p class="text-muted small mb-0" style="font-size: 12px;">Jadilah yang pertama memberikan ulasan dan rating untuk penginapan ini.</p>
                        </div>
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
                                    <span>️</span> Simpan ke Favorit
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
                            <div class="text-success" style="font-size: 12px;"> Mitra Terverifikasi</div>
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
                        <h6 class="fw-bold text-dark mb-1 fs-7">Pernah Menginap di Sini?</h6>
                        <p class="text-muted small mb-2.5" style="font-size: 11.5px;">Bantu wisatawan lain dengan membagikan penilaian & ulasan pengalaman menginap Anda.</p>
                        <a href="#section-tulis-ulasan" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3 py-1.5 w-100 shadow-sm">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Tulis Ulasan Tamu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leaflet.js: Lazy-loaded only when map enters viewport -->
<script>
(function() {
    const lat = {{ $lat }};
    const lng = {{ $lng }};
    const hotelTitle = "{{ addslashes($accommodation->name) }}";
    const address = "{{ addslashes($accommodation->address ?: 'Kawasan ' . $accommodation->name) }}";

    function initLeafletMap() {
        if (typeof window.initLokantaraMap === 'function') {
            if (window._hotelMapInitialized) return;
            window._hotelMapInitialized = true;
            window.initLokantaraMap('hotelMap', lat, lng, hotelTitle, address, 'hotel');
        } else {
            setTimeout(initLeafletMap, 50);
        }
    }

    const mapEl = document.getElementById('hotelMap');
    if (mapEl && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) { observer.disconnect(); initLeafletMap(); }
        }, { rootMargin: '200px' });
        observer.observe(mapEl);
    } else if (mapEl) {
        initLeafletMap();
    }
})();
</script>
@endsection
