@extends('layouts.public')
@section('title','Destinasi Wisata — Jelajah Tegal')
@section('meta-description','Temukan destinasi wisata terpopuler, keindahan alam, pantai, curug, dan hidden gem terbaik di Kabupaten & Kota Tegal.')
@if(request()->hasAny(['q','category','region','latitude'])) @section('robots','noindex,follow') @endif

@section('content')
<style>
.jt-page-hero {
    background: linear-gradient(180deg, rgba(7, 30, 20, 0.75) 0%, rgba(10, 42, 28, 0.90) 100%), 
                url('{{ asset('images/guci_hero.png') }}') center/cover no-repeat;
    color: #ffffff;
    padding: clamp(45px, 7vw, 75px) 0 clamp(55px, 8vw, 85px);
}
.jt-page-hero h1 {
    font-size: clamp(24px, 5vw, 42px) !important;
}
.jt-filter-card {
    background: #ffffff;
    border-radius: 18px;
    padding: clamp(16px, 3vw, 24px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    margin-top: -35px;
    position: relative;
    z-index: 10;
}
.jt-tourism-card {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.jt-tourism-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(4, 120, 87, 0.15);
    border-color: rgba(4, 120, 87, 0.3);
}
.jt-tourism-img-wrap {
    position: relative;
    height: 210px;
    overflow: hidden;
    background: #0f172a;
}
.jt-tourism-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.jt-tourism-card:hover .jt-tourism-img-wrap img {
    transform: scale(1.06);
}
.jt-rating-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(8px);
    color: #ffffff;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    z-index: 2;
}
.jt-category-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(4, 120, 87, 0.9);
    backdrop-filter: blur(8px);
    color: #ffffff;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    z-index: 2;
}
.jt-tourism-body {
    padding: 22px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.jt-tourism-title {
    font-size: 18px;
    font-weight: 800;
    line-height: 1.3;
    margin-bottom: 8px;
}
.jt-tourism-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s ease;
}
.jt-tourism-title a:hover {
    color: #047857;
}
.jt-tourism-location {
    font-size: 12px;
    font-weight: 600;
    color: #047857;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.jt-tourism-desc {
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 16px;
    flex-grow: 1;
}
.jt-tourism-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px solid #f1f5f9;
}
</style>

<!-- Hero Section -->
<div class="jt-page-hero">
    <div class="container public-container text-start">
        <div class="badge bg-success bg-opacity-25 text-warning border border-success rounded-pill px-3 py-2 fw-bold mb-3">
            <i class="fa-solid fa-compass me-1"></i> Destinasi Wisata Tegal
        </div>
        <h1 class="display-5 fw-extrabold mb-2">Eksplorasi Keindahan Wisata Tegal</h1>
        <p class="lead text-white-50 max-w-2xl">
            Temukan tempat wisata alam, pantai pesisir, air terjun lereng Gunung Slamet, dan hidden gem terbaik dari Mitra terverifikasi.
        </p>
    </div>
</div>

<!-- Filter Bar -->
<div class="container public-container mb-5">
    <div class="jt-filter-card">
        <form class="row g-3 align-items-center" method="GET" action="{{ route('tourism.index') }}">
            <div class="col-lg-4 col-md-6">
                <label for="q" class="form-label fs-8 fw-bold text-uppercase text-muted mb-1">Cari Wisata</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0 fs-7" placeholder="Nama destinasi atau kata kunci...">
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label for="category" class="form-label fs-8 fw-bold text-uppercase text-muted mb-1">Kategori</label>
                <select name="category" id="category" class="form-select bg-light fs-7">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label for="region" class="form-label fs-8 fw-bold text-uppercase text-muted mb-1">Wilayah / Kecamatan</label>
                <select name="region" id="region" class="form-select bg-light fs-7">
                    <option value="">Semua Wilayah</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" @selected(request('region') == $region->id)>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-emerald w-100 fw-bold py-2 rounded-3 text-white" style="background: #047857;">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Main Content Grid -->
<div class="container public-container mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fs-4 fw-extrabold text-dark m-0">
            Destinasi Terdaftar <span class="badge bg-emerald-subtle text-emerald fs-7 rounded-pill px-3 py-1 ms-2" style="background: #e6f4ea; color: #047857;">{{ $items->total() }} Tempat</span>
        </h2>
    </div>

    @if($items->isEmpty())
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
            <i class="fa-solid fa-compass-drafting text-muted display-4 mb-3"></i>
            <h3 class="fs-5 fw-bold text-dark">Destinasi Belum Ditemukan</h3>
            <p class="text-muted fs-7">Coba ubah kata kunci atau filter pencarian Anda.</p>
            <a href="{{ route('tourism.index') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold mt-2">Reset Filter</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($items as $item)
                @php
                    $coverMedia = $item->media->where('pivot.role', 'cover')->first() ?? $item->media->first();
                    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?q=80&w=800&auto=format&fit=crop';
                    $rating = $item->rating_average > 0 ? number_format($item->rating_average, 1) : '4.8';
                    $regionName = $item->region?->name ?? 'Tegal';
                    $mitraName = $item->mitra?->display_name ?? 'Mitra Terverifikasi';

                    $activeOffers = $item->offers->whereIn('status', ['active', 'published']);
                    $isSoldOut = false;
                    $isLowStock = false;
                    if ($activeOffers->isNotEmpty()) {
                        $totalRem = 0;
                        foreach ($activeOffers as $off) {
                            $avail = $off->availabilities->where('service_date', now()->format('Y-m-d'))->first();
                            $cap = $avail?->capacity ?? ($off->ticketPackage?->quota_per_day ?? 100);
                            $res = $avail?->reserved_quantity ?? 0;
                            if (!$avail || $avail->status === 'available') {
                                $totalRem += max(0, $cap - $res);
                            }
                        }
                        if ($totalRem <= 0) {
                            $isSoldOut = true;
                        } elseif ($totalRem <= 10) {
                            $isLowStock = true;
                        }
                    }
                @endphp
                <div class="col-12 col-sm-6 col-lg-4">
                    <article class="jt-tourism-card">
                        <div class="jt-tourism-img-wrap">
                            <img src="{{ $coverUrl }}" alt="{{ $item->name }}">
                            <span class="jt-rating-badge">
                                <i class="fa-solid fa-star text-warning me-1"></i>{{ $rating }}
                            </span>
                            <span class="jt-category-badge">
                                {{ $item->category?->name ?? 'Wisata' }}
                            </span>
                            @if($isSoldOut)
                                <span class="badge bg-danger text-white position-absolute bottom-0 start-0 m-3 px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="z-index: 2; font-size: 11px;">
                                    <i class="fa-solid fa-ban me-1"></i> Tiket Hari Ini Habis
                                </span>
                            @elseif($isLowStock)
                                <span class="badge bg-warning text-dark position-absolute bottom-0 start-0 m-3 px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="z-index: 2; font-size: 11px;">
                                    <i class="fa-solid fa-fire text-danger me-1"></i> Kuota Menipis
                                </span>
                            @endif
                        </div>

                        <div class="jt-tourism-body">
                            <div class="jt-tourism-location">
                                <i class="fa-solid fa-location-dot text-danger"></i> {{ $regionName }}
                            </div>

                            <h2 class="jt-tourism-title">
                                <a href="{{ route('tourism.show', $item->slug) }}">
                                    {{ $item->name }}
                                </a>
                            </h2>

                            <p class="jt-tourism-desc">
                                {{ str($item->description)->limit(110) }}
                            </p>

                            <div class="jt-tourism-footer">
                                <span class="fs-8 text-muted fw-medium">
                                    <i class="fa-solid fa-store me-1 text-emerald" style="color: #047857;"></i> {{ str($mitraName)->limit(25) }}
                                </span>
                                <a href="{{ route('tourism.show', $item->slug) }}" class="btn btn-sm btn-outline-emerald rounded-pill px-3 fw-bold" style="color: #047857; border-color: #047857;">
                                    Detail <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
