@extends('layouts.public')

@section('title', 'Jelajah Tegal — Eksplorasi Wisata, Penginapan & Potensi Lokal')
@section('meta-description', 'Temukan destinasi wisata terbaik, penginapan nyaman, kuliner khas, dan event seru di Kabupaten & Kota Tegal dalam satu platform terpadu Jelajah Tegal.')
@if(request()->hasAny(['q','region','service'])) @section('robots','noindex,follow') @endif

@section('content')
<style>
/* Modern Responsive Hero Styling with Real Photo Background */
.jt-hero-mockup {
    position: relative;
    background: linear-gradient(180deg, rgba(7, 30, 20, 0.72) 0%, rgba(10, 42, 28, 0.88) 100%), 
                url('{{ asset('images/guci_hero.png') }}') center/cover no-repeat;
    color: #ffffff;
    padding: clamp(48px, 8vw, 95px) 0 clamp(60px, 9vw, 115px);
    overflow: hidden;
}
.jt-hero-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 18px;
    border-radius: 99px;
    background: rgba(4, 120, 87, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(12px);
    color: #ffffff;
    font-size: clamp(11px, 2.5vw, 13px);
    font-weight: 700;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    line-height: 1.4;
    max-width: 100%;
}
.jt-hero-title-large {
    font-size: clamp(28px, 6vw, 54px);
    font-weight: 900;
    line-height: 1.15;
    letter-spacing: -0.03em;
    color: #ffffff;
    margin-bottom: 16px;
    text-align: left;
}
.jt-hero-title-large span {
    color: #34d399;
}
.jt-hero-subtitle {
    font-size: clamp(14px, 2.5vw, 18px);
    line-height: 1.6;
    color: rgba(255,255,255,0.92);
    margin-bottom: 24px;
    max-width: 750px;
    margin-left: 0;
    margin-right: auto;
    text-align: left;
}

/* Hero Action Buttons (Rekomendasi AI & Pencarian) */
.jt-hero-actions-wrap {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}
.jt-hero-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 26px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 15px;
    line-height: 1.3;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    white-space: nowrap;
}
.jt-hero-btn-ai {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4) !important;
}
.jt-hero-btn-ai:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 32px rgba(5, 150, 105, 0.6) !important;
    color: #ffffff !important;
}
.jt-hero-btn-search {
    background: #ffffff !important;
    color: #0f172a !important;
    border: 1px solid rgba(255, 255, 255, 0.9) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.14) !important;
}
.jt-hero-btn-search:hover {
    transform: translateY(-3px);
    background: #f0fdf4 !important;
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.2) !important;
    color: #047857 !important;
}
.jt-badge-tag {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    background: #ffffff;
    color: #047857;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.02em;
    margin-left: 2px;
}

@media (max-width: 576px) {
    .jt-hero-actions-wrap {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
        gap: 12px;
    }
    .jt-hero-btn {
        width: 100%;
        padding: 13px 16px;
        font-size: 14.5px;
        white-space: normal;
        text-align: center;
    }
    .jt-badge-tag {
        font-size: 10px;
        padding: 2px 8px;
    }
}
@media (min-width: 577px) and (max-width: 768px) {
    .jt-hero-btn {
        padding: 12px 18px;
        font-size: 14px;
    }
    .jt-badge-tag {
        font-size: 10px;
        padding: 2px 7px;
    }
}

/* Quick Search Chips - Smooth Scroll on Mobile */
.jt-chips-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 6px;
}
.jt-chips-wrapper::-webkit-scrollbar {
    display: none;
}
.jt-chip-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 99px;
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.22);
    color: #ffffff;
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
    backdrop-filter: blur(8px);
    transition: all 0.2s ease;
    flex-shrink: 0;
}
.jt-chip-item:hover {
    background: rgba(255,255,255,0.28);
    color: #ffffff;
    transform: translateY(-2px);
}

/* Section Header Leaf Styling */
.jt-section-title-wrap {
    text-align: center;
    margin-bottom: 35px;
}
.jt-eyebrow-leaf {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #047857;
    font-weight: 800;
    font-size: clamp(16px, 3.5vw, 19px);
    margin-bottom: 6px;
}
.jt-section-subtext {
    color: #64748b;
    font-size: clamp(13px, 2vw, 15px);
    margin: 0;
}

/* 4 Feature Exploration Cards with Overlapping Icons */
.jt-explore-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
}
.jt-explore-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 32px -8px rgba(4,120,87,0.15);
    border-color: #a7f3d0;
}
.jt-explore-img-wrap {
    height: 160px;
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
    transform: scale(1.06);
}
.jt-explore-body {
    padding: 18px;
    position: relative;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.jt-floating-icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    font-size: 19px;
    color: #ffffff;
    margin-top: -42px;
    margin-bottom: 12px;
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
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
}
.jt-explore-body p {
    font-size: 13px;
    color: #64748b;
    line-height: 1.55;
    margin-bottom: 16px;
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
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    height: 100%;
}
.jt-popular-card:hover {
    transform: translateY(-5px);
    border-color: #10b981;
    box-shadow: 0 16px 32px -8px rgba(16,185,129,0.18);
}
.jt-pop-img-wrap {
    height: 180px;
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
    transform: scale(1.06);
}
.jt-pop-rating-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    border-radius: 99px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}
.jt-pop-category-pill {
    position: absolute;
    bottom: 12px;
    left: 12px;
    background: #047857;
    color: #ffffff;
    border-radius: 99px;
    padding: 3px 12px;
    font-size: 11px;
    font-weight: 700;
}
.jt-pop-body {
    padding: 16px;
}
.jt-pop-body h3 {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
}
.jt-pop-location {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 8px;
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
    border-radius: 20px;
    padding: 24px;
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
    font-size: 19px;
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
    height: 70px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 6px;
}
.jt-ai-thumb-item h5 {
    font-size: 12px;
    font-weight: 800;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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
    gap: 10px;
    margin-bottom: 20px;
    flex: 1;
}
.jt-event-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    padding: 10px;
    border-radius: 12px;
    border: 1px solid #fef3c7;
}
.jt-event-item img {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
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
    gap: 10px;
    text-align: center;
    margin-bottom: 20px;
    flex: 1;
    align-items: center;
}
.jt-kuliner-circle-item img {
    width: clamp(55px, 12vw, 75px);
    height: clamp(55px, 12vw, 75px);
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 6px;
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
    padding: 10px 18px;
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

/* Category Filter Tabs Bar - Horizontal Scroll on Mobile */
.jt-mitra-tabs-container {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 4px;
    max-width: 100%;
}
.jt-mitra-tabs-container::-webkit-scrollbar {
    display: none;
}
.jt-mitra-tab-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-weight: 600;
    font-size: 12px;
    padding: 6px 14px;
    border-radius: 99px;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
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
    padding: clamp(24px, 4vw, 36px);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-top: 50px;
}
@media (max-width: 991px) {
    .jt-newsletter-banner-footer {
        flex-direction: column;
        text-align: center;
    }
    .jt-newsletter-banner-footer .input-group {
        width: 100%;
        max-width: 480px;
        margin: 0 auto;
    }
}
@media (max-width: 576px) {
    .jt-newsletter-banner-footer .input-group {
        flex-direction: column;
        gap: 8px;
    }
    .jt-newsletter-banner-footer input {
        border-radius: 99px !important;
        text-align: center;
    }
    .jt-newsletter-banner-footer button {
        border-radius: 99px !important;
        width: 100%;
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

        <!-- Dua Tombol Utama: Rekomendasi AI & Pencarian -->
        <div class="jt-hero-actions-wrap mb-4 pt-2">
            <a href="{{ route('tour-assistant.index') }}" class="btn jt-hero-btn jt-hero-btn-ai">
                <i class="fa-solid fa-wand-magic-sparkles text-warning fs-5"></i>
                <span>Rekomendasi AI</span>
                <span class="jt-badge-tag">Pintar & Otomatis ✨</span>
            </a>
            <a href="{{ route('tourism.index') }}" class="btn jt-hero-btn jt-hero-btn-search">
                <i class="fa-solid fa-magnifying-glass text-success fs-5"></i>
                <span>Pencarian Wisata</span>
            </a>
        </div>

        <!-- Popular Chips -->
        <div class="jt-chips-wrapper">
            <span class="text-white-50 flex-shrink-0"><i class="fa-solid fa-fire text-danger me-1"></i> Sedang Populer</span>
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
            <div class="col-12 col-sm-6 col-lg-3">
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
            <div class="col-12 col-sm-6 col-lg-3">
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
            <div class="col-12 col-sm-6 col-lg-3">
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
            <div class="col-12 col-sm-6 col-lg-3">
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
<!-- 3. Section 2: Destinasi Populer -->
<section class="public-section py-4">
    <div class="container public-container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <h2 class="fs-2 fw-extrabold text-dark m-0">Destinasi Populer</h2>
            <a href="{{ route('tourism.index') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold fs-8">
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
                    <div class="col-12 col-sm-6 col-lg-3">
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
                <div class="col-12 col-sm-6 col-lg-3">
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

                <div class="col-12 col-sm-6 col-lg-3">
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

                <div class="col-12 col-sm-6 col-lg-3">
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

                <div class="col-12 col-sm-6 col-lg-3">
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
            <div class="d-flex align-items-center gap-2 flex-wrap w-100 w-lg-auto justify-content-between justify-content-lg-end">
                <div class="jt-mitra-tabs-container">
                    <a href="{{ route('home') }}" class="jt-mitra-tab-pill active">Semua</a>
                    <a href="{{ route('tourism.index') }}" class="jt-mitra-tab-pill"><i class="fa-solid fa-compass text-emerald me-1"></i> Wisata</a>
                    <a href="{{ route('accommodation.index') }}" class="jt-mitra-tab-pill"><i class="fa-solid fa-hotel text-primary me-1"></i> Penginapan</a>
                    <a href="{{ route('home', ['service' => 'culinary']) }}" class="jt-mitra-tab-pill"><i class="fa-solid fa-utensils text-warning me-1"></i> Kuliner</a>
                    <a href="{{ route('public.mitra.index') }}" class="jt-mitra-tab-pill">Lainnya</a>
                </div>
                <a href="{{ route('public.mitra.index') }}" class="fw-bold text-success text-decoration-none fs-8 d-flex align-items-center gap-1 flex-shrink-0">
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
                    <div class="col-12 col-sm-6 col-lg-3">
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
                <div class="col-12 col-sm-6 col-lg-3">
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
                            <a href="{{ route('public.mitra.index') }}" class="jt-mitra-ref-btn">Lihat Profil Mitra <i class="fa-solid fa-arrow-right fs-8"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
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
                            <a href="{{ route('public.mitra.index') }}" class="jt-mitra-ref-btn">Lihat Profil Mitra <i class="fa-solid fa-arrow-right fs-8"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
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
                            <a href="{{ route('public.mitra.index') }}" class="jt-mitra-ref-btn">Lihat Profil Mitra <i class="fa-solid fa-arrow-right fs-8"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
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
                            <a href="{{ route('public.mitra.index') }}" class="jt-mitra-ref-btn">Lihat Profil Mitra <i class="fa-solid fa-arrow-right fs-8"></i></a>
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
            <div class="col-12 col-md-6 col-lg-4">
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
            <div class="col-12 col-md-6 col-lg-4">
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
            <div class="col-12 col-md-12 col-lg-4">
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

<!-- 4. Section AI Travel Planner Showcase Sesuai Referensi Gambar -->
<section class="public-section py-5 my-2">
    <div class="container public-container">
        <div class="rounded-5 p-4 p-md-5 position-relative overflow-hidden shadow-lg" 
             style="background: radial-gradient(circle at 85% 20%, #064e3b 0%, #062e24 45%, #081613 100%); color: #ffffff;">
            
            <div class="row align-items-center g-5 position-relative" style="z-index: 2;">
                <!-- Left Column: Copywriting & CTA -->
                <div class="col-12 col-lg-6 text-start">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" 
                         style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #34d399; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em;">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> AI Travel Planner
                    </div>
                    <h2 class="fw-bolder text-white mb-3" style="font-size: clamp(28px, 4.5vw, 44px); line-height: 1.18; letter-spacing: -0.02em;">
                        Rancang Perjalanan Impian Anda Secara Instan
                    </h2>
                    <p class="text-white-50 mb-4 fs-6" style="line-height: 1.65; max-width: 520px;">
                        Susun rencana perjalanan personal lengkap dengan rekomendasi tempat wisata terpopuler, kuliner favorit, dan rute hemat waktu yang dikonstruksi secara cerdas oleh AI kami.
                    </p>
                    <div>
                        <a href="{{ route('tour-assistant.index') }}" class="btn btn-lg rounded-pill px-4 py-2.5 fw-bold fs-7 shadow d-inline-flex align-items-center gap-2" 
                           style="background: #0284c7; color: #ffffff;">
                            <span>Rencanakan Sekarang</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Interactive AI Card Mockup (Persis Sesuai Screenshot) -->
                <div class="col-12 col-lg-6">
                    <div class="ai-preview-card-dark">
                        <!-- Header Card -->
                        <div class="ai-card-dark-header">
                            <h4 class="ai-card-dark-title">
                                <strong>Tegal:</strong> <span>Budaya & Kuliner</span>
                            </h4>
                            <span class="ai-card-dark-badge">3 Hari</span>
                        </div>

                        <!-- Connected Vertical Timeline Sesuai Gambar -->
                        <div class="ai-timeline-ring-wrap">
                            <!-- Timeline Item 1 -->
                            <div class="ai-timeline-ring-item">
                                <div class="ai-timeline-ring-dot-dark"></div>
                                <div class="ai-timeline-box-dark">
                                    <div class="ai-timeline-box-time-mint">HARI 1 — 10:00 WIB</div>
                                    <h5 class="ai-timeline-box-title-white">Tur Pemandian Air Panas Guci</h5>
                                    <p class="ai-timeline-box-desc-muted">Eksplorasi mata air belerang alami lereng Gunung Slamet dengan pemandu lokal.</p>
                                </div>
                            </div>

                            <!-- Timeline Item 2 -->
                            <div class="ai-timeline-ring-item">
                                <div class="ai-timeline-ring-dot-dark"></div>
                                <div class="ai-timeline-box-dark">
                                    <div class="ai-timeline-box-time-mint">HARI 1 — 13:00 WIB</div>
                                    <h5 class="ai-timeline-box-title-white">Makan Siang Sate Kambing Wendy's</h5>
                                    <p class="ai-timeline-box-desc-muted">Menikmati sate kambing muda empuk legendaris khas Tegal.</p>
                                </div>
                            </div>

                            <!-- Timeline Item 3 -->
                            <div class="ai-timeline-ring-item">
                                <div class="ai-timeline-ring-dot-dark"></div>
                                <div class="ai-timeline-box-dark">
                                    <div class="ai-timeline-box-time-mint">HARI 1 — 16:30 WIB</div>
                                    <h5 class="ai-timeline-box-title-white">Menikmati Sunset Pantai Alam Indah</h5>
                                    <p class="ai-timeline-box-desc-muted">Menyaksikan pemandangan sunset memukau pesisir utara dari anjungan pantai.</p>
                                </div>
                            </div>
                        </div>
                    </div>
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

        <div class="d-flex align-items-center gap-3 flex-wrap flex-lg-nowrap w-100 w-lg-auto justify-content-center justify-content-lg-end">
            <div class="input-group" style="max-width: 420px;">
                <input type="email" class="form-control px-3 py-2 border-0" placeholder="Masukkan email kamu" style="font-size: 13px;">
                <button class="btn btn-emerald px-4 fw-bold" style="background: #059669; color: #ffffff;" type="button">Berlangganan</button>
            </div>
            <div class="d-flex gap-3 text-white-50 fs-5">
                <a href="#" class="text-white-50"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="text-white-50"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="text-white-50"><i class="fa-brands fa-youtube"></i></a>
                <a href="#" class="text-white-50"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>
    </div>
</div>

@endsection
