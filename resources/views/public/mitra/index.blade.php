@extends('layouts.public')

@section('title', 'Direktori Mitra Resmi — Jelajah Tegal')
@section('meta-description', 'Jelajahi seluruh mitra resmi terverifikasi di Jelajah Tegal, mulai dari pengelola wisata dinas pemda, hotel & resort, kuliner khas, hingga rental & event.')
@if(request()->hasAny(['q','category','region','service'])) @section('robots','noindex,follow') @endif

@section('content')
<style>
.jt-mitra-hero {
    background: linear-gradient(180deg, rgba(7, 30, 20, 0.78) 0%, rgba(10, 42, 28, 0.92) 100%), 
                url('{{ asset('images/guci_hero.png') }}') center/cover no-repeat;
    color: #ffffff;
    padding: clamp(45px, 7vw, 75px) 0 clamp(55px, 8vw, 85px);
}
.jt-mitra-hero h1 {
    font-size: clamp(24px, 5vw, 42px) !important;
}

.jt-filter-card {
    background: #ffffff;
    border-radius: 20px;
    padding: clamp(18px, 3vw, 26px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    margin-top: -35px;
    position: relative;
    z-index: 10;
    border: 1px solid rgba(0,0,0,0.04);
}

.jt-category-pill-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 99px;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s ease;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
}
.jt-category-pill-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
.jt-category-pill-btn.active {
    background: #047857 !important;
    border-color: #047857 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(4, 120, 87, 0.3);
}

.jt-mitra-dir-card {
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
    border: 1px solid rgba(0,0,0,0.06);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
}
.jt-mitra-dir-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(4, 120, 87, 0.14);
    border-color: rgba(4, 120, 87, 0.3);
}

.jt-mitra-dir-cover {
    position: relative;
    height: 170px;
    background: #0f172a;
    overflow: hidden;
}
.jt-mitra-dir-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.jt-mitra-dir-card:hover .jt-mitra-dir-cover img {
    transform: scale(1.05);
}

.jt-mitra-badge-category {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 5px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.02em;
    backdrop-filter: blur(8px);
    z-index: 2;
}
.jt-badge-dinas {
    background: rgba(4, 120, 87, 0.92);
    color: #ffffff;
    border: 1px solid rgba(255,255,255,0.25);
}
.jt-badge-nondinas {
    background: rgba(217, 119, 6, 0.92);
    color: #ffffff;
    border: 1px solid rgba(255,255,255,0.25);
}

.jt-mitra-badge-verified {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(8px);
    color: #38bdf8;
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    z-index: 2;
}

.jt-mitra-dir-body {
    padding: 22px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.jt-mitra-logo-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #047857, #065f46);
    color: #ffffff;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 20px;
    border: 3px solid #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    margin-top: -42px;
    margin-bottom: 12px;
    position: relative;
    z-index: 3;
    overflow: hidden;
    flex-shrink: 0;
}
.jt-mitra-logo-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.jt-mitra-title {
    font-size: 18px;
    font-weight: 800;
    line-height: 1.3;
    margin-bottom: 4px;
    color: #0f172a;
}
.jt-mitra-legal-name {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.jt-feature-tag-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    background: #f1f5f9;
    color: #334155;
    margin-right: 4px;
    margin-bottom: 6px;
}

.jt-mitra-action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 11px 16px;
    border-radius: 12px;
    background: #047857;
    color: #ffffff;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s ease;
    margin-top: auto;
}
.jt-mitra-action-btn:hover {
    background: #065f46;
    color: #ffffff;
    box-shadow: 0 6px 18px rgba(4, 120, 87, 0.35);
}
</style>

<!-- 1. Hero Header -->
<section class="jt-mitra-hero">
    <div class="container public-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Direktori Mitra</li>
            </ol>
        </nav>
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-25 text-emerald-300 fs-8 fw-bold mb-3">
                    <i class="fa-solid fa-handshake"></i> Jaringan Ekosistem Pariwisata
                </div>
                <h1 class="fw-black text-white mb-2">Direktori Mitra Resmi Jelajah Tegal</h1>
                <p class="text-white-50 fs-6 mb-0" style="max-width: 650px;">
                    Temukan pengelola destinasi wisata dinas pemkab/pemkot, penginapan & hotel, sentra kuliner, rental kendaraan, dan promotor event resmi terverifikasi di Tegal.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 2. Search & Filter Bar -->
<section class="container public-container mb-5">
    <div class="jt-filter-card">
        <form action="{{ route('public.mitra.index') }}" method="GET">
            <!-- Filter Kategori Tabs -->
            <div class="d-flex align-items-center gap-2 flex-wrap pb-3 mb-3 border-bottom">
                <a href="{{ route('public.mitra.index', array_merge(request()->except('category', 'page'), [])) }}" 
                   class="jt-category-pill-btn @if(!request('category')) active @endif">
                    <i class="fa-solid fa-layer-group"></i> Semua Mitra 
                    <span class="badge bg-white text-dark rounded-pill ms-1">{{ $totalAll }}</span>
                </a>
                <a href="{{ route('public.mitra.index', array_merge(request()->except('category', 'page'), ['category' => 'dinas'])) }}" 
                   class="jt-category-pill-btn @if(request('category') === 'dinas') active @endif">
                    <i class="fa-solid fa-landmark"></i> Dinas Pemda 
                    <span class="badge bg-white text-dark rounded-pill ms-1">{{ $totalDinas }}</span>
                </a>
                <a href="{{ route('public.mitra.index', array_merge(request()->except('category', 'page'), ['category' => 'non_dinas'])) }}" 
                   class="jt-category-pill-btn @if(request('category') === 'non_dinas') active @endif">
                    <i class="fa-solid fa-building"></i> Swasta / Usaha Lokal 
                    <span class="badge bg-white text-dark rounded-pill ms-1">{{ $totalNonDinas }}</span>
                </a>
            </div>

            <!-- Inputs Filter Grid -->
            <div class="row g-3">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <!-- Pencarian Nama / Keyword -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama mitra, pengelola, alamat...">
                    </div>
                </div>

                <!-- Filter Wilayah -->
                <div class="col-12 col-sm-6 col-md-3">
                    <select name="region" class="form-select bg-light">
                        <option value="">Semua Wilayah / Daerah</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg->id }}" @selected(request('region') == $reg->id)>{{ $reg->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Sektor Layanan -->
                <div class="col-12 col-sm-6 col-md-3">
                    <select name="service" class="form-select bg-light">
                        <option value="">Semua Layanan</option>
                        @foreach($serviceTypes as $st)
                            <option value="{{ $st->code }}" @selected(request('service') == $st->code)>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Filter & Reset -->
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-success fw-bold w-100" style="background:#047857; border-color:#047857;">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'category', 'region', 'service', 'sort']))
                        <a href="{{ route('public.mitra.index') }}" class="btn btn-outline-secondary px-3" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</section>

<!-- 3. Mitra Grid Listing -->
<section class="container public-container mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fs-4 fw-extrabold text-dark m-0">Daftar Mitra Terverifikasi</h2>
            <p class="text-muted fs-7 m-0">Menampilkan <strong>{{ $mitras->total() }}</strong> entitas mitra resmi dalam jaringan Jelajah Tegal.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="fs-8 text-muted fw-bold text-nowrap">Urutkan:</span>
            <form action="{{ route('public.mitra.index') }}" method="GET" class="d-inline">
                @foreach(request()->except('sort', 'page') as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <select name="sort" class="form-select form-select-sm border-0 bg-light fw-bold" onchange="this.form.submit()">
                    <option value="latest" @selected(request('sort') == 'latest')>Terbaru</option>
                    <option value="name_asc" @selected(request('sort') == 'name_asc')>Nama A - Z</option>
                    <option value="name_desc" @selected(request('sort') == 'name_desc')>Nama Z - A</option>
                </select>
            </form>
        </div>
    </div>

    @if($mitras->isEmpty())
        <div class="text-center py-5 bg-light rounded-4 border">
            <div class="mb-3 text-muted" style="font-size: 48px;"><i class="fa-regular fa-folder-open"></i></div>
            <h3 class="fs-5 fw-bold text-dark mb-2">Tidak Ada Mitra yang Sesuai</h3>
            <p class="text-muted fs-7 mb-4">Coba sesuaikan kata kunci pencarian atau ubah filter kategori/wilayah.</p>
            <a href="{{ route('public.mitra.index') }}" class="btn btn-success px-4 fw-bold" style="background:#047857;">Lihat Semua Mitra</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($mitras as $mitra)
                @php
                    $coverUrl = $mitra->bannerMedia ? asset('storage/' . $mitra->bannerMedia->object_key) : 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=800&auto=format&fit=crop';
                    $logoUrl = $mitra->logoMedia ? asset('storage/' . $mitra->logoMedia->object_key) : null;
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="jt-mitra-dir-card">
                        <!-- Cover Image & Badges -->
                        <div class="jt-mitra-dir-cover">
                            <img src="{{ $coverUrl }}" alt="{{ $mitra->display_name }}">
                            
                            @if($mitra->isDinas())
                                <div class="jt-mitra-badge-category jt-badge-dinas">
                                    <i class="fa-solid fa-landmark me-1"></i> Dinas Pemda
                                </div>
                            @else
                                <div class="jt-mitra-badge-category jt-badge-nondinas">
                                    <i class="fa-solid fa-building me-1"></i> Usaha Swasta
                                </div>
                            @endif

                            <div class="jt-mitra-badge-verified">
                                <i class="fa-solid fa-circle-check"></i> Terverifikasi
                            </div>
                        </div>

                        <!-- Body Card -->
                        <div class="jt-mitra-dir-body">
                            <!-- Logo Avatar -->
                            <div class="jt-mitra-logo-wrap" style="@if($mitra->isDinas()) background: linear-gradient(135deg, #047857, #064e3b); @else background: linear-gradient(135deg, #d97706, #b45309); @endif">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $mitra->display_name }}">
                                @else
                                    {{ str($mitra->display_name)->substr(0, 2)->upper() }}
                                @endif
                            </div>

                            <!-- Name & Legal -->
                            <h3 class="jt-mitra-title">{{ $mitra->display_name }}</h3>
                            <div class="jt-mitra-legal-name">{{ $mitra->legal_name }}</div>

                            <!-- Location & Stats -->
                            <div class="d-flex align-items-center gap-2 text-muted fs-8 mb-3">
                                <i class="fa-solid fa-location-dot text-danger"></i>
                                <span>{{ $mitra->region?->name ?? 'Kabupaten / Kota Tegal' }}</span>
                            </div>

                            <!-- Services / Features Active -->
                            <div class="mb-3">
                                <div class="fs-9 text-muted text-uppercase fw-bold mb-1">Layanan Aktif:</div>
                                <div>
                                    @forelse($mitra->features as $f)
                                        @php
                                            $stName = $f->serviceType?->name ?? 'Layanan';
                                            $icon = match(strtolower($stName)) {
                                                'wisata' => 'fa-compass text-emerald',
                                                'penginapan' => 'fa-hotel text-primary',
                                                'kuliner' => 'fa-utensils text-warning',
                                                'rental' => 'fa-car text-info',
                                                'event' => 'fa-calendar-days text-danger',
                                                default => 'fa-store text-secondary',
                                            };
                                        @endphp
                                        <span class="jt-feature-tag-chip">
                                            <i class="fa-solid {{ $icon }}"></i> {{ $stName }}
                                        </span>
                                    @empty
                                        <span class="text-muted fs-8 fst-italic">Seluruh Sektor Terintegrasi</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('public.mitra.show', $mitra->slug) }}" class="jt-mitra-action-btn">
                                <span>Lihat Profil & Layanan</span>
                                <i class="fa-solid fa-arrow-right fs-8"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $mitras->links() }}
        </div>
    @endif
</section>
@endsection
