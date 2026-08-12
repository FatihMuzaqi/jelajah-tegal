@extends('layouts.public')
@section('title','Penginapan & Hotel — Jelajah Tegal')
@section('meta-description','Cari hotel, homestay, villa, dan penginapan nyaman di Tegal dengan penawaran harga terbaik.')
@if(request()->query()) @section('robots','noindex,follow') @endif

@section('content')
<style>
.jt-accomm-hero {
    background: linear-gradient(180deg, rgba(7, 30, 20, 0.78) 0%, rgba(10, 42, 28, 0.92) 100%), 
                url('https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=1920&auto=format&fit=crop') center/cover no-repeat;
    color: #ffffff;
    padding: 70px 0 80px;
}
.jt-accomm-card {
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
.jt-accomm-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(4, 120, 87, 0.15);
    border-color: rgba(4, 120, 87, 0.3);
}
.jt-accomm-img-wrap {
    position: relative;
    height: 210px;
    overflow: hidden;
    background: #0f172a;
}
.jt-accomm-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.jt-accomm-card:hover .jt-accomm-img-wrap img {
    transform: scale(1.06);
}
.jt-accomm-body {
    padding: 22px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
</style>

<div class="jt-accomm-hero">
    <div class="container public-container text-start">
        <div class="badge bg-info bg-opacity-25 text-info border border-info rounded-pill px-3 py-2 fw-bold mb-3">
            <i class="fa-solid fa-bed me-1"></i> Penginapan & Hotel Tegal
        </div>
        <h1 class="display-5 fw-extrabold mb-2">Temukan Tempat Menginap Nyaman</h1>
        <p class="lead text-white-50 max-w-2xl">
            Pilihan hotel bintang, homestay keluarga, dan villa pegunungan Guci terbaik di Tegal.
        </p>
    </div>
</div>

<div class="container public-container mb-5" style="margin-top: -40px; position: relative; z-index: 10;">
    <div class="bg-white rounded-4 p-4 shadow-sm border">
        <form class="row g-3" method="GET" action="{{ route('accommodation.index') }}">
            <div class="col-lg-3 col-md-6">
                <label class="form-label fs-8 fw-bold text-uppercase text-muted mb-1">Cari Hotel / Penginapan</label>
                <input name="q" value="{{ request('q') }}" class="form-control bg-light fs-7" placeholder="Nama hotel...">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label fs-8 fw-bold text-uppercase text-muted mb-1">Kategori</label>
                <select name="category" class="form-select bg-light fs-7">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category')===$category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label fs-8 fw-bold text-uppercase text-muted mb-1">Wilayah</label>
                <select name="region" class="form-select bg-light fs-7">
                    <option value="">Semua Wilayah</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" @selected(request('region')==$region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-emerald w-100 fw-bold py-2 rounded-3 text-white" style="background: #047857;">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Cari Penginapan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="container public-container mb-5">
    @if($items->isEmpty())
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
            <i class="fa-solid fa-hotel text-muted display-4 mb-3"></i>
            <h3 class="fs-5 fw-bold text-dark">Penginapan Belum Ditemukan</h3>
            <p class="text-muted fs-7">Coba ubah kata kunci atau filter pencarian Anda.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($items as $item)
                @php
                    $coverMedia = $item->media->where('pivot.role', 'cover')->first() ?? $item->media->first();
                    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=800&auto=format&fit=crop';
                    $minimum = $item->accommodation?->rooms->where('status','active')->min(fn($room)=>(float)$room->offer->price) ?? 450000;
                    $rating = $item->rating_average > 0 ? number_format($item->rating_average, 1) : '4.5';
                @endphp
                <div class="col-lg-4 col-md-6">
                    <article class="jt-accomm-card">
                        <div class="jt-accomm-img-wrap">
                            <img src="{{ $coverUrl }}" alt="{{ $item->name }}">
                            <span class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 backdrop-blur px-3 py-2 rounded-pill fs-8">
                                <i class="fa-solid fa-star text-warning me-1"></i>{{ $rating }}
                            </span>
                        </div>
                        <div class="jt-accomm-body">
                            <div class="fs-8 text-emerald fw-bold text-uppercase mb-1" style="color: #047857;">
                                <i class="fa-solid fa-location-dot me-1"></i> {{ $item->region?->name ?? 'Tegal' }}
                            </div>
                            <h3 class="fs-6 fw-bold mb-2">
                                <a href="{{ route('accommodation.show', $item->slug) }}" class="text-dark text-decoration-none">
                                    {{ $item->name }}
                                </a>
                            </h3>
                            <p class="fs-7 text-muted mb-3 flex-grow-1">
                                {{ str($item->description)->limit(100) }}
                            </p>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <div>
                                    <span class="fs-8 text-muted d-block">Mulai dari</span>
                                    <span class="fw-extrabold text-dark fs-6">Rp {{ number_format($minimum, 0, ',', '.') }}</span><span class="fs-8 text-muted">/malam</span>
                                </div>
                                <a href="{{ route('accommodation.show', $item->slug) }}" class="btn btn-sm btn-outline-emerald rounded-pill px-3 fw-bold" style="color: #047857; border-color: #047857;">
                                    Pilih Kamar
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
