@extends('layouts.public')

@section('title', $item->name . ' — ' . $title . ' Jelajah Tegal')
@section('meta-description', str($item->description ?: 'Detail informasi, menu, tarif, dan lokasi ' . $item->name . ' di Tegal.')->limit(155))
@section('canonical', route($routePrefix . '.show', $item->slug))

@section('content')
<style>
/* Hero Header */
.cd-hero-section {
    background: linear-gradient(135deg, #092018 0%, #134032 55%, #1b634b 100%);
    color: #ffffff;
    padding: 45px 0 65px;
    position: relative;
    overflow: hidden;
}
.cd-hero-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(242,169,59,0.15) 0%, transparent 60%);
}
.cd-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.75);
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}
.cd-breadcrumbs a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: color 0.2s;
}
.cd-breadcrumbs a:hover {
    color: #f2a93b;
}
.cd-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 20px;
    padding: 26px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(17,26,24,0.03);
}
.cd-card-title {
    font-size: 19px;
    font-weight: 800;
    color: var(--lokantara-text);
    margin: 0 0 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}
#cd-interactive-map {
    height: 280px;
    width: 100%;
    border-radius: 16px;
    z-index: 1;
}
</style>

@php
    $showHeroBgMap = [
        'culinary' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1600&q=80',
        'event' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1600&q=80',
        'rental' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1600&q=80',
        'tourism' => asset('images/guci_hero.png'),
    ];
    $coverMedia = $item->media->where('pivot.role', 'cover')->first() ?? $item->media->first();
    $currentShowBg = $coverMedia ? asset('storage/' . $coverMedia->object_key) : ($showHeroBgMap[$routePrefix ?? ''] ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80');
@endphp

<!-- Hero Section -->
<section class="cd-hero-section position-relative" style="background: linear-gradient(135deg, rgba(9, 32, 24, 0.84) 0%, rgba(19, 64, 50, 0.90) 100%), url('{{ $currentShowBg }}') center/cover no-repeat; padding: 55px 0 65px;">
    <div class="cd-hero-overlay"></div>
    <div class="container public-container position-relative" style="z-index: 2;">
        <!-- Breadcrumbs -->
        <nav class="cd-breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route($routePrefix . '.index') }}">{{ $title }}</a>
            <span>/</span>
            <span class="text-white fw-semibold">{{ $item->name }}</span>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-success text-white px-3 py-1" style="border-radius: 99px; font-size: 11px;">
                        <i class="fa-solid fa-circle-check me-1"></i> Terverifikasi Resmi
                    </span>
                    <span class="badge" style="background: rgba(45,140,168,0.3); color: #90cdf4; border: 1px solid rgba(45,140,168,0.4); border-radius: 99px; font-size: 11px;">
                        <i class="fa-solid fa-location-dot me-1"></i> {{ $item->region?->name ?? 'Tegal' }}
                    </span>
                    <span class="badge" style="background: rgba(242,169,59,0.25); color: #fbd38d; border: 1px solid rgba(242,169,59,0.4); border-radius: 99px; font-size: 11px;">
                        <i class="fa-solid fa-tag me-1"></i> {{ $item->category?->name ?? $title }}
                    </span>
                </div>

                <h1 class="fs-1 fw-bold text-white mb-2">{{ $item->name }}</h1>
                
                <div class="d-flex align-items-center gap-2 mb-3 text-white-50" style="font-size: 14px;">
                    <div class="d-flex align-items-center text-warning gap-1">
                        <i class="fa-solid fa-star"></i> <strong class="text-white">{{ number_format($item->rating_average, 1) }}</strong>
                    </div>
                    <span>·</span>
                    <span>{{ $item->reviews->count() }} Ulasan Wisatawan</span>
                    <span>·</span>
                    <span>Dikelola oleh: <strong>{{ $item->mitra?->display_name ?? 'Mitra Jelajah Tegal' }}</strong></span>
                </div>

                <p class="text-white-50 mb-0" style="font-size: 14px; max-width: 650px;">
                    {{ $item->description ?: 'Layanan ' . strtolower($title) . ' unggulan di Tegal.' }}
                </p>
            </div>

            <!-- Cover Photo Box -->
            <div class="col-lg-5">
                @php
                    $cover = $item->media->where('pivot.role', 'cover')->first() ?? $item->media->first();
                    $coverUrl = $cover ? asset('storage/' . $cover->object_key) : null;
                @endphp
                <div style="border-radius: 20px; overflow: hidden; height: 260px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); border: 2px solid rgba(255,255,255,0.2); background: #174d3c;">
                    @if($coverUrl)
                        <img src="{{ $coverUrl }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: grid; place-items: center; color: #fff; font-size: 48px;">
                            @if($routePrefix === 'culinary') <i class="fa-solid fa-utensils"></i> @elseif($routePrefix === 'event') <i class="fa-solid fa-ticket"></i> @else <i class="fa-solid fa-car"></i> @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Details Section -->
<section class="public-section pt-4">
    <div class="container public-container">
        <div class="row g-4">
            <!-- Left Main Column (8 Cols) -->
            <div class="col-lg-8">
                <!-- 1. Deskripsi & Foto Galeri -->
                <div class="cd-card">
                    <h2 class="cd-card-title"><i class="fa-solid fa-book-open text-emerald me-2"></i> Tentang {{ $item->name }}</h2>
                    <p style="color: var(--lokantara-muted); line-height: 1.7; font-size: 14px;">
                        {{ $item->description ?: 'Informasi lengkap mengenai tempat ini sedang dipersiapkan oleh Mitra pengelola.' }}
                    </p>

                    @if($item->media->where('pivot.role', 'gallery')->isNotEmpty())
                        <h4 class="fs-6 fw-bold mt-4 mb-2">Galeri Foto:</h4>
                        <div class="row g-2">
                            @foreach($item->media->where('pivot.role', 'gallery') as $gal)
                                <div class="col-4">
                                    <div style="height: 110px; border-radius: 10px; overflow: hidden;">
                                        <img src="{{ asset('storage/' . $gal->object_key) }}" alt="Galeri" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- 2. Khusus KULINER: Buku Menu Makanan & Minuman -->
                @if($routePrefix === 'culinary' && $item->culinary)
                    <div class="cd-card">
                        <h2 class="cd-card-title"><i class="fa-solid fa-utensils text-warning me-2"></i> Daftar Menu & Harga</h2>

                        @forelse($item->culinary->menuCategories as $cat)
                            <div class="mb-4">
                                <h3 class="fs-6 fw-bold text-dark mb-3 pb-2 border-bottom">
                                    <i class="fa-solid fa-bowl-food text-success me-2"></i> {{ $cat->name }}
                                </h3>

                                <div class="row g-3">
                                    @forelse($cat->items->where('status', 'active') as $menu)
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                                                <div>
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <strong class="text-dark">{{ $menu->name }}</strong>
                                                        @if($menu->is_featured)
                                                            <span class="badge bg-warning text-dark" style="font-size: 10px;">Favorit</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-muted mb-2" style="font-size: 12px;">{{ $menu->description ?: 'Menu khas pilihan lezat.' }}</p>
                                                </div>
                                                <div class="fw-bold" style="color: var(--lokantara-primary); font-size: 14px;">
                                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12"><small class="text-muted">Menu belum ditambahkan.</small></div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <x-empty-state title="Menu Belum Tersedia" description="Mitra sedang melengkapi daftar menu makanan & minuman." compact />
                        @endforelse
                    </div>

                    <!-- Form Reservasi Meja jika Menerima Reservasi -->
                    @if($item->culinary->accepts_reservations)
                        <div class="cd-card">
                            <h2 class="cd-card-title"><span>🪑</span> Reservasi Meja / Slot Waktu</h2>
                            <p class="text-muted" style="font-size: 13px;">Pesan tempat Anda terlebih dahulu untuk kenyamanan bersantap bersama keluarga.</p>

                            @if($item->culinary->tableSlots->isEmpty())
                                <div class="p-3 rounded bg-light text-muted" style="font-size: 13px;">
                                    Saat ini belum ada slot jadwal reservasi yang dibuka. Silakan hubungi langsung pihak tempat makan.
                                </div>
                            @else
                                <div class="d-flex flex-column gap-3">
                                    @foreach($item->culinary->tableSlots as $slot)
                                        <div class="p-3 rounded-3 border d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: var(--lokantara-background);">
                                            <div>
                                                <strong><i class="fa-regular fa-calendar text-primary me-1"></i> {{ $slot->service_date?->format('d M Y') }}</strong> · Jam {{ $slot->start_time }} - {{ $slot->end_time }}
                                                <small class="text-muted d-block">Kapasitas meja: {{ $slot->capacity }} orang</small>
                                            </div>
                                            @auth
                                                <form method="POST" action="{{ route('culinary.reserve', [$item->slug, $slot]) }}">
                                                    @csrf
                                                    <input type="hidden" name="party_size" value="2">
                                                    <button class="btn btn-sm btn-lokantara fw-bold px-3">
                                                        Pesan Slot Ini
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-lokantara">
                                                    Login untuk Reservasi
                                                </a>
                                            @endauth
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @endif

                <!-- 3. Khusus EVENT -->
                @if($routePrefix === 'event' && $item->event)
                    <div class="cd-card">
                        <h2 class="cd-card-title"><i class="fa-solid fa-ticket text-danger me-2"></i> Tiket & Jadwal Event</h2>
                        @foreach($item->event->ticketTypes as $type)
                            <div class="p-3 rounded-3 mb-2 d-flex align-items-center justify-content-between" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                                <div>
                                    <strong>{{ $type->name }}</strong>
                                    <small class="text-muted d-block">Sisa kuota: {{ max(0, $type->quota - $type->issued_quantity) }} tiket</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold fs-5" style="color: var(--lokantara-primary);">Rp {{ number_format($type->offer->price, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- 4. Khusus RENTAL -->
                @if($routePrefix === 'rental' && $item->rentalVehicle)
                    <div class="cd-card">
                        <h2 class="cd-card-title"><i class="fa-solid fa-car text-primary me-2"></i> Tarif Sewa Armada</h2>
                        @foreach($item->rentalVehicle->rates as $rate)
                            <div class="p-3 rounded-3 mb-2 d-flex align-items-center justify-content-between" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                                <div>
                                    <strong class="fs-6">{{ str($rate->drive_mode)->headline() }}</strong>
                                    <small class="text-muted d-block">Durasi: {{ $rate->duration_value }} {{ str($rate->duration_unit)->headline() }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold fs-5" style="color: var(--lokantara-primary);">Rp {{ number_format($rate->offer->price, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- 5. Ulasan Pengunjung -->
                <div class="cd-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="cd-card-title mb-0"><i class="fa-solid fa-star text-warning me-2"></i> Ulasan Pengunjung ({{ $item->reviews->count() }})</h2>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2 fs-8">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 fs-8">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @forelse($item->reviews as $review)
                        <div class="p-3 rounded-3 mb-3" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2.5">
                                    @if($review->user?->profile?->avatar)
                                        <img src="{{ asset('storage/' . $review->user->profile->avatar->object_key) }}" alt="{{ $review->user->name }}" class="rounded-circle border shadow-sm flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #047857 0%, #10b981 100%); color: #fff; display: grid; place-items: center; font-weight: 700; font-size: 15px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(4,120,87,0.2);">
                                            {{ strtoupper(substr($review->user?->name ?? 'P', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="fs-7 text-dark">{{ $review->user?->name ?? 'Pengunjung' }}</strong>
                                        <div class="d-flex align-items-center gap-1 mt-0.5">
                                            <div class="text-warning" style="font-size: 12px;">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2 py-0.5" style="font-size: 10px;">
                                                {{ $review->rating }}.0
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">{{ $review->created_at?->diffForHumans() }}</small>
                            </div>

                            @if ($review->title)
                                <h6 class="fw-bold mb-1 fs-7" style="color: var(--lokantara-text);">{{ $review->title }}</h6>
                            @endif
                            <p class="text-secondary mb-2" style="font-size: 13.5px; line-height: 1.6;">{{ $review->body }}</p>

                            <!-- Nested Replies List -->
                            @if ($review->replies->isNotEmpty())
                                <div class="mt-3 pt-2.5 border-top d-flex flex-column gap-2" style="border-color: rgba(0,0,0,0.06) !important;">
                                    @foreach ($review->replies as $reply)
                                        <div class="p-2.5 rounded-3 ms-2 ms-md-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($reply->author?->profile?->avatar)
                                                        <img src="{{ asset('storage/' . $reply->author->profile->avatar->object_key) }}" alt="{{ $reply->author->name }}" class="rounded-circle border" style="width: 24px; height: 24px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 24px; height: 24px; font-size: 10px; background: #047857;">
                                                            {{ strtoupper(substr($reply->author?->name ?? 'P', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <strong class="fs-8 text-dark">{{ $reply->author?->name ?? 'Pengguna' }}</strong>
                                                    @if ($reply->mitra_id)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 9.5px;">
                                                            <i class="fa-solid fa-shield-halved me-0.5"></i> Mitra Pengelola
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 9.5px;">
                                                            <i class="fa-solid fa-user me-0.5"></i> Pengunjung
                                                        </span>
                                                    @endif
                                                </div>
                                                <small class="text-muted" style="font-size: 10.5px;">{{ $reply->created_at?->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0 text-muted" style="font-size: 12.5px; line-height: 1.5;">{{ $reply->body }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Action: Reply Toggle & Form -->
                            <div class="mt-2.5 pt-2 d-flex align-items-center justify-content-between">
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-primary fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 12px;" data-bs-toggle="collapse" data-bs-target="#replyBox-cd-{{ $review->id }}" aria-expanded="false">
                                    <i class="fa-solid fa-reply"></i>
                                    <span>Balas Ulasan ({{ $review->replies->count() }})</span>
                                </button>
                            </div>

                            <div class="collapse mt-2.5" id="replyBox-cd-{{ $review->id }}">
                                @auth
                                    <form method="POST" action="{{ route('public.reviews.replies.store', $review->id) }}" class="p-3 rounded-3 bg-white border shadow-sm">
                                        @csrf
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            @if(auth()->user()->profile?->avatar)
                                                <img src="{{ asset('storage/' . auth()->user()->profile->avatar->object_key) }}" alt="Avatar" class="rounded-circle border" style="width: 26px; height: 26px; object-fit: cover;">
                                            @endif
                                            <small class="fw-bold text-dark fs-8">Tulis tanggapan sebagai {{ auth()->user()->name }}:</small>
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="body" class="form-control form-control-sm" rows="2" placeholder="Tulis tanggapan atau pengalaman Anda..." required style="font-size: 12.5px; border-radius: 8px;"></textarea>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-light py-1 px-3 border rounded-pill" style="font-size: 11.5px;" data-bs-toggle="collapse" data-bs-target="#replyBox-cd-{{ $review->id }}">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-lokantara py-1 px-3 rounded-pill fw-bold" style="font-size: 11.5px;">Kirim Balasan</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="p-2.5 rounded-3 bg-light border text-center" style="font-size: 12px;">
                                        <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none"><i class="fa-regular fa-user me-1"></i>Masuk (Login)</a> untuk menulis balasan ulasan ini.
                                    </div>
                                @endauth
                            </div>
                        </div>
                    @empty
                        <x-empty-state title="Belum Ada Ulasan" description="Jadilah yang pertama memberikan ulasan setelah berkunjung." compact />
                    @endforelse

                    <!-- Review Form Box -->
                    <div class="mt-4 pt-3 border-top">
                        <x-review-form :action="route($routePrefix . '.reviews.store', $item->slug)" :itemType="$serviceType->name ?? 'layanan'" />
                    </div>
                </div>
            </div>

            <!-- Right Sidebar (4 Cols) -->
            <div class="col-lg-4">
                <!-- Location & Interactive Map Card -->
                <div class="cd-card" style="position: sticky; top: 90px;">
                    <h3 class="fs-6 fw-bold mb-3"><i class="fa-solid fa-map-location-dot text-success me-2"></i> Lokasi & Petunjuk Arah</h3>
                    
                    @php
                        $lat = $item->location?->latitude ?? -6.8730933;
                        $lng = $item->location?->longitude ?? 109.2541104;
                    @endphp

                    <!-- Interactive Map Container -->
                    <div id="cd-interactive-map" class="mb-3" style="height: 380px; width: 100%; border-radius: 16px; overflow: hidden; background: #e9ecef; z-index: 1; border: 1px solid var(--lokantara-border);"></div>

                    <div class="mb-3">
                        <strong class="d-block" style="font-size: 12px; color: var(--lokantara-muted); text-transform: uppercase;"><i class="fa-solid fa-location-dot text-danger me-1"></i> Alamat:</strong>
                        <p class="mb-0 text-dark" style="font-size: 13px;">{{ $item->address ?: 'Wilayah Tegal' }}</p>
                    </div>

                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lat }},{{ $lng }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-lokantara w-100 fw-bold py-2 fs-7 d-flex align-items-center justify-content-center gap-2 mb-3">
                        <i class="fa-solid fa-map-location-dot text-emerald"></i> Buka Google Maps &rarr;
                    </a>

                    <hr>

                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 42px; height: 42px; border-radius: 10px; background: #134032; color: #fff; display: grid; place-items: center; font-weight: bold;">
                            {{ str($item->mitra?->display_name ?? 'M')->substr(0,1)->upper() }}
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">Dikelola oleh:</small>
                            <a href="{{ route('public.mitra.show', $item->mitra?->slug ?? 'lokantara') }}" class="text-decoration-none fw-bold text-dark">
                                {{ $item->mitra?->display_name ?? 'Mitra Jelajah Tegal' }} &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leaflet Map: Lazy-loaded when map enters viewport -->
<script>
(function() {
    const lat = {{ $lat }};
    const lng = {{ $lng }};
    function initMap() {
        const name = "{{ addslashes($item->name) }}";
        const address = "{{ addslashes($item->address ?? 'Tegal') }}";
        if (typeof window.initLokantaraMap === 'function') {
            if (window._cdMapInitialized) return;
            window._cdMapInitialized = true;
            window.initLokantaraMap('cd-interactive-map', lat, lng, name, address, 'tourism');
        } else {
            setTimeout(initMap, 50);
        }
    }
    const mapEl = document.getElementById('cd-interactive-map');
    if (mapEl && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) { observer.disconnect(); initMap(); }
        }, { rootMargin: '200px' });
        observer.observe(mapEl);
    } else if (mapEl) { initMap(); }
})();
</script>
@endsection
