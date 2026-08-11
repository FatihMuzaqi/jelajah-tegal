@extends('layouts.public')

@section('title', 'Jelajah Tegal — Eksplorasi Wisata, Penginapan & Potensi Lokal')
@section('meta-description', 'Temukan destinasi wisata terbaik, penginapan nyaman, kuliner khas, dan event seru di Kabupaten & Kota Tegal dalam satu platform terpadu Jelajah Tegal.')
@if(request()->hasAny(['q','region','service'])) @section('robots','noindex,follow') @endif

@section('content')
<style>
/* Modern Hero Styling */
.jt-hero {
    position: relative;
    background: linear-gradient(135deg, #092018 0%, #124032 50%, #1b634b 100%);
    color: #ffffff;
    padding: 70px 0 90px;
    overflow: hidden;
}
.jt-hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    opacity: 0.28;
}
.jt-hero-orb-1 {
    width: 350px;
    height: 350px;
    background: #f2a93b;
    top: -50px;
    right: -50px;
}
.jt-hero-orb-2 {
    width: 400px;
    height: 400px;
    background: #2d8ca8;
    bottom: -80px;
    left: -80px;
}
.jt-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 860px;
    margin: 0 auto;
}
.jt-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 99px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.22);
    backdrop-filter: blur(10px);
    color: #fbd38d;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 20px;
}
.jt-hero-title {
    font-size: 46px;
    font-weight: 900;
    line-height: 1.18;
    letter-spacing: -0.03em;
    color: #ffffff;
    margin-bottom: 18px;
}
.jt-hero-lead {
    font-size: 17px;
    line-height: 1.6;
    color: rgba(255,255,255,0.85);
    margin-bottom: 36px;
    max-width: 720px;
    margin-left: auto;
    margin-right: auto;
}

/* Glassmorphism Multi-Filter Search Bar */
.jt-search-wrapper {
    background: rgba(255,255,255,0.95);
    border: 1px solid rgba(255,255,255,0.4);
    border-radius: 20px;
    padding: 12px 14px;
    box-shadow: 0 20px 45px rgba(0,0,0,0.2);
    backdrop-filter: blur(15px);
    text-align: left;
    margin-bottom: 24px;
}
.jt-search-form {
    display: grid;
    grid-template-columns: 2fr 1.3fr 1.2fr auto;
    gap: 12px;
    align-items: center;
}
@media (max-width: 991px) {
    .jt-search-form {
        grid-template-columns: 1fr;
    }
}
.jt-search-group {
    padding: 6px 12px;
    border-radius: 12px;
    background: #f8faf9;
    border: 1px solid #e2e8f0;
}
.jt-search-group label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 2px;
}
.jt-search-group input,
.jt-search-group select {
    width: 100%;
    border: none;
    background: transparent;
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    outline: none;
    padding: 0;
}
.jt-search-btn {
    background: linear-gradient(135deg, #1f7a5c, #13352c);
    color: #ffffff;
    font-weight: 700;
    font-size: 15px;
    padding: 14px 28px;
    border-radius: 14px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(31,122,92,0.3);
    transition: all 0.2s;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.jt-search-btn:hover {
    background: linear-gradient(135deg, #185e47, #0d261e);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(31,122,92,0.4);
    color: #fff;
}

/* Quick Search Chips */
.jt-chips-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.9);
}
.jt-chip {
    padding: 6px 14px;
    border-radius: 99px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    color: #ffffff;
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
    transition: all 0.2s;
}
.jt-chip:hover {
    background: rgba(242,169,59,0.3);
    border-color: #f2a93b;
    color: #fbd38d;
}

/* Stats Counter Bar */
.jt-stats-bar {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 24px;
    margin-top: -40px;
    position: relative;
    z-index: 10;
    box-shadow: 0 15px 35px rgba(17,26,24,0.08);
}
.jt-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    text-align: center;
}
.jt-stat-item h3 {
    font-size: 32px;
    font-weight: 900;
    color: var(--lokantara-primary);
    margin: 0 0 4px;
    line-height: 1;
}
.jt-stat-item p {
    margin: 0;
    font-size: 13px;
    font-weight: 600;
    color: var(--lokantara-muted);
    text-transform: uppercase;
}

/* Category Cards */
.jt-category-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 18px;
    padding: 24px 20px;
    text-align: center;
    text-decoration: none;
    color: var(--lokantara-text);
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}
.jt-category-card:hover {
    transform: translateY(-5px);
    border-color: var(--lokantara-primary);
    box-shadow: 0 12px 30px rgba(31,122,92,0.12);
    color: var(--lokantara-primary);
}
.jt-cat-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: rgba(31,122,92,0.08);
    display: grid;
    place-items: center;
    font-size: 28px;
    margin-bottom: 14px;
    transition: transform 0.25s;
}
.jt-category-card:hover .jt-cat-icon {
    transform: scale(1.1) rotate(5deg);
    background: rgba(31,122,92,0.15);
}
.jt-category-card h4 {
    font-size: 16px;
    font-weight: 800;
    margin: 0 0 6px;
}
.jt-category-card p {
    font-size: 12px;
    color: var(--lokantara-muted);
    margin: 0;
}

/* Showcase Cards (Wisata & Hotel) */
.jt-showcase-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.25s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    height: 100%;
    display: flex;
    flex-direction: column;
}
.jt-showcase-card:hover {
    transform: translateY(-5px);
    border-color: var(--lokantara-primary);
    box-shadow: 0 15px 35px rgba(31,122,92,0.14);
}
.jt-card-image-wrap {
    height: 200px;
    position: relative;
    overflow: hidden;
    background: #e2e8f0;
}
.jt-card-image-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.jt-showcase-card:hover .jt-card-image-wrap img {
    transform: scale(1.06);
}
.jt-card-badges {
    position: absolute;
    top: 12px;
    left: 12px;
    right: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    pointer-events: none;
}
.jt-card-badge {
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    backdrop-filter: blur(8px);
}
.badge-wisata {
    background: rgba(13,38,30,0.85);
    color: #fbd38d;
}
.badge-hotel {
    background: rgba(13,38,30,0.85);
    color: #90cdf4;
}
.jt-card-body {
    padding: 22px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.jt-card-body h3 {
    font-size: 18px;
    font-weight: 800;
    margin: 0 0 8px;
    color: var(--lokantara-text);
}
.jt-card-body p {
    font-size: 13px;
    color: var(--lokantara-muted);
    line-height: 1.5;
    margin-bottom: 16px;
    flex: 1;
}
.jt-card-footer {
    padding-top: 14px;
    border-top: 1px solid var(--lokantara-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.jt-price-tag {
    font-size: 16px;
    font-weight: 800;
    color: var(--lokantara-primary);
}

/* Feature Spotlight Box */
.jt-feature-box {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 18px;
    padding: 28px 24px;
    transition: all 0.2s;
    height: 100%;
}
.jt-feature-box:hover {
    border-color: var(--lokantara-primary);
    box-shadow: 0 8px 25px rgba(31,122,92,0.08);
}
.jt-feat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: rgba(31,122,92,0.1);
    color: var(--lokantara-primary);
    display: grid;
    place-items: center;
    font-size: 24px;
    margin-bottom: 16px;
}
.jt-feature-box h4 {
    font-size: 17px;
    font-weight: 800;
    margin: 0 0 8px;
    color: var(--lokantara-text);
}
.jt-feature-box p {
    font-size: 13px;
    color: var(--lokantara-muted);
    line-height: 1.6;
    margin: 0;
}

/* CTA Banner */
.jt-cta-banner {
    background: linear-gradient(135deg, #092018 0%, #154737 60%, #1f7a5c 100%);
    border-radius: 24px;
    padding: 50px 40px;
    color: #ffffff;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}
</style>

<!-- 1. Hero Section & Multi-Filter Search -->
<section class="jt-hero">
    <div class="jt-hero-orb jt-hero-orb-1"></div>
    <div class="jt-hero-orb jt-hero-orb-2"></div>

    <div class="container public-container jt-hero-content">
        <div class="jt-hero-badge">
            ✨ Portal Digital Resmi Pariwisata & Ekonomi Kreatif Tegal
        </div>

        <h1 class="jt-hero-title">
            Eksplorasi Keindahan & Pesona Lokal Tegal
        </h1>

        <p class="jt-hero-lead">
            Temukan destinasi wisata eksotis, penginapan nyaman, sentra kuliner legendaris, dan event budaya di wilayah Kabupaten & Kota Tegal dalam satu platform terpadu.
        </p>

        <!-- Glassmorphism Multi-Filter Search Hub -->
        <div class="jt-search-wrapper">
            <form class="jt-search-form" method="GET" action="{{ route('home') }}" role="search">
                <div class="jt-search-group">
                    <label for="public-search">🔍 Kata Kunci</label>
                    <input id="public-search" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Cari wisata, hotel, kuliner...">
                </div>

                <div class="jt-search-group">
                    <label for="public-region">📍 Wilayah / Lokasi</label>
                    <select id="public-region" name="region">
                        <option value="">Semua 21 Wilayah</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" @selected(($filters['region'] ?? null) == $region->id)>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="jt-search-group">
                    <label for="public-service">🏷️ Kategori Layanan</label>
                    <select id="public-service" name="service">
                        <option value="">Semua Layanan</option>
                        @foreach($services as $service)
                            <option value="{{ $service->code }}" @selected(($filters['service'] ?? null) === $service->code)>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="jt-search-btn" type="submit">
                    <span>Cari Sekarang</span>
                    <span>&rarr;</span>
                </button>
            </form>
        </div>

        <!-- Quick Chips / Popular Searches -->
        <div class="jt-chips-row">
            <span class="text-white-50">Populer di Tegal:</span>
            <a href="{{ route('tourism.show', 'purwahamba-indah') }}" class="jt-chip">🏖️ Purwahamba Indah</a>
            <a href="{{ route('accommodation.show', 'hotel-purwahamba-indah') }}" class="jt-chip">🏨 Hotel Purwahamba</a>
            <a href="{{ route('tourism.index') }}" class="jt-chip">♨️ Guci & Curug</a>
            <a href="{{ route('home', ['service' => 'culinary']) }}" class="jt-chip">🍲 Sate Tegal & Teh Poci</a>
            <a href="{{ route('home', ['service' => 'event']) }}" class="jt-chip">🎪 Event Budaya</a>
        </div>
    </div>
</section>

<!-- 2. Quick Platform Stats Bar -->
<div class="container public-container">
    <div class="jt-stats-bar">
        <div class="jt-stats-grid">
            <div class="jt-stat-item">
                <h3>{{ $stats[0]['value'] }}+</h3>
                <p>Mitra Terverifikasi</p>
            </div>
            <div class="jt-stat-item">
                <h3>21</h3>
                <p>Wilayah & Kecamatan Tegal</p>
            </div>
            <div class="jt-stat-item">
                <h3>{{ $stats[2]['value'] }}</h3>
                <p>Layanan Aktif</p>
            </div>
            <div class="jt-stat-item">
                <h3 class="text-warning">100%</h3>
                <p>Legal & Terpercaya</p>
            </div>
        </div>
    </div>
</div>

<!-- 3. Kategori Layanan Terpadu -->
<section class="public-section">
    <div class="container public-container">
        <div class="text-center mb-5">
            <p class="public-eyebrow">Pilihan Eksplorasi</p>
            <h2 class="fs-2 fw-bold text-dark">Kategori Layanan Jelajah Tegal</h2>
            <p class="text-muted" style="max-width: 540px; margin: 0 auto;">Pilih ragam pengalaman yang ingin Anda nikmati selama berkunjung di Tegal.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-2 col-md-4 col-6">
                <a href="{{ route('tourism.index') }}" class="jt-category-card">
                    <div class="jt-cat-icon">🏖️</div>
                    <h4>Wisata</h4>
                    <p>Pantai & Alam</p>
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <a href="{{ route('accommodation.index') }}" class="jt-category-card">
                    <div class="jt-cat-icon">🏨</div>
                    <h4>Penginapan</h4>
                    <p>Hotel & Villa</p>
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <a href="{{ route('home', ['service' => 'culinary']) }}" class="jt-category-card">
                    <div class="jt-cat-icon">🍲</div>
                    <h4>Kuliner</h4>
                    <p>Sate & Poci</p>
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <a href="{{ route('home', ['service' => 'event']) }}" class="jt-category-card">
                    <div class="jt-cat-icon">🎪</div>
                    <h4>Event</h4>
                    <p>Festival Seni</p>
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <a href="{{ route('home', ['service' => 'rental']) }}" class="jt-category-card">
                    <div class="jt-cat-icon">🚗</div>
                    <h4>Rental</h4>
                    <p>Mobil & Motor</p>
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <a href="{{ route('home') }}" class="jt-category-card">
                    <div class="jt-cat-icon">🏢</div>
                    <h4>Mitra</h4>
                    <p>Direktori Resmi</p>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 4. Destinasi Wisata Pilihan -->
@if($featuredTourisms->isNotEmpty())
<section class="public-section pt-0">
    <div class="container public-container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div>
                <p class="public-eyebrow">Rekomendasi Terbaik</p>
                <h2 class="fs-2 fw-bold text-dark">Destinasi Wisata Pilihan</h2>
            </div>
            <a href="{{ route('tourism.index') }}" class="btn btn-outline-lokantara fw-bold">
                Lihat Semua Wisata &rarr;
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredTourisms as $tourism)
                @php
                    $coverMedia = $tourism->media->where('pivot.role', 'cover')->first() ?? $tourism->media->first();
                    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : null;
                    $minTicket = $tourism->offers->min('price');
                @endphp
                <div class="col-lg-4 col-md-6">
                    <article class="jt-showcase-card">
                        <div class="jt-card-image-wrap">
                            @if($coverUrl)
                                <img src="{{ $coverUrl }}" alt="{{ $tourism->name }}">
                            @else
                                <div style="width: 100%; height: 100%; display: grid; place-items: center; background: linear-gradient(135deg, #174d3c, #1f7a5c); color: #fff; font-size: 36px;">
                                    🏖️
                                </div>
                            @endif
                            <div class="jt-card-badges">
                                <span class="jt-card-badge badge-wisata">
                                    📍 {{ $tourism->region?->name ?? 'Tegal' }}
                                </span>
                                @if($tourism->category)
                                    <span class="jt-card-badge" style="background: rgba(255,255,255,0.9); color: #174d3c;">
                                        {{ $tourism->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="jt-card-body">
                            <h3>
                                <a href="{{ route('tourism.show', $tourism->slug) }}" class="text-decoration-none text-dark">
                                    {{ $tourism->name }}
                                </a>
                            </h3>
                            <p>{{ str($tourism->description ?: 'Eksplorasi destinasi wisata unggulan di wilayah Tegal.')->limit(95) }}</p>

                            <div class="jt-card-footer">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Tiket Masuk</small>
                                    <div class="jt-price-tag">
                                        {{ $minTicket ? 'Rp ' . number_format($minTicket, 0, ',', '.') : 'Tiket Masuk Terjangkau' }}
                                    </div>
                                </div>
                                <a href="{{ route('tourism.show', $tourism->slug) }}" class="btn btn-sm btn-lokantara fw-bold px-3">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- 5. Penginapan & Hotel Rekomendasi -->
@if($featuredAccommodations->isNotEmpty())
<section class="public-section pt-0">
    <div class="container public-container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div>
                <p class="public-eyebrow">Istirahat Nyaman</p>
                <h2 class="fs-2 fw-bold text-dark">Penginapan & Hotel Pilihan</h2>
            </div>
            <a href="{{ route('accommodation.index') }}" class="btn btn-outline-lokantara fw-bold">
                Lihat Semua Penginapan &rarr;
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredAccommodations as $hotel)
                @php
                    $coverMedia = $hotel->media->where('pivot.role', 'cover')->first() ?? $hotel->media->first();
                    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : null;
                    $minPrice = $hotel->accommodation->rooms->min('offer.price') ?? 0;
                @endphp
                <div class="col-lg-6">
                    <article class="jt-showcase-card">
                        <div class="row g-0 h-100">
                            <div class="col-md-5">
                                <div class="jt-card-image-wrap h-100" style="min-height: 200px;">
                                    @if($coverUrl)
                                        <img src="{{ $coverUrl }}" alt="{{ $hotel->name }}">
                                    @else
                                        <div style="width: 100%; height: 100%; display: grid; place-items: center; background: linear-gradient(135deg, #1b634b, #0d261e); color: #fff; font-size: 36px;">
                                            🏨
                                        </div>
                                    @endif
                                    <div class="jt-card-badges">
                                        <span class="jt-card-badge badge-hotel">
                                            {{ str($hotel->accommodation->property_type ?? 'Hotel')->headline() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 d-flex flex-column">
                                <div class="jt-card-body">
                                    <div class="d-flex align-items-center gap-1 text-warning mb-1" style="font-size: 13px;">
                                        ★ <strong>{{ number_format($hotel->rating_average, 1) }}</strong>
                                        <span class="text-muted">({{ $hotel->region?->name ?? 'Tegal' }})</span>
                                    </div>
                                    <h3>
                                        <a href="{{ route('accommodation.show', $hotel->slug) }}" class="text-decoration-none text-dark">
                                            {{ $hotel->name }}
                                        </a>
                                    </h3>
                                    <p>{{ str($hotel->description ?: 'Penginapan nyaman dengan pelayanan prima di Tegal.')->limit(85) }}</p>

                                    <div class="jt-card-footer mt-auto">
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 11px;">Mulai dari</small>
                                            <div class="jt-price-tag">
                                                {{ $minPrice ? 'Rp ' . number_format($minPrice, 0, ',', '.') : 'Hubungi Mitra' }}
                                                <small class="text-muted fw-normal" style="font-size: 11px;">/malam</small>
                                            </div>
                                        </div>
                                        <a href="{{ route('accommodation.show', $hotel->slug) }}" class="btn btn-sm btn-lokantara fw-bold px-3">
                                            Pesan Kamar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- 6. Direktori Mitra Terverifikasi -->
<section class="public-section pt-0" aria-labelledby="result-heading">
    <div class="container public-container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div>
                <p class="public-eyebrow">Direktori Resmi</p>
                <h2 id="result-heading" class="fs-2 fw-bold text-dark">
                    {{ request()->hasAny(['q','region','service']) ? 'Hasil Pencarian Mitra' : 'Mitra yang Tersedia' }}
                </h2>
            </div>
            <span class="text-muted fw-semibold">{{ $mitras->total() }} Mitra Aktif</span>
        </div>

        @if($mitras->isEmpty())
            <x-empty-state title="Belum ada Mitra yang cocok" description="Coba ubah kata kunci pencarian atau pilih filter wilayah lain." />
        @else
            <div class="public-card-grid">
                @foreach($mitras as $mitra)
                    <article class="mitra-card" style="transition: all 0.25s ease; border-radius: 18px;">
                        <a href="{{ route('public.mitra.show', $mitra->slug) }}" class="text-decoration-none">
                            <div class="mitra-cover" style="background: linear-gradient(135deg, #124032, #1b634b); color: #fff;">
                                <span>{{ str($mitra->display_name)->substr(0,1)->upper() }}</span>
                            </div>
                        </a>
                        <div class="mitra-card-body d-flex flex-column">
                            <div class="card-meta mb-2">
                                <span>📍 {{ $mitra->region?->name ?? 'Tegal' }}</span>
                                <span class="verified-label">✔ Terverifikasi</span>
                            </div>
                            <h3 class="mb-1">
                                <a href="{{ route('public.mitra.show', $mitra->slug) }}" class="text-decoration-none text-dark fw-bold">
                                    {{ $mitra->display_name }}
                                </a>
                            </h3>
                            <p class="text-muted flex-grow-1" style="font-size: 13px;">
                                {{ str($mitra->description ?: 'Pelaku usaha terverifikasi di platform Jelajah Tegal.')->limit(110) }}
                            </p>
                            <div class="tag-row mb-3">
                                @forelse($mitra->features as $feature)
                                    <span>{{ $feature->serviceType->name }}</span>
                                @empty
                                    <span>Wisata & Penginapan</span>
                                @endforelse
                            </div>
                            <div class="pt-2 border-top mt-auto">
                                <a href="{{ route('public.mitra.show', $mitra->slug) }}" class="btn btn-sm btn-outline-lokantara w-100 fw-bold">
                                    Kunjungi Profil Mitra &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="public-pagination mt-4">{{ $mitras->links() }}</div>
        @endif
    </div>
</section>

<!-- 7. Keunggulan Platform Jelajah Tegal -->
<section class="public-section pt-0">
    <div class="container public-container">
        <div class="text-center mb-5">
            <p class="public-eyebrow">Kenapa Jelajah Tegal?</p>
            <h2 class="fs-2 fw-bold text-dark">Solusi Terpadu Liburan & Bisnis di Tegal</h2>
            <p class="text-muted" style="max-width: 580px; margin: 0 auto;">Kami menghubungkan wisatawan langsung dengan pelaku pariwisata dan ekonomi kreatif lokal secara aman dan transparan.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="jt-feature-box">
                    <div class="jt-feat-icon">🛡️</div>
                    <h4>Mitra Terverifikasi</h4>
                    <p>Semua pelaku usaha melewati verifikasi dokumen legalitas & KYC resmi sebelum dipublikasikan.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="jt-feature-box">
                    <div class="jt-feat-icon">🎟️</div>
                    <h4>Pemesanan Transparan</h4>
                    <p>Harga tiket dan reservasi kamar jelas tanpa biaya tersembunyi dengan konfirmasi instan.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="jt-feature-box">
                    <div class="jt-feat-icon">🗺️</div>
                    <h4>Peta Interaktif Presisi</h4>
                    <p>Didukung koordinat GPS presisi dan integrasi langsung ke petunjuk arah Google Maps.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="jt-feature-box">
                    <div class="jt-feat-icon">🤝</div>
                    <h4>Dukung UMKM Lokal</h4>
                    <p>Setiap kunjungan dan transaksi turut mendukung pertumbuhan pariwisata daerah Tegal.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 8. Call to Action (CTA) Banner -->
<section class="public-section pt-0">
    <div class="container public-container">
        <div class="jt-cta-banner">
            <h2 class="fs-1 fw-bold text-white mb-3">Punya Usaha Wisata, Hotel, atau Kuliner di Tegal?</h2>
            <p class="text-white-50 fs-6 mb-4" style="max-width: 620px; margin-left: auto; margin-right: auto;">
                Bergabunglah bersama ratusan Mitra lainnya di Jelajah Tegal. Jangkau ribuan wisatawan dan tingkatkan omset bisnis Anda sekarang juga.
            </p>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="{{ route('register') }}" class="btn btn-light fw-bold px-4 py-3" style="border-radius: 12px; color: #13352c;">
                    🚀 Gabung Menjadi Mitra
                </a>
                <a href="{{ route('public.about') }}" class="btn btn-outline-light fw-semibold px-4 py-3" style="border-radius: 12px;">
                    Pelajari Selengkapnya
                </a>
            </div>
        </div>
    </div>
</section>

@include('public.partials.platform-sections')
@endsection
