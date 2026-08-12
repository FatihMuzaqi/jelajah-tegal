@extends('layouts.public')

@section('title', 'Jelajah Tegal — Eksplorasi Wisata, Penginapan & Potensi Lokal')
@section('meta-description', 'Temukan destinasi wisata terbaik, penginapan nyaman, kuliner khas, dan event seru di Kabupaten & Kota Tegal dalam satu platform terpadu Jelajah Tegal.')
@if(request()->hasAny(['q','region','service'])) @section('robots','noindex,follow') @endif

@section('content')
<style>
/* Modern Hero Styling with Real Photo Background */
.jt-hero-mockup {
    position: relative;
    background: linear-gradient(180deg, rgba(7, 30, 20, 0.68) 0%, rgba(10, 42, 28, 0.86) 100%), 
                url('{{ asset('images/guci_hero.png') }}') center/cover no-repeat;
    color: #ffffff;
    padding: 90px 0 110px;
    overflow: hidden;
}
.jt-hero-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 22px;
    border-radius: 99px;
    background: rgba(4, 120, 87, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(12px);
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 24px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.jt-hero-title-large {
    font-size: 54px;
    font-weight: 900;
    line-height: 1.12;
    letter-spacing: -0.03em;
    color: #ffffff;
    margin-bottom: 20px;
    text-align: left;
}
.jt-hero-title-large span {
    color: #34d399;
}
.jt-hero-subtitle {
    font-size: 18px;
    line-height: 1.6;
    color: rgba(255,255,255,0.92);
    margin-bottom: 40px;
    max-width: 750px;
    margin-left: 0;
    margin-right: auto;
    text-align: left;
}

/* Glassmorphism 4-Field Search Card */
.jt-search-box-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 12px 14px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.35);
    text-align: left;
    margin-bottom: 30px;
}
.jt-search-grid-layout {
    display: grid;
    grid-template-columns: 2.2fr 1.2fr 1.2fr 1.2fr auto;
    gap: 10px;
    align-items: center;
}
@media (max-width: 991px) {
    .jt-search-grid-layout {
        grid-template-columns: 1fr;
    }
}
.jt-search-field-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-right: 1px solid #e2e8f0;
}
@media (max-width: 991px) {
    .jt-search-field-item {
        border-right: none;
        border-bottom: 1px solid #e2e8f0;
    }
}
.jt-search-field-item:last-child {
    border-right: none;
}
.jt-field-icon {
    font-size: 18px;
    color: #64748b;
}
.jt-field-content {
    flex: 1;
}
.jt-field-content label {
    display: block;
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 2px;
}
.jt-field-content input,
.jt-field-content select {
    width: 100%;
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    outline: none;
    padding: 0;
}
.btn-emerald-search {
    background: #047857;
    color: #ffffff;
    font-weight: 700;
    font-size: 14px;
    padding: 14px 26px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(4,120,87,0.35);
    transition: all 0.25s ease;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-emerald-search:hover {
    background: #065f46;
    color: #ffffff;
    transform: translateY(-2px);
}

/* Quick Search Chips */
.jt-chips-wrapper {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    font-size: 13px;
}
.jt-chip-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    border-radius: 99px;
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.22);
    color: #ffffff;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    backdrop-filter: blur(8px);
    transition: all 0.2s ease;
}
.jt-chip-item:hover {
    background: rgba(255,255,255,0.28);
    color: #ffffff;
    transform: translateY(-2px);
}

/* Section Header Leaf Styling */
.jt-section-title-wrap {
    text-align: center;
    margin-bottom: 45px;
}
.jt-eyebrow-leaf {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #047857;
    font-weight: 800;
    font-size: 18px;
    margin-bottom: 6px;
}
.jt-section-subtext {
    color: #64748b;
    font-size: 15px;
    margin: 0;
}

/* 4 Feature Exploration Cards with Overlapping Icons */
.jt-explore-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}
.jt-explore-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px -10px rgba(4,120,87,0.15);
    border-color: #a7f3d0;
}
.jt-explore-img-wrap {
    height: 170px;
    position: relative;
    overflow: hidden;
}
.jt-explore-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.jt-explore-card:hover .jt-explore-img-wrap img {
    transform: scale(1.08);
}
.jt-explore-body {
    padding: 20px;
    position: relative;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.jt-floating-icon-circle {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    font-size: 20px;
    color: #ffffff;
    margin-top: -46px;
    margin-bottom: 14px;
    border: 3px solid #ffffff;
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    position: relative;
    z-index: 2;
}
.bg-icon-green { background: #059669; }
.bg-icon-orange { background: #ea580c; }
.bg-icon-blue { background: #0284c7; }
.bg-icon-purple { background: #7c3aed; }

.jt-explore-body h3 {
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
}
.jt-explore-body p {
    font-size: 13px;
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 18px;
    flex: 1;
}
.jt-circle-arrow-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    display: grid;
    place-items: center;
    color: #0f172a;
    font-size: 14px;
    margin-left: auto;
    transition: all 0.2s ease;
    text-decoration: none;
}
.jt-explore-card:hover .jt-circle-arrow-btn {
    background: #047857;
    color: #ffffff;
    border-color: #047857;
}

/* Popular Destinations Section */
.jt-popular-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 18px rgba(0,0,0,0.03);
    height: 100%;
}
.jt-popular-card:hover {
    transform: translateY(-6px);
    border-color: #10b981;
    box-shadow: 0 20px 40px -10px rgba(16,185,129,0.18);
}
.jt-pop-img-wrap {
    height: 190px;
    position: relative;
    overflow: hidden;
}
.jt-pop-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.jt-popular-card:hover .jt-pop-img-wrap img {
    transform: scale(1.08);
}
.jt-pop-rating-badge {
    position: absolute;
    top: 14px;
    right: 14px;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    border-radius: 99px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}
.jt-pop-category-pill {
    position: absolute;
    bottom: 14px;
    left: 14px;
    background: #047857;
    color: #ffffff;
    border-radius: 99px;
    padding: 4px 14px;
    font-size: 11px;
    font-weight: 700;
}
.jt-pop-body {
    padding: 20px;
}
.jt-pop-body h3 {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
}
.jt-pop-location {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 10px;
    font-weight: 600;
}
.jt-pop-body p {
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
    margin: 0;
}

/* 3-Column Bottom Grid Box Components */
.jt-box-card {
    border-radius: 24px;
    padding: 28px;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.jt-box-ai {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}
.jt-box-event {
    background: #fffbeb;
    border: 1px solid #fef3c7;
}
.jt-box-kuliner {
    background: #fef2f2;
    border: 1px solid #fecaca;
}

.jt-box-header {
    margin-bottom: 16px;
}
.jt-box-header h3 {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.jt-box-header p {
    font-size: 13px;
    color: #64748b;
    margin: 0;
    line-height: 1.5;
}

/* Sub-Thumbnails Grid for AI Recommendation */
.jt-ai-thumbs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 20px;
    flex: 1;
}
.jt-ai-thumb-item {
    text-decoration: none;
    color: #0f172a;
    text-align: center;
}
.jt-ai-thumb-item img {
    width: 100%;
    height: 75px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 6px;
}
.jt-ai-thumb-item h5 {
    font-size: 12px;
    font-weight: 800;
    margin: 0;
}
.jt-ai-thumb-item span {
    font-size: 10px;
    color: #64748b;
    display: block;
}

/* Event List Items */
.jt-event-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
    flex: 1;
}
.jt-event-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    padding: 10px;
    border-radius: 14px;
    border: 1px solid #fef3c7;
}
.jt-event-item img {
    width: 54px;
    height: 54px;
    border-radius: 10px;
    object-fit: cover;
}
.jt-event-item h5 {
    font-size: 13px;
    font-weight: 800;
    margin: 0 0 2px;
    color: #0f172a;
}
.jt-event-item p {
    font-size: 11px;
    color: #d97706;
    margin: 0;
    font-weight: 700;
}
.jt-event-item span {
    font-size: 11px;
    color: #64748b;
    display: block;
}

/* Kuliner Round Circles */
.jt-kuliner-circles {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    text-align: center;
    margin-bottom: 20px;
    flex: 1;
    align-items: center;
}
.jt-kuliner-circle-item img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 8px;
    border: 3px solid #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.jt-kuliner-circle-item h5 {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.btn-box-outline {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 99px;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    text-decoration: none;
    text-align: center;
    display: block;
    width: 100%;
    margin-top: auto;
    transition: all 0.2s ease;
}
.btn-box-outline:hover {
    background: #047857;
    color: #ffffff;
    border-color: #047857;
}

/* Category Filter Tabs Bar */
.jt-mitra-tab-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-weight: 600;
    font-size: 13px;
    padding: 6px 16px;
    border-radius: 99px;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.jt-mitra-tab-pill.active, .jt-mitra-tab-pill:hover {
    background: #064e3b;
    color: #ffffff;
    border-color: #064e3b;
}

/* Bottom Newsletter Banner */
.jt-newsletter-banner-footer {
    background: #044e38;
    color: #ffffff;
    border-radius: 20px;
    padding: 30px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-top: 60px;
}
@media (max-width: 991px) {
    .jt-newsletter-banner-footer {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<!-- 1. Hero Section & Multi-Filter Search Bar -->
<section class="jt-hero-mockup">
    <div class="container public-container text-start">
        <div class="jt-hero-badge-pill">
            <i class="fa-solid fa-compass text-warning me-1"></i> Portal Digital Resmi Pariwisata & Ekonomi Kreatif Tegal
        </div>

        <h1 class="jt-hero-title-large">
            Jelajahi Tegal,<br><span>Temukan Ceritamu</span>
        </h1>

        <p class="jt-hero-subtitle">
            Temukan destinasi wisata, kuliner, penginapan, dan pengalaman lokal terbaik di Tegal dalam satu platform.
        </p>

        <!-- Floating Search Box Card -->
        <div class="jt-search-box-card">
            <form class="jt-search-grid-layout" method="GET" action="{{ route('home') }}" role="search">
                <div class="jt-search-field-item">
                    <i class="fa-solid fa-magnifying-glass jt-field-icon"></i>
                    <div class="jt-field-content">
                        <label for="public-search">Mau ke mana hari ini?</label>
                        <input id="public-search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari destinasi, kuliner, hotel, atau aktivitas...">
                    </div>
                </div>

                <div class="jt-search-field-item">
                    <i class="fa-solid fa-location-dot jt-field-icon"></i>
                    <div class="jt-field-content">
                        <label for="public-region">Lokasi</label>
                        <select id="public-region" name="region">
                            <option value="">Semua Wilayah</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected(($filters['region'] ?? null) == $region->id)>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="jt-search-field-item">
                    <i class="fa-solid fa-border-all jt-field-icon"></i>
                    <div class="jt-field-content">
                        <label for="public-service">Kategori</label>
                        <select id="public-service" name="service">
                            <option value="">Semua Kategori</option>
                            @foreach($services as $service)
                                <option value="{{ $service->code }}" @selected(($filters['service'] ?? null) === $service->code)>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="jt-search-field-item" onclick="document.getElementById('public-search-date')?.showPicker?.() || document.getElementById('public-search-date')?.focus()">
                    <i class="fa-solid fa-calendar-days jt-field-icon"></i>
                    <div class="jt-field-content">
                        <label for="public-search-date">Tanggal</label>
                        <input type="date" id="public-search-date" name="date" value="{{ request('date') }}" min="{{ date('Y-m-d') }}" style="cursor: pointer;">
                    </div>
                </div>

                <button class="btn-emerald-search" type="submit">
                    <span>Cari Sekarang</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <!-- Popular Chips -->
        <div class="jt-chips-wrapper">
            <span class="text-white-50"><i class="fa-solid fa-fire text-danger me-1"></i> Sedang Populer</span>
            <a href="{{ route('tourism.show', 'purwahamba-indah') }}" class="jt-chip-item">
                <i class="fa-solid fa-umbrella-beach text-warning me-1"></i> Purwahamba Indah
            </a>
            <a href="{{ route('tourism.index') }}" class="jt-chip-item">
                <i class="fa-solid fa-water text-cyan me-1"></i> Guci & Curug
            </a>
            <a href="{{ route('home', ['service' => 'culinary']) }}" class="jt-chip-item">
                <i class="fa-solid fa-utensils text-warning me-1"></i> Sate Tegal
            </a>
            <a href="{{ route('accommodation.index') }}" class="jt-chip-item">
                <i class="fa-solid fa-hotel text-info me-1"></i> Hotel Pilihan
            </a>
            <a href="{{ route('home', ['service' => 'event']) }}" class="jt-chip-item">
                <i class="fa-solid fa-calendar-day text-danger me-1"></i> Event Budaya
            </a>
        </div>
    </div>
</section>

<!-- 2. Section 1: Jelajahi Tegal (4 Cards) -->
<section class="public-section py-5">
    <div class="container public-container">
        <div class="jt-section-title-wrap">
            <div class="jt-eyebrow-leaf">
                <i class="fa-solid fa-leaf text-success"></i> Jelajahi Tegal <i class="fa-solid fa-leaf text-success"></i>
            </div>
            <p class="jt-section-subtext">Temukan berbagai hal menarik yang bisa kamu jelajahi di Tegal</p>
        </div>

        <div class="row g-4">
            <!-- Card 1: Wisata -->
            <div class="col-lg-3 col-md-6">
                <div class="jt-explore-card">
                    <div class="jt-explore-img-wrap">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800&auto=format&fit=crop" alt="Wisata Tegal">
                    </div>
                    <div class="jt-explore-body">
                        <div class="jt-floating-icon-circle bg-icon-green">
                            <i class="fa-solid fa-umbrella-beach"></i>
                        </div>
                        <h3>Wisata</h3>
                        <p>Temukan destinasi wisata alam, pantai, pegunungan, dan lebih banyak lagi.</p>
                        <a href="{{ route('tourism.index') }}" class="jt-circle-arrow-btn" aria-label="Lihat Wisata">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 2: Kuliner -->
            <div class="col-lg-3 col-md-6">
                <div class="jt-explore-card">
                    <div class="jt-explore-img-wrap">
                        <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=800&auto=format&fit=crop" alt="Kuliner Tegal">
                    </div>
                    <div class="jt-explore-body">
                        <div class="jt-floating-icon-circle bg-icon-orange">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                        <h3>Kuliner</h3>
                        <p>Nikmati berbagai kuliner khas Tegal yang lezat dan menggugah selera.</p>
                        <a href="{{ route('home', ['service' => 'culinary']) }}" class="jt-circle-arrow-btn" aria-label="Lihat Kuliner">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Penginapan -->
            <div class="col-lg-3 col-md-6">
                <div class="jt-explore-card">
                    <div class="jt-explore-img-wrap">
                        <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=800&auto=format&fit=crop" alt="Penginapan Tegal">
                    </div>
                    <div class="jt-explore-body">
                        <div class="jt-floating-icon-circle bg-icon-blue">
                            <i class="fa-solid fa-bed"></i>
                        </div>
                        <h3>Penginapan</h3>
                        <p>Temukan penginapan nyaman dari hotel, homestay, hingga villa terbaik.</p>
                        <a href="{{ route('accommodation.index') }}" class="jt-circle-arrow-btn" aria-label="Lihat Penginapan">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 4: Event -->
            <div class="col-lg-3 col-md-6">
                <div class="jt-explore-card">
                    <div class="jt-explore-img-wrap">
                        <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=800&auto=format&fit=crop" alt="Event Tegal">
                    </div>
                    <div class="jt-explore-body">
                        <div class="jt-floating-icon-circle bg-icon-purple">
                            <i class="fa-solid fa-masks-theater"></i>
                        </div>
                        <h3>Event</h3>
                        <p>Jangan lewatkan berbagai event menarik dan budaya lokal yang autentik.</p>
                        <a href="{{ route('home', ['service' => 'event']) }}" class="jt-circle-arrow-btn" aria-label="Lihat Event">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Section 2: Destinasi Populer -->
<section class="public-section py-4">
    <div class="container public-container">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <h2 class="fs-2 fw-extrabold text-dark m-0">Destinasi Populer</h2>
            <a href="{{ route('tourism.index') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                Lihat Semua Destinasi <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4 position-relative">
            @if(isset($featuredTourisms) && $featuredTourisms->isNotEmpty())
                @foreach($featuredTourisms as $tourism)
                    @php
                        $coverMedia = $tourism->media->where('pivot.role', 'cover')->first() ?? $tourism->media->first();
                        $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?q=80&w=800&auto=format&fit=crop';
                        $rating = $tourism->rating_average > 0 ? number_format($tourism->rating_average, 1) : '4.8';
                        $regionName = $tourism->region?->name ?? 'Tegal';
                        $mitraName = $tourism->mitra?->display_name ?? 'Mitra Terverifikasi';
                    @endphp
                    <div class="col-lg-3 col-md-6">
                        <div class="jt-popular-card">
                            <div class="jt-pop-img-wrap">
                                <img src="{{ $coverUrl }}" alt="{{ $tourism->name }}">
                                <span class="jt-pop-rating-badge"><i class="fa-solid fa-star text-warning me-1"></i>{{ $rating }}</span>
                                <span class="jt-pop-category-pill">{{ $tourism->category?->name ?? 'Wisata' }}</span>
                            </div>
                            <div class="jt-pop-body">
                                <h3>
                                    <a href="{{ route('tourism.show', $tourism->slug) }}" class="text-decoration-none text-dark">
                                        {{ $tourism->name }}
                                    </a>
                                </h3>
                                <div class="jt-pop-location">
                                    <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $regionName }}
                                    <span class="text-muted d-block fw-normal fs-8">Oleh: {{ $mitraName }}</span>
                                </div>
                                <p>{{ str($tourism->description)->limit(70) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Fallback Mockup Cards -->
                <div class="col-lg-3 col-md-6">
                    <div class="jt-popular-card">
                        <div class="jt-pop-img-wrap">
                            <img src="https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?q=80&w=800&auto=format&fit=crop" alt="Guci Hot Spring">
                            <span class="jt-pop-rating-badge"><i class="fa-solid fa-star text-warning me-1"></i>4.8</span>
                            <span class="jt-pop-category-pill">Alam</span>
                        </div>
                        <div class="jt-pop-body">
                            <h3>Guci Hot Spring</h3>
                            <div class="jt-pop-location">Bumijawa, Tegal <span class="text-muted d-block fw-normal fs-8">Oleh: PT Guci Natural Resort Tegal</span></div>
                            <p>Pemandian air panas alami dengan pemandangan indah dan udara sejuk.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="jt-popular-card">
                        <div class="jt-pop-img-wrap">
                            <img src="https://images.unsplash.com/photo-1519046904884-53103b34b206?q=80&w=800&auto=format&fit=crop" alt="Pantai Alam Indah">
                            <span class="jt-pop-rating-badge"><i class="fa-solid fa-star text-warning me-1"></i>4.6</span>
                            <span class="jt-pop-category-pill" style="background: #0d9488;">Pantai</span>
                        </div>
                        <div class="jt-pop-body">
                            <h3>Pantai Alam Indah</h3>
                            <div class="jt-pop-location">Kota Tegal <span class="text-muted d-block fw-normal fs-8">Oleh: Mitra Wisata Utama Tegal</span></div>
                            <p>Pantai indah dengan pasir luas dan berbagai wahana menarik.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="jt-popular-card">
                        <div class="jt-pop-img-wrap">
                            <img src="https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=800&auto=format&fit=crop" alt="Curug Putri">
                            <span class="jt-pop-rating-badge"><i class="fa-solid fa-star text-warning me-1"></i>4.7</span>
                            <span class="jt-pop-category-pill">Curug</span>
                        </div>
                        <div class="jt-pop-body">
                            <h3>Curug Putri</h3>
                            <div class="jt-pop-location">Bumijawa, Tegal <span class="text-muted d-block fw-normal fs-8">Oleh: CV Pesona Alam Bumijawa</span></div>
                            <p>Air terjun yang menawan dengan suasana alami yang asri.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="jt-popular-card">
                        <div class="jt-pop-img-wrap">
                            <img src="https://images.unsplash.com/photo-1476514525535-ce74f45814de?q=80&w=800&auto=format&fit=crop" alt="Danau Beko">
                            <span class="jt-pop-rating-badge"><i class="fa-solid fa-star text-warning me-1"></i>4.5</span>
                            <span class="jt-pop-category-pill" style="background: #0284c7;">Keluarga</span>
                        </div>
                        <div class="jt-pop-body">
                            <h3>Danau Beko</h3>
                            <div class="jt-pop-location">Margasari, Tegal <span class="text-muted d-block fw-normal fs-8">Oleh: Pokdarwis Danau Beko Margasari</span></div>
                            <p>Tempat rekreasi keluarga dengan danau buatan yang menyenangkan.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Section: Mitra Paling Populer (Exact Reference Image Design) -->
<section class="public-section py-5" style="background: #fafafa;">
    <div class="container public-container">
        <!-- Header Row -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fs-3 fw-extrabold text-dark m-0 d-flex align-items-center gap-2">
                    <span class="fs-4">🤝</span> Mitra Paling Populer
                </h2>
                <p class="text-muted mb-0 fs-7" style="font-weight: 500;">Partner terpercaya pilihan wisatawan Jelajah Tegal</p>
            </div>

            <!-- Filter Tabs & Link -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="d-flex gap-2 me-2">
                    <a href="{{ route('home') }}" class="jt-mitra-tab-pill active">Semua</a>
                    <a href="{{ route('tourism.index') }}" class="jt-mitra-tab-pill"><i class="fa-solid fa-compass text-emerald me-1"></i> Wisata</a>
                    <a href="{{ route('accommodation.index') }}" class="jt-mitra-tab-pill"><i class="fa-solid fa-hotel text-primary me-1"></i> Penginapan</a>
                    <a href="{{ route('home', ['service' => 'culinary']) }}" class="jt-mitra-tab-pill"><i class="fa-solid fa-utensils text-warning me-1"></i> Kuliner</a>
                    <a href="{{ route('home') }}#mitra-list-section" class="jt-mitra-tab-pill">Lainnya</a>
                </div>
                <a href="{{ route('home') }}#mitra-list-section" class="fw-bold text-success text-decoration-none fs-7 d-flex align-items-center gap-1">
                    Lihat Semua Mitra <i class="fa-solid fa-arrow-right fs-8"></i>
                </a>
            </div>
        </div>

        <!-- 4 Column Cards Grid -->
        <div class="row g-4 pt-1">
            @if(isset($popularMitras) && $popularMitras->isNotEmpty())
                @foreach($popularMitras->take(4) as $mitra)
                    @php
                        $coverUrl = $mitra->bannerMedia ? asset('storage/' . $mitra->bannerMedia->object_key) : 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=800&auto=format&fit=crop';
                        $logoUrl = $mitra->logoMedia ? asset('storage/' . $mitra->logoMedia->object_key) : null;
                        
                        $primaryFeature = $mitra->features->first()?->serviceType?->name ?? 'Layanan';
                        $catIcon = match(strtolower($primaryFeature)) {
                            'wisata' => 'fa-compass',
                            'penginapan' => 'fa-hotel',
                            'kuliner' => 'fa-utensils',
                            'rental' => 'fa-car',
                            default => 'fa-store',
                        };
                    @endphp
                    <div class="col-lg-3 col-md-6">
                        <div class="jt-mitra-ref-card">
                            <!-- Cover Photo -->
                            <div class="jt-mitra-ref-cover">
                                <img src="{{ $coverUrl }}" alt="{{ $mitra->display_name }}">
                                <div class="jt-mitra-ref-verified">
                                    <i class="fa-solid fa-check fs-8"></i> Mitra Terverifikasi
                                </div>
                                <button class="jt-mitra-ref-heart" type="button" aria-label="Simpan">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div>

                            <!-- Card Body -->
                            <div class="jt-mitra-ref-body">
                                <div class="jt-mitra-ref-header-row">
                                    @if($logoUrl)
                                        <img src="{{ $logoUrl }}" alt="{{ $mitra->display_name }}" class="jt-mitra-ref-logo">
                                    @else
                                        <div class="jt-mitra-ref-logo-fallback">
                                            {{ str($mitra->display_name)->substr(0,1)->upper() }}
                                        </div>
                                    @endif
                                    <div class="jt-mitra-ref-title-wrap">
                                        <h3 class="jt-mitra-ref-title">{{ $mitra->display_name }}</h3>
                                        <div class="jt-mitra-ref-category">
                                            <i class="fa-solid {{ $catIcon }} fs-8"></i> {{ $primaryFeature }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="jt-mitra-ref-location">
                                    <i class="fa-solid fa-location-dot text-secondary"></i> {{ $mitra->region?->name ?? 'Kabupaten Tegal' }}
                                </div>

                                <!-- Rating & Visitor Stats -->
                                <div class="jt-mitra-ref-stats">
                                    <div class="jt-mitra-ref-rating">
                                        <i class="fa-solid fa-star"></i> 4.9 <span class="text-muted fw-normal">(320 ulasan)</span>
                                    </div>
                                    <div class="jt-mitra-ref-visitors">
                                        <i class="fa-solid fa-users fs-8"></i> 1.2K+ <span class="d-none d-xl-inline">pengunjung</span>
                                    </div>
                                </div>

                                <!-- Button Action -->
                                <a href="{{ route('public.mitra.show', $mitra->slug) }}" class="jt-mitra-ref-btn">
                                    Lihat Profil Mitra <i class="fa-solid fa-arrow-right fs-8"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Mockup Cards matching the user's reference image exactly -->
                <div class="col-lg-3 col-md-6">
                    <div class="jt-mitra-ref-card">
                        <div class="jt-mitra-ref-cover">
                            <img src="https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?q=80&w=800&auto=format&fit=crop" alt="Purwahamba Indah">
                            <div class="jt-mitra-ref-verified">
                                <i class="fa-solid fa-check fs-8"></i> Mitra Terverifikasi
                            </div>
                            <button class="jt-mitra-ref-heart" type="button" aria-label="Simpan"><i class="fa-regular fa-heart"></i></button>
                        </div>
                        <div class="jt-mitra-ref-body">
                            <div class="jt-mitra-ref-header-row">
                                <div class="jt-mitra-ref-logo-fallback" style="background:#0284c7;">P</div>
                                <div class="jt-mitra-ref-title-wrap">
                                    <h3 class="jt-mitra-ref-title">Purwahamba Indah</h3>
                                    <div class="jt-mitra-ref-category"><i class="fa-solid fa-compass fs-8"></i> Destinasi Wisata Alam</div>
                                </div>
                            </div>
                            <div class="jt-mitra-ref-location"><i class="fa-solid fa-location-dot text-secondary"></i> Kramat, Kabupaten Tegal</div>
                            <div class="jt-mitra-ref-stats">
                                <div class="jt-mitra-ref-rating"><i class="fa-solid fa-star"></i> 4.8 <span class="text-muted fw-normal">(324 ulasan)</span></div>
                                <div class="jt-mitra-ref-visitors"><i class="fa-solid fa-users fs-8"></i> 1.2K+ pengunjung</div>
                            </div>
                            <a href="{{ route('home') }}" class="jt-mitra-ref-btn">Lihat Profil Mitra <i class="fa-solid fa-arrow-right fs-8"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="jt-mitra-ref-card">
                        <div class="jt-mitra-ref-cover">
                            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800&auto=format&fit=crop" alt="Hotel Grand Diana">
                            <div class="jt-mitra-ref-verified">
                                <i class="fa-solid fa-check fs-8"></i> Mitra Terverifikasi
                            </div>
                            <button class="jt-mitra-ref-heart" type="button" aria-label="Simpan"><i class="fa-regular fa-heart"></i></button>
                        </div>
                        <div class="jt-mitra-ref-body">
                            <div class="jt-mitra-ref-header-row">
                                <div class="jt-mitra-ref-logo-fallback" style="background:#1e293b;">GD</div>
                                <div class="jt-mitra-ref-title-wrap">
                                    <h3 class="jt-mitra-ref-title">Hotel Grand Diana</h3>
                                    <div class="jt-mitra-ref-category"><i class="fa-solid fa-hotel fs-8"></i> Hotel & Penginapan</div>
                                </div>
                            </div>
                            <div class="jt-mitra-ref-location"><i class="fa-solid fa-location-dot text-secondary"></i> Slawi, Kabupaten Tegal</div>
                            <div class="jt-mitra-ref-stats">
                                <div class="jt-mitra-ref-rating"><i class="fa-solid fa-star"></i> 4.7 <span class="text-muted fw-normal">(218 ulasan)</span></div>
                                <div class="jt-mitra-ref-visitors"><i class="fa-solid fa-users fs-8"></i> 980+ pengunjung</div>
                            </div>
                            <a href="{{ route('home') }}" class="jt-mitra-ref-btn">Lihat Profil Mitra <i class="fa-solid fa-arrow-right fs-8"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="jt-mitra-ref-card">
                        <div class="jt-mitra-ref-cover">
                            <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=800&auto=format&fit=crop" alt="Sate Kambing Muda H. Taslim">
                            <div class="jt-mitra-ref-verified">
                                <i class="fa-solid fa-check fs-8"></i> Mitra Terverifikasi
                            </div>
                            <button class="jt-mitra-ref-heart" type="button" aria-label="Simpan"><i class="fa-regular fa-heart"></i></button>
                        </div>
                        <div class="jt-mitra-ref-body">
                            <div class="jt-mitra-ref-header-row">
                                <div class="jt-mitra-ref-logo-fallback" style="background:#b91c1c;">HT</div>
                                <div class="jt-mitra-ref-title-wrap">
                                    <h3 class="jt-mitra-ref-title">Sate Kambing Muda H. Taslim</h3>
                                    <div class="jt-mitra-ref-category"><i class="fa-solid fa-utensils fs-8"></i> Kuliner Khas Tegal</div>
                                </div>
                            </div>
                            <div class="jt-mitra-ref-location"><i class="fa-solid fa-location-dot text-secondary"></i> Slawi, Kabupaten Tegal</div>
                            <div class="jt-mitra-ref-stats">
                                <div class="jt-mitra-ref-rating"><i class="fa-solid fa-star"></i> 4.9 <span class="text-muted fw-normal">(512 ulasan)</span></div>
                                <div class="jt-mitra-ref-visitors"><i class="fa-solid fa-users fs-8"></i> 2.3K+ pengunjung</div>
                            </div>
                            <a href="{{ route('home') }}" class="jt-mitra-ref-btn">Lihat Profil Mitra <i class="fa-solid fa-arrow-right fs-8"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="jt-mitra-ref-card">
                        <div class="jt-mitra-ref-cover">
                            <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=800&auto=format&fit=crop" alt="d'Pine Resort">
                            <div class="jt-mitra-ref-verified">
                                <i class="fa-solid fa-check fs-8"></i> Mitra Terverifikasi
                            </div>
                            <button class="jt-mitra-ref-heart" type="button" aria-label="Simpan"><i class="fa-regular fa-heart"></i></button>
                        </div>
                        <div class="jt-mitra-ref-body">
                            <div class="jt-mitra-ref-header-row">
                                <div class="jt-mitra-ref-logo-fallback" style="background:#064e3b;">DP</div>
                                <div class="jt-mitra-ref-title-wrap">
                                    <h3 class="jt-mitra-ref-title">d'Pine Resort</h3>
                                    <div class="jt-mitra-ref-category"><i class="fa-solid fa-tree fs-8"></i> Resort & Penginapan</div>
                                </div>
                            </div>
                            <div class="jt-mitra-ref-location"><i class="fa-solid fa-location-dot text-secondary"></i> Guci, Kabupaten Tegal</div>
                            <div class="jt-mitra-ref-stats">
                                <div class="jt-mitra-ref-rating"><i class="fa-solid fa-star"></i> 4.6 <span class="text-muted fw-normal">(187 ulasan)</span></div>
                                <div class="jt-mitra-ref-visitors"><i class="fa-solid fa-users fs-8"></i> 750+ pengunjung</div>
                            </div>
                            <a href="{{ route('home') }}" class="jt-mitra-ref-btn">Lihat Profil Mitra <i class="fa-solid fa-arrow-right fs-8"></i></a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- 4. Section 3: 3-Column Feature Grid (AI, Event, Kuliner) -->
<section class="public-section py-4">
    <div class="container public-container">
        <div class="row g-4">
            <!-- Box 1: Rekomendasi AI -->
            <div class="col-lg-4">
                <div class="jt-box-card jt-box-ai">
                    <div class="jt-box-header">
                        <h3>
                            <i class="fa-solid fa-wand-magic-sparkles text-primary"></i> 
                            Rekomendasi Untukmu 
                            <span class="badge bg-primary text-white rounded-pill px-2 py-1 fs-8">AI</span>
                        </h3>
                        <p>Berdasarkan minatmu, kami menemukan beberapa tempat yang mungkin kamu suka.</p>
                    </div>

                    <div class="jt-ai-thumbs-grid">
                        <a href="{{ route('tourism.index') }}" class="jt-ai-thumb-item">
                            <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=400&auto=format&fit=crop" alt="Bukit Bintang">
                            <h5>Bukit Bintang</h5>
                            <span>Bumijawa</span>
                        </a>
                        <a href="{{ route('tourism.index') }}" class="jt-ai-thumb-item">
                            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=400&auto=format&fit=crop" alt="Waduk Cacaban">
                            <h5>Waduk Cacaban</h5>
                            <span>Lebaksiu</span>
                        </a>
                        <a href="{{ route('tourism.index') }}" class="jt-ai-thumb-item">
                            <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=400&auto=format&fit=crop" alt="Pagaralang">
                            <h5>Pagaralang</h5>
                            <span>Tegal</span>
                        </a>
                    </div>

                    <a href="{{ route('tourism.index') }}" class="btn-box-outline">
                        Lihat Rekomendasi Lainnya <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Box 2: Event Terdekat -->
            <div class="col-lg-4">
                <div class="jt-box-card jt-box-event">
                    <div class="jt-box-header">
                        <h3>
                            <i class="fa-solid fa-calendar-days text-amber-600"></i> Event Terdekat
                        </h3>
                    </div>

                    <div class="jt-event-list">
                        <div class="jt-event-item">
                            <img src="https://images.unsplash.com/photo-1533105079780-92b9be482077?q=80&w=400&auto=format&fit=crop" alt="Karnaval Budaya Tegal">
                            <div>
                                <h5>Karnaval Budaya Tegal</h5>
                                <p>18 Agustus 2026</p>
                                <span>Alun-alun Kabupaten Tegal</span>
                            </div>
                        </div>

                        <div class="jt-event-item">
                            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=400&auto=format&fit=crop" alt="Festival Kuliner Tegal">
                            <div>
                                <h5>Festival Kuliner Tegal</h5>
                                <p>25–27 Agustus 2026</p>
                                <span>Taman Rakyat Slawi</span>
                            </div>
                        </div>

                        <div class="jt-event-item">
                            <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=400&auto=format&fit=crop" alt="Larung Sesaji Pantai">
                            <div>
                                <h5>Larung Sesaji Pantai</h5>
                                <p>30 Agustus 2026</p>
                                <span>Pantai Alam Indah</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('home', ['service' => 'event']) }}" class="btn-box-outline">
                        Lihat Semua Event <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Box 3: Kuliner Khas Tegal -->
            <div class="col-lg-4">
                <div class="jt-box-card jt-box-kuliner">
                    <div class="jt-box-header">
                        <h3>
                            <i class="fa-solid fa-utensils text-rose-600"></i> Kuliner Khas Tegal
                        </h3>
                        <p>Jangan lewatkan cita rasa khas Tegal yang melegenda dan wajib dicoba!</p>
                    </div>

                    <div class="jt-kuliner-circles">
                        <div class="jt-kuliner-circle-item">
                            <img src="https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?q=80&w=300&auto=format&fit=crop" alt="Sate Kambing">
                            <h5>Sate Kambing</h5>
                        </div>
                        <div class="jt-kuliner-circle-item">
                            <img src="https://images.unsplash.com/photo-1541544741938-0af808871cc0?q=80&w=300&auto=format&fit=crop" alt="Tahu Aci">
                            <h5>Tahu Aci</h5>
                        </div>
                        <div class="jt-kuliner-circle-item">
                            <img src="https://images.unsplash.com/photo-1576092768241-dec231879fc3?q=80&w=300&auto=format&fit=crop" alt="Teh Poci">
                            <h5>Teh Poci</h5>
                        </div>
                    </div>

                    <a href="{{ route('home', ['service' => 'culinary']) }}" class="btn-box-outline">
                        Jelajahi Kuliner Lainnya <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. Section 4: Newsletter Footer Banner -->
<div class="container public-container pb-5">
    <div class="jt-newsletter-banner-footer">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-white text-emerald p-3 rounded-circle" style="width: 54px; height: 54px; display: grid; place-items: center; background: rgba(255,255,255,0.15); color: #ffffff;">
                <i class="fa-solid fa-paper-plane fs-4"></i>
            </div>
            <div>
                <h3 class="fs-4 fw-extrabold m-0 text-white">Dapatkan Info Wisata Tegal Terbaru</h3>
                <p class="m-0 text-white-50 fs-7">Berlangganan newsletter untuk mendapatkan info destinasi, event, dan promo menarik dari Jelajah Tegal.</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="input-group">
                <input type="email" class="form-control px-3 py-2 rounded-start-pill border-0" placeholder="Masukkan email kamu" style="min-width: 240px; font-size: 13px;">
                <button class="btn btn-emerald px-4 rounded-end-pill fw-bold" style="background: #059669; color: #ffffff;" type="button">Berlangganan</button>
            </div>
            <div class="d-flex gap-2 text-white-50 fs-5 ms-2">
                <a href="#" class="text-white-50"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="text-white-50"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="text-white-50"><i class="fa-brands fa-youtube"></i></a>
                <a href="#" class="text-white-50"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>
    </div>
</div>

@endsection
