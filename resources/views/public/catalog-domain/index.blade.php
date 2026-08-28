@extends('layouts.public')

@section('title', 'Jelajahi ' . $title . ' — Jelajah Tegal')
@section('meta-description', 'Temukan rekomendasi ' . strtolower($title) . ' terbaik, terverifikasi, dan terlengkap di Tegal.')
@if(request()->query()) @section('robots', 'noindex,follow') @endif

@section('content')
<style>
.domain-hero-section {
    background: linear-gradient(135deg, #0a231b 0%, #134032 55%, #1b634b 100%);
    color: #ffffff;
    padding: 60px 0 50px;
    position: relative;
    overflow: hidden;
}
.domain-hero-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(242,169,59,0.15) 0%, transparent 60%);
}
.domain-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.25s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.domain-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 32px rgba(17,26,24,0.1);
    border-color: var(--lokantara-primary);
}
.domain-card-img-wrap {
    height: 200px;
    position: relative;
    background: #cbd5e1;
    overflow: hidden;
}
.domain-card-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.domain-card:hover .domain-card-img-wrap img {
    transform: scale(1.06);
}
.domain-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(17,26,24,0.75);
    backdrop-filter: blur(8px);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
}
</style>

@php
    $heroBgMap = [
        'culinary' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1600&q=80',
        'event' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1600&q=80',
        'rental' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1600&q=80',
        'tourism' => asset('images/guci_hero.png'),
    ];
    $currentBg = $heroBgMap[$routePrefix ?? ''] ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80';
@endphp

<!-- Hero Header -->
<section class="domain-hero-section position-relative" style="background: linear-gradient(135deg, rgba(9, 32, 24, 0.82) 0%, rgba(19, 64, 50, 0.88) 100%), url('{{ $currentBg }}') center/cover no-repeat; padding: 75px 0 65px;">
    <div class="domain-hero-overlay"></div>
    <div class="container public-container position-relative" style="z-index: 2;">
        <p class="public-eyebrow mb-2" style="color: #f2a93b;">Pilihan Terbaik di Tegal</p>
        <h1 class="fs-1 fw-bold text-white mb-2">Jelajahi {{ $title }} Khas Tegal</h1>
        <p class="text-white-50 mb-4" style="max-width: 600px;">
            Temukan tempat {{ strtolower($title) }} pilihan dan terverifikasi di seluruh Kabupaten & Kota Tegal.
        </p>

        <!-- Search Bar -->
        <form class="p-3 rounded-4" style="background: rgba(255,255,255,0.14); backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,0.25); max-width: 700px;" method="GET" action="{{ route($routePrefix . '.index') }}">
            <div class="input-group">
                <input class="form-control border-0 py-2 ps-3" name="q" value="{{ request('q') }}" placeholder="Cari nama {{ strtolower($title) }}, menu, atau lokasi..." style="border-radius: 12px 0 0 12px; font-size: 14px; background: rgba(255,255,255,0.95);">
                <button class="btn btn-emerald fw-bold px-4 text-white" style="border-radius: 0 12px 12px 0; background: #047857;">Cari</button>
            </div>
        </form>
    </div>
</section>

<!-- Content Grid -->
<section class="public-section">
    <div class="container public-container">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <h2 class="fs-4 fw-bold text-dark mb-0">Daftar {{ $title }} Tersedia</h2>
            <span class="text-muted fw-semibold">{{ $items->total() }} Tempat Terverifikasi</span>
        </div>

        @if($items->isEmpty())
            <x-empty-state :title="$title . ' Belum Tersedia'" description="Belum ada data published yang sesuai dengan pencarian Anda." />
        @else
            <div class="row g-4">
                @foreach($items as $item)
                    @php
                        $cover = $item->media->where('pivot.role', 'cover')->first() ?? $item->media->first();
                        $coverUrl = $cover ? asset('storage/' . $cover->object_key) : null;
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <article class="domain-card">
                            <a href="{{ route($routePrefix . '.show', $item->slug) }}" class="domain-card-img-wrap text-decoration-none">
                                @if($coverUrl)
                                    <img src="{{ $coverUrl }}" alt="{{ $item->name }}">
                                @else
                                    <div style="width: 100%; height: 100%; display: grid; place-items: center; background: linear-gradient(135deg, #134032, #1b634b); color: #fff; font-size: 40px;">
                                        @if($routePrefix === 'culinary') <i class="fa-solid fa-utensils"></i> @elseif($routePrefix === 'event') <i class="fa-solid fa-ticket"></i> @else <i class="fa-solid fa-car"></i> @endif
                                    </div>
                                @endif
                                <span class="domain-badge">
                                    <i class="fa-solid fa-location-dot me-1"></i> {{ $item->region?->name ?? 'Tegal' }}
                                </span>
                            </a>

                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <small class="text-muted" style="font-size: 11px;">{{ $item->category?->name ?? $title }}</small>
                                    <div class="d-flex align-items-center gap-1 text-warning" style="font-size: 12px;">
                                        <i class="fa-solid fa-star"></i> <strong>{{ number_format($item->rating_average, 1) }}</strong>
                                    </div>
                                </div>

                                <h3 class="fs-5 fw-bold mb-2">
                                    <a href="{{ route($routePrefix . '.show', $item->slug) }}" class="text-decoration-none text-dark stretched-link">
                                        {{ $item->name }}
                                    </a>
                                </h3>

                                <p class="text-muted flex-grow-1 mb-3" style="font-size: 13px; line-height: 1.5;">
                                    {{ str($item->description ?: 'Layanan ' . strtolower($title) . ' terverifikasi di Tegal.')->limit(90) }}
                                </p>

                                <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                    <span class="fw-semibold text-muted" style="font-size: 12px;">
                                        Mitra: {{ $item->mitra?->display_name ?? 'Jelajah Tegal' }}
                                    </span>
                                    <a href="{{ route($routePrefix . '.show', $item->slug) }}" class="btn btn-sm btn-lokantara fw-bold px-3">
                                        Lihat Detail &rarr;
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <div class="public-pagination mt-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
