@extends('layouts.public')

@section('title', 'Jelajah Tegal — Eksplorasi Wisata, Penginapan & Potensi Lokal')
@section('meta-description', 'Temukan destinasi wisata terbaik, penginapan nyaman, kuliner khas, dan event seru di Kabupaten & Kota Tegal dalam satu platform terpadu Jelajah Tegal.')
@if(request()->hasAny(['q','region','service'])) @section('robots','noindex,follow') @endif

@push('head-extra')
<link rel="preload" as="image" href="{{ asset('images/guci_hero.webp') }}" fetchpriority="high">
@endpush

@section('content')
<style>
/* Modern Responsive Hero with Multi-Image Slideshow Background */
.jt-hero-mockup {
    position: relative;
    color: #ffffff;
    padding: clamp(52px, 9vw, 105px) 0 clamp(68px, 10vw, 125px);
    overflow: hidden;
    min-height: 520px;
    background: #061e14;
}
.jt-hero-slider {
    position: absolute;
    inset: 0;
    z-index: 1;
    overflow: hidden;
}
.jt-hero-slide {
    position: absolute;
    inset: -15px;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transform: scale(1.08);
    transition: opacity 1.4s ease-in-out, transform 6.5s ease-out;
    pointer-events: none;
}
/* First slide uses <img> for LCP discoverability */
.jt-hero-slide-img {
    position: absolute;
    inset: -15px;
    width: calc(100% + 30px);
    height: calc(100% + 30px);
    object-fit: cover;
    object-position: center;
}
.jt-hero-slide.active {
    opacity: 1;
    transform: scale(1);
    z-index: 2;
}
.jt-hero-overlay {
    position: absolute;
    inset: 0;
    z-index: 3;
    background: linear-gradient(180deg, rgba(6, 28, 18, 0.74) 0%, rgba(6, 38, 25, 0.86) 55%, rgba(3, 18, 12, 0.95) 100%);
    pointer-events: none;
}
.jt-hero-content {
    position: relative;
    z-index: 5;
}
/* Slide Indicators */
.jt-hero-slide-nav {
    position: absolute;
    bottom: 24px;
    right: clamp(16px, 4vw, 48px);
    z-index: 6;
    display: flex;
    align-items: center;
    background: rgba(0, 0, 0, 0.42);
    padding: 8px 14px;
    border-radius: 99px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
}
.jt-slide-dots-wrap {
    display: flex;
    align-items: center;
    gap: 7px;
}
.jt-slide-dot {
    width: 18px;
    height: 4px;
    border-radius: 99px;
    background: rgba(255, 255, 255, 0.3);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.jt-slide-dot.active {
    width: 36px;
    background: #34d399;
    box-shadow: 0 0 10px rgba(52, 211, 153, 0.8);
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
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: clamp(32px, 6.2vw, 56px);
    font-weight: 900;
    line-height: 1.14;
    letter-spacing: -0.035em;
    color: #ffffff;
    margin-bottom: 18px;
    text-align: left;
    text-shadow: 0 4px 24px rgba(0, 0, 0, 0.45);
}
.jt-hero-title-large span {
    font-family: 'Kaushan Script', cursive, sans-serif;
    font-size: 1.18em;
    font-weight: 400;
    letter-spacing: 0.015em;
    line-height: 1.2;
    background: linear-gradient(135deg, #34d399 0%, #6ee7b7 50%, #a7f3d0 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    filter: drop-shadow(0 4px 18px rgba(52, 211, 153, 0.45));
    display: inline-block;
    padding-right: 28px;
    margin-right: -28px;
    padding-left: 0;
    margin-left: -5px;
    overflow: visible;
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

/* Section Header Styling */
.jt-section-title-wrap {
    text-align: center;
    margin-bottom: 36px;
}
.jt-section-heading-primary {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
    font-weight: 800;
    font-size: clamp(24px, 3.8vw, 32px);
    color: #0f172a;
    letter-spacing: -0.03em;
    line-height: 1.25;
    margin-bottom: 6px;
}
.jt-eyebrow-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 99px;
    background: rgba(4, 120, 87, 0.08);
    color: #047857;
    border: 1px solid rgba(4, 120, 87, 0.2);
    font-weight: 800;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}
.jt-section-subtext {
    color: #64748b;
    font-size: clamp(13.5px, 2vw, 15px);
    margin: 0 auto;
    max-width: 600px;
    line-height: 1.55;
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
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
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

<!-- 1. Hero Section & Multi-Filter Search Bar with Multi-Image Slideshow Background -->
<section class="jt-hero-mockup" id="hero-slider-section">
    <!-- Hero Background Slider (Cross-Fade Transitions) -->
    <div class="jt-hero-slider">
        <!-- Slide 1: Real <img> for LCP discoverability -->
        <div class="jt-hero-slide active" data-label="Pemandian Air Panas Guci">
            <img
                src="{{ asset('images/guci_hero.webp') }}"
                alt="Pemandian Air Panas Guci Tegal"
                class="jt-hero-slide-img"
                width="1920" height="1080"
                fetchpriority="high"
                decoding="sync"
            >
        </div>
        <div class="jt-hero-slide" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=85&w=1920&auto=format&fit=crop');" data-label="Pantai Alam Indah Tegal"></div>
        <div class="jt-hero-slide" style="background-image: url('https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?q=85&w=1920&auto=format&fit=crop');" data-label="Pegunungan & Lembah Guci"></div>
        <div class="jt-hero-slide" style="background-image: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=85&w=1920&auto=format&fit=crop');" data-label="Danau & Waduk Cacaban"></div>
    </div>
    <div class="jt-hero-overlay"></div>

    <!-- Slide Indicators Only (Click Page / Dots to Change) -->
    <div class="jt-hero-slide-nav" title="Klik area banner untuk ganti foto">
        <div class="jt-slide-dots-wrap">
            <span class="jt-slide-dot active" onclick="goToHeroSlide(0)" title="Wisata Alam Guci"></span>
            <span class="jt-slide-dot" onclick="goToHeroSlide(1)" title="Pantai Alam Indah"></span>
            <span class="jt-slide-dot" onclick="goToHeroSlide(2)" title="Lembah Gunung Slamet"></span>
            <span class="jt-slide-dot" onclick="goToHeroSlide(3)" title="Waduk Cacaban"></span>
        </div>
    </div>

    <div class="container public-container text-start jt-hero-content">
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
                <span>Rekomendasi AI</span>
                <span class="jt-badge-tag">Pintar & Otomatis</span>
            </a>
            <a href="{{ route('tourism.index') }}" class="btn jt-hero-btn jt-hero-btn-search">
                <span>Pencarian Wisata</span>
            </a>
        </div>

        <!-- Popular Chips -->
        <div class="jt-chips-wrapper">
            <span class="text-white-50 flex-shrink-0">Sedang Populer</span>
            <a href="{{ route('tourism.show', 'purwahamba-indah') }}" class="jt-chip-item">
                Purwahamba Indah
            </a>
            <a href="{{ route('tourism.index') }}" class="jt-chip-item">
                Guci & Curug
            </a>
            <a href="{{ route('home', ['service' => 'culinary']) }}" class="jt-chip-item">
                Sate Tegal
            </a>
            <a href="{{ route('accommodation.index') }}" class="jt-chip-item">
                Hotel Pilihan
            </a>
            <a href="{{ route('home', ['service' => 'event']) }}" class="jt-chip-item">
                Event Budaya
            </a>
        </div>
    </div>
</section>

<!-- 2. Section 1: Jelajahi Tegal (Wisata, Kuliner, Penginapan, Event) -->
<section class="public-section py-5">
    <div class="container public-container">
        <div class="jt-section-title-wrap">
            <div class="jt-eyebrow-badge">
                <i class="fa-solid fa-compass"></i> Eksplorasi Layanan
            </div>
            <h2 class="jt-section-heading-primary">Jelajahi Keberagaman Tegal</h2>
            <p class="jt-section-subtext">Temukan wisata alam, penginapan nyaman, kuliner legendaris, dan agenda budaya dalam satu platform terpadu.</p>
        </div>

        <div class="row g-4">
            <!-- Card 1: Wisata -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="jt-explore-card">
                    <div class="jt-explore-img-wrap">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=600&auto=format&fit=crop" alt="Wisata Tegal" loading="lazy" decoding="async">
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
                        <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=600&auto=format&fit=crop" alt="Kuliner Tegal" loading="lazy" decoding="async">
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
                        <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=600&auto=format&fit=crop" alt="Penginapan Tegal" loading="lazy" decoding="async">
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
                        <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=600&auto=format&fit=crop" alt="Event Tegal" loading="lazy" decoding="async">
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
            <div>
                <div class="jt-eyebrow-badge mb-1">
                    <i class="fa-solid fa-fire text-danger"></i> Rekomendasi Pilihan
                </div>
                <h2 class="jt-section-heading-primary m-0">Destinasi Wisata Populer</h2>
            </div>
            <a href="{{ route('tourism.index') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold fs-8">
                Lihat Semua Wisata <i class="fa-solid fa-arrow-right ms-1"></i>
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
                                <img src="{{ $coverUrl }}" alt="{{ $tourism->name }}" loading="lazy" decoding="async">
                                <span class="jt-pop-rating-badge"><i class="fa-solid fa-star text-warning me-1"></i>{{ $rating }}</span>
                                <span class="jt-pop-category-pill">{{ $tourism->category?->name ?? 'Wisata' }}</span>
                            </div>
                            <div class="jt-pop-body">
                                <h3>
                                    <a href="{{ route('tourism.show', $tourism->slug) }}" class="text-decoration-none text-dark stretched-link">
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
                <div class="col-12 text-center py-5">
                    <div class="p-4 rounded-4 bg-light text-muted border-0 shadow-sm mx-auto" style="max-width: 500px;">
                        <i class="fa-solid fa-mountain-sun fs-2 mb-2 d-block text-secondary opacity-50"></i>
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Destinasi</h6>
                        <p class="small text-muted mb-0">Belum ada destinasi wisata yang terdaftar saat ini.</p>
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
                <div class="jt-eyebrow-badge mb-1">
                    <i class="fa-solid fa-store"></i> Partner Terverifikasi
                </div>
                <h2 class="jt-section-heading-primary m-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-handshake text-success me-1"></i> Mitra Usaha Terpopuler
                </h2>
                <p class="text-muted mb-0 fs-7" style="font-weight: 500;">Partner resmi dan terpercaya pilihan wisatawan Jelajah Tegal</p>
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
                                <img src="{{ $coverUrl }}" alt="{{ $mitra->display_name }}" loading="lazy" decoding="async">
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
                <div class="col-12 text-center py-5">
                    <div class="p-4 rounded-4 bg-white text-muted border-0 shadow-sm mx-auto" style="max-width: 500px;">
                        <i class="fa-solid fa-handshake fs-2 mb-2 d-block text-secondary opacity-50"></i>
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Mitra</h6>
                        <p class="small text-muted mb-0">Belum ada data mitra terverifikasi yang terdaftar saat ini.</p>
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
                            <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=350&auto=format&fit=crop" alt="Bukit Bintang" loading="lazy" decoding="async">
                            <h5>Bukit Bintang</h5>
                            <span>Bumijawa</span>
                        </a>
                        <a href="{{ route('tourism.index') }}" class="jt-ai-thumb-item">
                            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=350&auto=format&fit=crop" alt="Waduk Cacaban" loading="lazy" decoding="async">
                            <h5>Waduk Cacaban</h5>
                            <span>Lebaksiu</span>
                        </a>
                        <a href="{{ route('tourism.index') }}" class="jt-ai-thumb-item">
                            <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=350&auto=format&fit=crop" alt="Pagaralang" loading="lazy" decoding="async">
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
                            <img src="https://images.unsplash.com/photo-1533105079780-92b9be482077?q=80&w=350&auto=format&fit=crop" alt="Karnaval Budaya Tegal" loading="lazy" decoding="async">
                            <div>
                                <h5>Karnaval Budaya Tegal</h5>
                                <p>18 Agustus 2026</p>
                                <span>Alun-alun Kabupaten Tegal</span>
                            </div>
                        </div>

                        <div class="jt-event-item">
                            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=350&auto=format&fit=crop" alt="Festival Kuliner Tegal" loading="lazy" decoding="async">
                            <div>
                                <h5>Festival Kuliner Tegal</h5>
                                <p>25–27 Agustus 2026</p>
                                <span>Taman Rakyat Slawi</span>
                            </div>
                        </div>

                        <div class="jt-event-item">
                            <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=350&auto=format&fit=crop" alt="Larung Sesaji Pantai" loading="lazy" decoding="async">
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
                            <img src="https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?q=80&w=300&auto=format&fit=crop" alt="Sate Kambing" loading="lazy" decoding="async">
                            <h5>Sate Kambing</h5>
                        </div>
                        <div class="jt-kuliner-circle-item">
                            <img src="https://images.unsplash.com/photo-1541544741938-0af808871cc0?q=80&w=300&auto=format&fit=crop" alt="Tahu Aci" loading="lazy" decoding="async">
                            <h5>Tahu Aci</h5>
                        </div>
                        <div class="jt-kuliner-circle-item">
                            <img src="https://images.unsplash.com/photo-1576092768241-dec231879fc3?q=80&w=300&auto=format&fit=crop" alt="Teh Poci" loading="lazy" decoding="async">
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

<!-- CTA Gabung Mitra -->
<div class="container public-container mb-5">
    <div class="rounded-4 overflow-hidden position-relative p-4 p-md-5" style="background: linear-gradient(135deg, #047857 0%, #065f46 100%);">
        <div class="row align-items-center position-relative z-1">
            <div class="col-lg-8 mb-4 mb-lg-0 text-center text-lg-start">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3" style="font-size: 13px;">Tingkatkan Omset Bisnismu</span>
                <h2 class="text-white fw-extrabold mb-3">Punya Usaha Wisata, Kuliner, atau Penginapan di Tegal?</h2>
                <p class="text-white-50 fs-6 mb-0" style="max-width: 600px; line-height: 1.6;">
                    Jangkau lebih banyak wisatawan, kelola pesanan dengan mudah, dan kembangkan bisnismu secara digital dengan menjadi Mitra resmi Jelajah Tegal hari ini.
                </p>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <a href="{{ route('mitra.register') }}" class="btn btn-warning rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 15px;">
                    Gabung Mitra Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

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

<!-- Hero Background Slider Script -->
<script>
    let currentHeroSlide = 0;
    const heroSlides = document.querySelectorAll('.jt-hero-slide');
    const heroDots = document.querySelectorAll('.jt-slide-dot');
    let heroSliderTimer = null;

    function showHeroSlide(index) {
        if (!heroSlides.length) return;
        if (index >= heroSlides.length) currentHeroSlide = 0;
        else if (index < 0) currentHeroSlide = heroSlides.length - 1;
        else currentHeroSlide = index;

        heroSlides.forEach((slide, idx) => {
            slide.classList.toggle('active', idx === currentHeroSlide);
        });
        heroDots.forEach((dot, idx) => {
            dot.classList.toggle('active', idx === currentHeroSlide);
        });
    }

    function nextHeroSlide() {
        showHeroSlide(currentHeroSlide + 1);
        resetHeroTimer();
    }

    function prevHeroSlide() {
        showHeroSlide(currentHeroSlide - 1);
        resetHeroTimer();
    }

    function goToHeroSlide(index) {
        showHeroSlide(index);
        resetHeroTimer();
    }

    function resetHeroTimer() {
        if (heroSliderTimer) clearInterval(heroSliderTimer);
        heroSliderTimer = setInterval(() => {
            showHeroSlide(currentHeroSlide + 1);
        }, 5500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        resetHeroTimer();

        // Mechanism: Click on the hero banner background to advance to the next slide
        const heroSection = document.getElementById('hero-slider-section');
        if (heroSection) {
            heroSection.addEventListener('click', (e) => {
                // If click originated on an interactive element (buttons, links, chips, inputs, or dots), do not trigger slide switch
                if (e.target.closest('a, button, input, select, textarea, .jt-slide-dot, .jt-chips-wrapper, .jt-hero-actions-wrap, .jt-badge-tag')) {
                    return;
                }
                nextHeroSlide();
            });
        }
    });
</script>

@endsection
