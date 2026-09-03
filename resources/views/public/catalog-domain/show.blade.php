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

                <!-- 2. Khusus KULINER: E-Voucher & Daftar Menu -->
                @if($routePrefix === 'culinary' && $item->culinary)
                    <!-- Pilihan E-Voucher & Paket Menu Hemat (Premium Coupon UI) -->
                    @if($item->offers && $item->offers->isNotEmpty())
                        @php
                            $cashVouchers = $item->offers->filter(fn($o) => str_contains(strtolower($o->name), 'bebas') || str_contains(strtolower($o->sku), 'cash'));
                            $packageVouchers = $item->offers->reject(fn($o) => str_contains(strtolower($o->name), 'bebas') || str_contains(strtolower($o->sku), 'cash'));
                        @endphp

                        <div class="cd-card border-0 shadow-sm p-4 rounded-4 mb-4 position-relative overflow-hidden" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0 !important;">
                            <!-- Header Bar -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-3 border-bottom">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge rounded-pill px-2.5 py-1 text-white fw-bold fs-8" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 2px 6px rgba(245,158,11,0.3);">
                                            <i class="fa-solid fa-gift me-1"></i> E-VOUCHER PROMO
                                        </span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold fs-8">
                                            <i class="fa-solid fa-qrcode me-1"></i> Instant QR Redeem
                                        </span>
                                    </div>
                                    <h2 class="h5 fw-bold text-dark mb-0" style="font-size: 18px;">
                                        Pilihan E-Voucher & Paket Menu Hemat
                                    </h2>
                                    <p class="text-muted small mb-0 mt-0.5" style="font-size: 12.5px;">
                                        Beli voucher santap online dengan harga promo. Cukup tunjukkan kode QR saat tiba di restoran.
                                    </p>
                                </div>

                                <!-- Tab Switcher -->
                                <div class="d-flex align-items-center p-1 bg-white rounded-pill border shadow-xs" style="font-size: 12px;">
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 border-0 vch-tab-btn btn-primary text-white shadow-xs" id="btnTabCash" onclick="switchVchTab('cash')">
                                        <i class="fa-solid fa-wallet me-1"></i> Voucher Bebas Menu ({{ $cashVouchers->count() }})
                                    </button>
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 border-0 vch-tab-btn text-muted" id="btnTabPackage" onclick="switchVchTab('package')">
                                        <i class="fa-solid fa-bowl-food me-1"></i> Paket Menu ({{ $packageVouchers->count() }})
                                    </button>
                                </div>
                            </div>

                            <!-- List Tab 1: Voucher Saldo Bebas Menu -->
                            <div id="vchTabContentCash" class="d-flex flex-column gap-3">
                                @forelse($cashVouchers as $vch)
                                    @php
                                        // Extract face value if available in name (e.g. 50.000, 100.000, 200.000)
                                        preg_match('/Rp\s*([0-9\.]+)/i', $vch->name, $matches);
                                        $nominalText = $matches[1] ?? number_format($vch->price, 0, ',', '.');
                                        $nominalNum = (int) str_replace('.', '', $nominalText);
                                        $savedAmount = max(0, $nominalNum - (float) $vch->price);
                                    @endphp
                                    <div class="vch-ticket-card rounded-4 border position-relative overflow-hidden" style="background: #ffffff; transition: all 0.25s ease; border-color: #cbd5e1 !important; box-shadow: 0 3px 12px rgba(15,23,42,0.04);">
                                        <div class="row g-0 align-items-stretch">
                                            <!-- Sisi Kiri: Nilai Saldo Voucher -->
                                            <div class="col-12 col-md-3 p-3.5 d-flex flex-column justify-content-center align-items-center text-center position-relative" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: #ffffff;">
                                                <small class="text-white-50 text-uppercase fw-bold letter-spacing-1 mb-0.5" style="font-size: 10px; letter-spacing: 0.08em;">NILAI VOUCHER</small>
                                                <div class="fw-extrabold text-white" style="font-size: 20px; line-height: 1.1;">
                                                    Rp {{ $nominalText }}
                                                </div>
                                                @if($savedAmount > 0)
                                                    <span class="badge rounded-pill mt-2 px-2.5 py-0.5 fw-bold" style="background: rgba(255,255,255,0.22); color: #fef08a; font-size: 10.5px; border: 1px solid rgba(255,255,255,0.35);">
                                                        Hemat Rp {{ number_format($savedAmount, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill mt-2 px-2 py-0.5" style="background: rgba(255,255,255,0.2); font-size: 10px;">Bebas Pilih</span>
                                                @endif
                                            </div>

                                            <!-- Sisi Kanan: Informasi & Tombol Beli -->
                                            <div class="col-12 col-md-9 p-3.5 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1.5">
                                                        <h4 class="fw-bold text-dark fs-6 mb-0">{{ $vch->name }}</h4>
                                                        <span class="badge bg-blue-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold fs-8 flex-shrink-0" style="background: #eff6ff; color: #1d4ed8 !important;">
                                                            <i class="fa-solid fa-wallet me-1"></i> Potong Kasir
                                                        </span>
                                                    </div>
                                                    <p class="text-muted mb-2.5" style="font-size: 12.5px; line-height: 1.45;">
                                                        {{ $vch->description ?: 'Bebas pilih makanan & minuman apa saja di lokasi. Tunjukkan QR voucher saat bayar di kasir untuk potong total tagihan.' }}
                                                    </p>
                                                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-3" style="font-size: 11.5px;">
                                                        <span class="d-inline-flex align-items-center gap-1"><i class="fa-regular fa-clock text-primary"></i> Berlaku 30 Hari</span>
                                                        <span>&middot;</span>
                                                        <span class="d-inline-flex align-items-center gap-1 text-success fw-semibold"><i class="fa-solid fa-circle-check"></i> Dine-in & Takeaway</span>
                                                        <span>&middot;</span>
                                                        <span class="d-inline-flex align-items-center gap-1"><i class="fa-solid fa-shield-halved text-warning"></i> Garansi Resmi</span>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center justify-content-between pt-2.5 border-top border-light-subtle flex-wrap gap-2">
                                                    <div>
                                                        <small class="text-muted d-block fs-8">Harga Beli Promo:</small>
                                                        <div class="fs-5 fw-extrabold text-primary" style="line-height: 1.1;">
                                                            Rp {{ number_format($vch->price, 0, ',', '.') }}
                                                        </div>
                                                    </div>

                                                    @auth
                                                        <button type="button" class="btn btn-primary text-white fw-bold px-4 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#vchModal-{{ $vch->id }}" style="font-size: 13px;">
                                                            <i class="fa-solid fa-cart-shopping"></i> Beli Voucher
                                                        </button>
                                                    @else
                                                        <a href="{{ route('login') }}" class="btn btn-outline-primary fw-bold px-4 py-1.5 rounded-pill" style="font-size: 13px;">
                                                            Login untuk Beli
                                                        </a>
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 bg-light rounded-3 text-center text-muted small">
                                        Belum ada voucher saldo bebas menu yang tersedia.
                                    </div>
                                @endforelse
                            </div>

                            <!-- List Tab 2: Paket Menu Promo Komplit -->
                            <div id="vchTabContentPackage" class="d-flex flex-column gap-3" style="display: none !important;">
                                @forelse($packageVouchers as $vch)
                                    <div class="vch-ticket-card rounded-4 border position-relative overflow-hidden" style="background: #ffffff; transition: all 0.25s ease; border-color: #cbd5e1 !important; box-shadow: 0 3px 12px rgba(15,23,42,0.04);">
                                        <div class="row g-0 align-items-stretch">
                                            <!-- Sisi Kiri: Badge Paket Menu -->
                                            <div class="col-12 col-md-3 p-3.5 d-flex flex-column justify-content-center align-items-center text-center position-relative" style="background: linear-gradient(135deg, #065f46 0%, #10b981 100%); color: #ffffff;">
                                                <i class="fa-solid fa-bowl-food text-warning mb-1" style="font-size: 22px;"></i>
                                                <small class="text-white-50 text-uppercase fw-bold letter-spacing-1 mb-0.5" style="font-size: 10px; letter-spacing: 0.08em;">PAKET MENU</small>
                                                <div class="fw-extrabold text-white" style="font-size: 18px; line-height: 1.1;">
                                                    Hemat & Kenyang
                                                </div>
                                                <span class="badge rounded-pill mt-2 px-2 py-0.5" style="background: rgba(255,255,255,0.2); font-size: 10px;">Menu Komplit</span>
                                            </div>

                                            <!-- Sisi Kanan: Rincian Paket & Beli -->
                                            <div class="col-12 col-md-9 p-3.5 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1.5">
                                                        <h4 class="fw-bold text-dark fs-6 mb-0">{{ $vch->name }}</h4>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold fs-8 flex-shrink-0">
                                                            <i class="fa-solid fa-utensils me-1"></i> Paket Promo
                                                        </span>
                                                    </div>
                                                    <p class="text-muted mb-2.5" style="font-size: 12.5px; line-height: 1.45;">
                                                        {{ $vch->description ?: 'Paket hemat makanan & minuman siap santap di tempat atau bungkus.' }}
                                                    </p>
                                                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-3" style="font-size: 11.5px;">
                                                        <span class="d-inline-flex align-items-center gap-1"><i class="fa-regular fa-clock text-primary"></i> Berlaku 30 Hari</span>
                                                        <span>&middot;</span>
                                                        <span class="d-inline-flex align-items-center gap-1 text-success fw-semibold"><i class="fa-solid fa-circle-check"></i> Siap Santap di Lokasi</span>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center justify-content-between pt-2.5 border-top border-light-subtle flex-wrap gap-2">
                                                    <div>
                                                        <small class="text-muted d-block fs-8">Harga Paket:</small>
                                                        <div class="fs-5 fw-extrabold text-success" style="line-height: 1.1;">
                                                            Rp {{ number_format($vch->price, 0, ',', '.') }}
                                                        </div>
                                                    </div>

                                                    @auth
                                                        <button type="button" class="btn btn-lokantara fw-bold px-4 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#vchModal-{{ $vch->id }}" style="font-size: 13px;">
                                                            <i class="fa-solid fa-cart-shopping"></i> Beli Paket
                                                        </button>
                                                    @else
                                                        <a href="{{ route('login') }}" class="btn btn-outline-lokantara fw-bold px-4 py-1.5 rounded-pill" style="font-size: 13px;">
                                                            Login untuk Beli
                                                        </a>
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 bg-light rounded-3 text-center text-muted small">
                                        Belum ada paket menu promo yang tersedia.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Modals for all Vouchers -->
                        @foreach($item->offers as $vch)
                            <div class="modal fade" id="vchModal-{{ $vch->id }}" tabindex="-1" aria-labelledby="vchModalLabel-{{ $vch->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                                        <div class="modal-header bg-success text-white border-0 py-3 px-4">
                                            <h5 class="modal-title fs-6 fw-bold text-white d-flex align-items-center" id="vchModalLabel-{{ $vch->id }}">
                                                <i class="fa-solid fa-ticket text-warning me-2"></i> Beli E-Voucher Kuliner
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <form action="{{ route('consumer.checkout.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
                                            <input type="hidden" name="domain" value="culinary">
                                            <input type="hidden" name="reference_id" value="{{ $vch->id }}">

                                            <div class="modal-body p-4">
                                                <div class="p-3.5 rounded-3 mb-3 bg-light border">
                                                    <h6 class="fw-bold mb-1 text-dark">{{ $vch->name }}</h6>
                                                    <p class="text-muted small mb-1">{{ $item->name }} — {{ $item->region?->name ?? 'Tegal' }}</p>
                                                    <div class="fs-5 fw-extrabold text-success">
                                                        Rp {{ number_format($vch->price, 0, ',', '.') }} <span class="fs-8 fw-normal text-muted">/ voucher</span>
                                                    </div>
                                                </div>

                                                <!-- Rencana Tanggal Penggunaan -->
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold fs-7 text-dark">Rencana Tanggal Penggunaan <span class="text-danger">*</span></label>
                                                    <input type="date" name="service_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                                                    <small class="text-muted" style="font-size: 11px;">*Voucher tetap dapat digunakan hingga 30 hari ke depan.</small>
                                                </div>

                                                <!-- Jumlah Voucher -->
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold fs-7 text-dark mb-1">Jumlah Voucher <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <button type="button" class="btn btn-outline-secondary px-3" onclick="let q = document.getElementById('vchQty-{{ $vch->id }}'); if(parseInt(q.value) > 1) { q.value = parseInt(q.value) - 1; calcVchTotal{{ $vch->id }}(); }">-</button>
                                                        <input type="number" id="vchQty-{{ $vch->id }}" name="quantity" class="form-control text-center fw-bold" value="1" min="1" max="10" onchange="calcVchTotal{{ $vch->id }}()" required>
                                                        <button type="button" class="btn btn-outline-secondary px-3" onclick="let q = document.getElementById('vchQty-{{ $vch->id }}'); if(parseInt(q.value || 0) < 10) { q.value = parseInt(q.value || 0) + 1; calcVchTotal{{ $vch->id }}(); }">+</button>
                                                    </div>
                                                </div>

                                                <!-- Kode Promo (Opsional) -->
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold fs-7 text-dark">Kode Voucher Promo (Opsional)</label>
                                                    <input type="text" name="voucher_code" class="form-control rounded-3 text-uppercase" placeholder="Contoh: TEGALHEMAT">
                                                </div>

                                                <!-- Total Pembayaran -->
                                                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                                    <span class="fw-bold text-muted fs-7">Total Pembayaran</span>
                                                    <span class="fs-4 fw-extrabold text-success" id="vchTotalDisplay-{{ $vch->id }}">
                                                        Rp {{ number_format($vch->price, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                                                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold text-white">
                                                    Lanjutkan Pembayaran <i class="fa-solid fa-arrow-right ms-1"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function calcVchTotal{{ $vch->id }}() {
                                    const qty = parseInt(document.getElementById('vchQty-{{ $vch->id }}').value) || 1;
                                    const unitPrice = {{ (float) $vch->price }};
                                    const total = qty * unitPrice;
                                    const display = document.getElementById('vchTotalDisplay-{{ $vch->id }}');
                                    if (display) {
                                        display.textContent = 'Rp ' + total.toLocaleString('id-ID');
                                    }
                                }
                            </script>
                        @endforeach

                        <script>
                            function switchVchTab(type) {
                                const btnCash = document.getElementById('btnTabCash');
                                const btnPkg = document.getElementById('btnTabPackage');
                                const contentCash = document.getElementById('vchTabContentCash');
                                const contentPkg = document.getElementById('vchTabContentPackage');

                                if (type === 'cash') {
                                    btnCash.className = 'btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 border-0 vch-tab-btn btn-primary text-white shadow-xs';
                                    btnPkg.className = 'btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 border-0 vch-tab-btn text-muted';
                                    contentCash.style.removeProperty('display');
                                    contentCash.style.display = 'flex';
                                    contentPkg.style.display = 'none';
                                } else {
                                    btnPkg.className = 'btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 border-0 vch-tab-btn btn-success text-white shadow-xs';
                                    btnCash.className = 'btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 border-0 vch-tab-btn text-muted';
                                    contentPkg.style.removeProperty('display');
                                    contentPkg.style.display = 'flex';
                                    contentCash.style.display = 'none';
                                }
                            }
                        </script>
                    @endif

                    <!-- Buku Menu Makanan & Minuman -->
                    <div class="cd-card">
                        <h2 class="cd-card-title"><i class="fa-solid fa-utensils text-warning me-2"></i> Daftar Menu & Harga Lengkap</h2>

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
                            <h2 class="cd-card-title"><i class="fa-solid fa-chair text-primary me-2"></i> Reservasi Meja / Slot Waktu</h2>
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
                <div class="mt-4 pt-2">
                    @if (session('status'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2.5 px-3 mb-3 d-flex align-items-center gap-2 fs-8">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2.5 px-3 mb-3 fs-8">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Review Form Box -->
                    <div class="mb-4" id="section-tulis-ulasan">
                        <x-review-form :action="route($routePrefix . '.reviews.store', $item->slug)" :itemType="$serviceType->name ?? 'layanan'" />
                    </div>

                    <!-- Section Heading -->
                    <h5 class="fw-bold text-dark mb-3 mt-4" style="font-size: 16px;">
                        Semua Ulasan Wisatawan ({{ $item->reviews->count() }})
                    </h5>

                    @forelse($item->reviews as $review)
                        <div class="p-3.5 mb-3 rounded-4 shadow-sm bg-white border" style="border-color: #e2e8f0 !important; transition: all 0.2s ease;">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2.5">
                                    @if($review->user?->profile?->avatar)
                                        <img src="{{ asset('storage/' . $review->user->profile->avatar->object_key) }}" alt="{{ $review->user->name }}" class="rounded-circle border shadow-sm flex-shrink-0" style="width: 38px; height: 38px; object-fit: cover;">
                                    @else
                                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0d9488; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0;">
                                            {{ strtoupper(substr($review->user?->name ?? 'P', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="text-dark fw-bold" style="font-size: 14px; line-height: 1.2;">{{ $review->user?->name ?? 'Pengunjung' }}</strong>
                                        <div class="d-flex align-items-center gap-0.5 mt-0.5" style="color: #f59e0b; font-size: 11px;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <span class="text-muted" style="font-size: 11px;">{{ $review->created_at?->diffForHumans() }}</span>
                            </div>

                            <p class="text-dark mt-2.5 mb-2" style="font-size: 13px; line-height: 1.5; color: #334155;">{{ $review->body }}</p>

                            <!-- Review Photos Gallery -->
                            @if (!empty($review->photos) && is_array($review->photos))
                                <div class="d-flex flex-wrap gap-2 mt-2 mb-2.5 review-photos-gallery">
                                    @foreach ($review->photos as $photo)
                                        <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="review-photo-thumbnail rounded-3 overflow-hidden border shadow-2xs d-inline-block position-relative" style="width: 72px; height: 72px; background: #f8fafc;" title="Klik untuk perbesar foto ulasan">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Foto ulasan {{ $review->user?->name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.08)';" onmouseout="this.style.transform='scale(1)';">
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Nested Replies List -->
                            @if ($review->replies->isNotEmpty())
                                <div class="mt-3 ps-3 border-start d-flex flex-column gap-2" style="border-left: 2px solid #e2e8f0 !important; margin-left: 14px;">
                                    @foreach ($review->replies as $reply)
                                        <div class="py-1">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                    @if($reply->author?->profile?->avatar)
                                                        <img src="{{ asset('storage/' . $reply->author->profile->avatar->object_key) }}" alt="{{ $reply->author->name }}" class="rounded-circle border" style="width: 24px; height: 24px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 24px; height: 24px; font-size: 10px; background: #475569;">
                                                            {{ strtoupper(substr($reply->author?->name ?? 'P', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <strong class="text-dark fw-bold ms-1" style="font-size: 12.5px;">{{ $reply->author?->name ?? 'Pengguna' }}</strong>
                                                    @if ($reply->mitra_id)
                                                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 9.5px; font-weight: 600; padding: 2px 6px; border-radius: 4px;">Pengelola</span>
                                                    @else
                                                        <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 9.5px; font-weight: 600; padding: 2px 6px; border-radius: 4px;">Pengunjung</span>
                                                    @endif
                                                </div>
                                                <span class="text-muted" style="font-size: 10.5px;">{{ $reply->created_at?->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-dark ps-4 ms-2" style="font-size: 12.5px; line-height: 1.5; color: #1e293b;">
                                                {{ $reply->body }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Action: Reply Toggle & Form -->
                            <div class="mt-2.5 pt-1 d-flex align-items-center justify-content-between">
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-muted fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11.5px;" data-bs-toggle="collapse" data-bs-target="#replyBox-cd-{{ $review->id }}" aria-expanded="false">
                                    <i class="fa-solid fa-reply"></i>
                                    <span>Balas Ulasan ({{ $review->replies->count() }})</span>
                                </button>
                            </div>

                            <div class="collapse mt-2.5" id="replyBox-cd-{{ $review->id }}">
                                @auth
                                    <form method="POST" action="{{ route('public.reviews.replies.store', $review->id) }}" class="p-3 rounded-3 bg-white border shadow-sm" style="border-color: #e2e8f0 !important;">
                                        @csrf
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            @if(auth()->user()->profile?->avatar)
                                                <img src="{{ asset('storage/' . auth()->user()->profile->avatar->object_key) }}" alt="Avatar" class="rounded-circle border" style="width: 24px; height: 24px; object-fit: cover;">
                                            @endif
                                            <small class="fw-bold text-dark fs-8">Tulis tanggapan sebagai {{ auth()->user()->name }}:</small>
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="body" class="form-control form-control-sm rounded-3" rows="2" placeholder="Tulis tanggapan atau saran Anda..." required style="font-size: 12.5px; border-color: #cbd5e1; background: #f8fafc;"></textarea>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-light py-1 px-3 border rounded-3" style="font-size: 11.5px;" data-bs-toggle="collapse" data-bs-target="#replyBox-cd-{{ $review->id }}">Batal</button>
                                            <button type="submit" class="btn btn-sm text-white py-1 px-3 rounded-3 fw-bold" style="background: #0d9488; font-size: 11.5px; border: none;">Kirim Balasan</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="p-2.5 rounded-3 bg-light border text-center" style="font-size: 12px; border-color: #e2e8f0 !important;">
                                        <a href="{{ route('login') }}" class="text-emerald fw-bold text-decoration-none"><i class="fa-regular fa-user me-1"></i>Masuk (Login)</a> untuk menulis balasan ulasan ini.
                                    </div>
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="p-4 rounded-4 bg-white border text-center shadow-sm" style="border-color: #e2e8f0 !important;">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 44px; height: 44px; background: #f0fdf4; color: #047857; font-size: 18px;">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1 fs-7">Belum Ada Ulasan</h6>
                            <p class="text-muted small mb-0" style="font-size: 12px;">Jadilah yang pertama memberikan ulasan dan rating untuk destinasi ini.</p>
                        </div>
                    @endforelse
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
