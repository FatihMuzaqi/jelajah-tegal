@extends('layouts.public')

@section('title', $mitra->display_name . ' — Profil Mitra Resmi Jelajah Tegal')
@section('meta-description', str($mitra->description ?: 'Lihat seluruh layanan wisata, penginapan, kuliner, dan event yang disediakan oleh ' . $mitra->display_name . ' di Jelajah Tegal.')->limit(155))
@section('canonical', route('public.mitra.show', $mitra->slug))

@section('content')
<style>
/* Mitra Profile Header */
.mitra-hero-section {
    position: relative;
    background: linear-gradient(135deg, #092018 0%, #134032 55%, #1b634b 100%);
    color: #ffffff;
    padding: 60px 0 75px;
    overflow: hidden;
}
.mitra-hero-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(242,169,59,0.15) 0%, transparent 60%);
}
.mitra-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.75);
    margin-bottom: 24px;
    position: relative;
    z-index: 2;
}
.mitra-breadcrumbs a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: color 0.2s;
}
.mitra-breadcrumbs a:hover {
    color: #f2a93b;
}
.mitra-avatar-box {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: linear-gradient(135deg, #f2a93b, #d97706);
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 36px;
    font-weight: 900;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    border: 3px solid rgba(255,255,255,0.25);
    flex-shrink: 0;
}
.mitra-header-title {
    font-size: 36px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 8px;
    letter-spacing: -0.02em;
}

/* Stats Bar */
.mitra-stats-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 22px;
    margin-top: -40px;
    position: relative;
    z-index: 10;
    box-shadow: 0 15px 35px rgba(17,26,24,0.08);
}
.mitra-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 16px;
    text-align: center;
}
.mitra-stat-item h4 {
    font-size: 26px;
    font-weight: 900;
    color: var(--lokantara-primary);
    margin: 0 0 2px;
}
.mitra-stat-item p {
    font-size: 12px;
    color: var(--lokantara-muted);
    font-weight: 600;
    margin: 0;
    text-transform: uppercase;
}

/* Cards & Section */
.mitra-content-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(17,26,24,0.03);
}
.mitra-content-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--lokantara-text);
    margin: 0 0 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Catalog Card */
.catalog-item-card {
    background: var(--lokantara-background);
    border: 1px solid var(--lokantara-border);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.25s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.catalog-item-card:hover {
    transform: translateY(-4px);
    border-color: var(--lokantara-primary);
    box-shadow: 0 12px 28px rgba(31,122,92,0.12);
}
.catalog-img-wrap {
    height: 170px;
    position: relative;
    background: #cbd5e1;
    overflow: hidden;
}
.catalog-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.catalog-item-card:hover .catalog-img-wrap img {
    transform: scale(1.06);
}
.catalog-body {
    padding: 18px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.catalog-body h4 {
    font-size: 16px;
    font-weight: 800;
    margin: 0 0 6px;
    color: var(--lokantara-text);
}
.catalog-body p {
    font-size: 12px;
    color: var(--lokantara-muted);
    line-height: 1.5;
    margin-bottom: 14px;
    flex: 1;
}
.catalog-footer {
    padding-top: 12px;
    border-top: 1px solid var(--lokantara-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
</style>

<!-- Hero Section -->
<section class="mitra-hero-section">
    <div class="mitra-hero-overlay"></div>
    <div class="container public-container position-relative" style="z-index: 2;">
        <!-- Breadcrumbs -->
        <nav class="mitra-breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('home') }}">Mitra</a>
            <span>/</span>
            <span class="text-white fw-semibold">{{ $mitra->display_name }}</span>
        </nav>

        <div class="d-flex flex-wrap align-items-center gap-4">
            <div class="mitra-avatar-box">
                {{ str($mitra->display_name)->substr(0, 1)->upper() }}
            </div>

            <div style="flex: 1; min-width: 280px;">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-success text-white px-3 py-1" style="border-radius: 99px; font-size: 11px;">
                         Mitra Terverifikasi Resmi
                    </span>
                    @if ($mitra->region)
                        <span class="badge" style="background: rgba(45,140,168,0.3); color: #90cdf4; border: 1px solid rgba(45,140,168,0.4); border-radius: 99px; font-size: 11px;">
                            <i class="fa-solid fa-location-dot text-danger"></i> {{ $mitra->region->name }}
                        </span>
                    @endif
                </div>

                <h1 class="mitra-header-title">{{ $mitra->display_name }}</h1>

                <p class="mb-0 text-white-50" style="font-size: 14px; max-width: 700px;">
                    {{ $mitra->description ?: 'Pelaku usaha pariwisata dan ekonomi kreatif terverifikasi di wilayah Tegal.' }}
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<div class="container public-container">
    <div class="mitra-stats-card">
        <div class="mitra-stats-grid">
            <div class="mitra-stat-item">
                <h4>{{ $tourisms->count() }}</h4>
                <p>Destinasi Wisata</p>
            </div>
            <div class="mitra-stat-item">
                <h4>{{ $accommodations->count() }}</h4>
                <p>Penginapan & Hotel</p>
            </div>
            <div class="mitra-stat-item">
                <h4>{{ $culinaries->count() }}</h4>
                <p>Tempat Kuliner</p>
            </div>
            <div class="mitra-stat-item">
                <h4>{{ $events->count() }}</h4>
                <p>Event Budaya</p>
            </div>
            <div class="mitra-stat-item">
                <h4>{{ $rentals->count() }}</h4>
                <p>Armada Rental</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Showcase Section -->
<section class="public-section pt-4">
    <div class="container public-container">
        <div class="row g-4">
            <!-- Left Main Column: Catalog Listings (8 Cols) -->
            <div class="col-lg-8">
                <!-- 1. Destinasi Wisata Milik Mitra -->
                @if ($tourisms->isNotEmpty())
                    <div class="mitra-content-card">
                        <h2 class="mitra-content-title">
                            <span>️</span> Destinasi Wisata yang Dikelola ({{ $tourisms->count() }})
                        </h2>

                        <div class="row g-3">
                            @foreach ($tourisms as $item)
                                @php
                                    $cover = $item->media->where('pivot.role', 'cover')->first() ?? $item->media->first();
                                    $coverUrl = $cover ? asset('storage/' . $cover->object_key) : null;
                                    $minPrice = $item->offers->min('price');
                                @endphp
                                <div class="col-md-6">
                                    <div class="catalog-item-card">
                                        <div class="catalog-img-wrap">
                                            @if ($coverUrl)
                                                <img src="{{ $coverUrl }}" alt="{{ $item->name }}">
                                            @else
                                                <div style="width: 100%; height: 100%; display: grid; place-items: center; background: #174d3c; color: #fff; font-size: 28px;">
                                                    ️
                                                </div>
                                            @endif
                                        </div>
                                        <div class="catalog-body">
                                            <small class="text-muted mb-1" style="font-size: 11px;">{{ $item->category?->name ?? 'Wisata' }}</small>
                                            <h4>{{ $item->name }}</h4>
                                            <p>{{ str($item->description ?: 'Destinasi wisata unggulan di Tegal.')->limit(80) }}</p>

                                            <div class="catalog-footer">
                                                <span class="fw-bold" style="color: var(--lokantara-primary); font-size: 14px;">
                                                    {{ $minPrice ? 'Rp ' . number_format($minPrice, 0, ',', '.') : 'Tiket Masuk' }}
                                                </span>
                                                <a href="{{ route('tourism.show', $item->slug) }}" class="btn btn-sm btn-lokantara fw-bold px-3">
                                                    Lihat Tiket
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 2. Penginapan Milik Mitra -->
                @if ($accommodations->isNotEmpty())
                    <div class="mitra-content-card">
                        <h2 class="mitra-content-title">
                            <span><i class="fa-solid fa-hotel text-info"></i></span> Hotel & Penginapan yang Dikelola ({{ $accommodations->count() }})
                        </h2>

                        <div class="row g-3">
                            @foreach ($accommodations as $item)
                                @php
                                    $cover = $item->media->where('pivot.role', 'cover')->first() ?? $item->media->first();
                                    $coverUrl = $cover ? asset('storage/' . $cover->object_key) : null;
                                    $minPrice = $item->accommodation?->rooms?->min('offer.price') ?? 0;
                                @endphp
                                <div class="col-md-6">
                                    <div class="catalog-item-card">
                                        <div class="catalog-img-wrap">
                                            @if ($coverUrl)
                                                <img src="{{ $coverUrl }}" alt="{{ $item->name }}">
                                            @else
                                                <div style="width: 100%; height: 100%; display: grid; place-items: center; background: #1b634b; color: #fff; font-size: 28px;">
                                                    <i class="fa-solid fa-hotel text-secondary"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="catalog-body">
                                            <small class="text-muted mb-1" style="font-size: 11px;">{{ str($item->accommodation?->property_type ?? 'Hotel')->headline() }}</small>
                                            <h4>{{ $item->name }}</h4>
                                            <p>{{ str($item->description ?: 'Akomodasi nyaman dan bersih di Tegal.')->limit(80) }}</p>

                                            <div class="catalog-footer">
                                                <span class="fw-bold" style="color: var(--lokantara-primary); font-size: 14px;">
                                                    {{ $minPrice ? 'Rp ' . number_format($minPrice, 0, ',', '.') . '/mlm' : 'Hubungi Mitra' }}
                                                </span>
                                                <a href="{{ route('accommodation.show', $item->slug) }}" class="btn btn-sm btn-lokantara fw-bold px-3">
                                                    Pesan Kamar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 3. Kuliner Milik Mitra -->
                @if ($culinaries->isNotEmpty())
                    <div class="mitra-content-card">
                        <h2 class="mitra-content-title">
                            <span></span> Tempat Kuliner & Restoran ({{ $culinaries->count() }})
                        </h2>

                        <div class="row g-3">
                            @foreach ($culinaries as $item)
                                <div class="col-md-6">
                                    <div class="catalog-item-card">
                                        <div class="catalog-body">
                                            <h4>{{ $item->name }}</h4>
                                            <p>{{ str($item->description ?: 'Sentra kuliner lezat khas Tegal.')->limit(80) }}</p>
                                            <div class="catalog-footer">
                                                <span class="text-muted" style="font-size: 12px;">Kuliner Khas</span>
                                                <a href="{{ route('culinary.show', $item->slug) }}" class="btn btn-sm btn-lokantara fw-bold px-3">
                                                    Lihat Menu
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 4. Event & Rental jika ada -->
                @if ($events->isNotEmpty())
                    <div class="mitra-content-card">
                        <h2 class="mitra-content-title"><span></span> Event & Acara</h2>
                        <div class="row g-3">
                            @foreach ($events as $item)
                                <div class="col-md-6">
                                    <div class="catalog-item-card">
                                        <div class="catalog-body">
                                            <h4>{{ $item->name }}</h4>
                                            <p>{{ str($item->description)->limit(80) }}</p>
                                            <div class="catalog-footer">
                                                <a href="{{ route('event.show', $item->slug) }}" class="btn btn-sm btn-lokantara fw-bold px-3 ms-auto">
                                                    Detail Event
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($totalCatalogs === 0)
                    <div class="mitra-content-card text-center py-5">
                        <span class="fs-1 mb-2 d-block"></span>
                        <h3 class="fs-5 fw-bold">Katalog Layanan Sedang Dipersiapkan</h3>
                        <p class="text-muted mb-0" style="font-size: 14px;">Mitra {{ $mitra->display_name }} sedang melengkapi katalog produk dan layanan untuk Anda.</p>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar Column: Profil & Kontak (4 Cols) -->
            <div class="col-lg-4">
                <div class="mitra-content-card" style="position: sticky; top: 90px;">
                    <h3 class="fs-6 fw-bold mb-3">Informasi Resmi Mitra</h3>

                    <div class="p-3 rounded-3 mb-3" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;">
                        <div class="py-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Nama Legal Usaha</span>
                            <strong>{{ $mitra->legal_name }}</strong>
                        </div>
                        <div class="py-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Wilayah Operasional</span>
                            <strong>{{ $mitra->region?->name ?? 'Kabupaten / Kota Tegal' }}</strong>
                        </div>
                        <div class="py-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Status Legalitas</span>
                            <span class="text-success fw-bold"> Terverifikasi Resmi</span>
                        </div>
                        <div class="py-2">
                            <span class="text-muted d-block" style="font-size: 11px; text-transform: uppercase;">Bergabung Sejak</span>
                            <strong>{{ $mitra->approved_at?->translatedFormat('F Y') ?? '2026' }}</strong>
                        </div>
                    </div>

                    @if ($mitra->address)
                        <div class="mb-3">
                            <strong class="fs-7 d-block mb-1"><i class="fa-solid fa-location-dot text-danger"></i> Alamat Kantor / Lokasi:</strong>
                            <p class="text-muted mb-0" style="font-size: 13px;">{{ $mitra->address }}</p>
                        </div>
                    @endif

                    @if ($mitra->contact_phone || $mitra->contact_email)
                        <hr class="my-3">
                        <strong class="fs-7 d-block mb-2"> Hubungi Mitra:</strong>
                        <div class="d-flex flex-column gap-2">
                            @if ($mitra->contact_phone)
                                <a href="tel:{{ $mitra->contact_phone }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                                    <span></span> {{ $mitra->contact_phone }}
                                </a>
                            @endif
                            @if ($mitra->contact_email)
                                <a href="mailto:{{ $mitra->contact_email }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                                    <span>️</span> {{ $mitra->contact_email }}
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-top text-center">
                        <a href="{{ route('home') }}" class="btn btn-outline-lokantara w-100 fw-bold py-2 fs-7">
                            &larr; Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
