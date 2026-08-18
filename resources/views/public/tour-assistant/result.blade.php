@extends('layouts.public')

@section('title', 'Rekomendasi Paket Liburan - Tour Assistant Jelajah Tegal')

@section('content')
<style>
/* Tour Assistant Result Styling */
.ta-result-hero {
    background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #065f46 100%);
    color: #ffffff;
    padding: clamp(35px, 5vw, 60px) 0 clamp(45px, 6vw, 75px);
    text-align: center;
}
.ta-result-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 18px;
    border-radius: 99px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 14px;
}
.ta-package-card {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 24px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.ta-package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 36px rgba(6, 78, 59, 0.12);
}
.ta-package-card.card-optimal {
    border-color: #059669;
    box-shadow: 0 12px 35px rgba(5, 150, 105, 0.18);
    position: relative;
    z-index: 2;
}
.ta-package-header {
    padding: 24px 24px 16px;
    border-bottom: 1px solid #f1f5f9;
}
.ta-item-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 16px;
    color: #ffffff;
    flex-shrink: 0;
}
</style>

<!-- 1. Hero Header -->
<section class="ta-result-hero">
    <div class="container public-container">
        <div class="ta-result-badge">
            <i class="fa-solid fa-wand-magic-sparkles text-warning"></i> Rekomendasi Terpilih
        </div>
        <h1 class="fw-bolder text-white mb-2" style="font-size: clamp(26px, 4.5vw, 40px);">
            Pilihan Paket Liburan Anda ✨
        </h1>
        <p class="text-white-50 mx-auto mb-4" style="max-width: 650px;">
            AI telah meracik 3 alternatif itinerary terbaik berdasarkan parameter liburan Anda:
        </p>

        <!-- Summary Chips Bar -->
        <div class="d-inline-flex align-items-center justify-content-center gap-2 flex-wrap p-2 rounded-pill shadow-sm" style="background: rgba(0, 0, 0, 0.25); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);">
            <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold fs-7">
                <i class="fa-solid fa-wallet text-success me-1"></i> Budget: Rp {{ number_format($data['budget'], 0, ',', '.') }}
            </span>
            <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold fs-7">
                <i class="fa-solid fa-users text-primary me-1"></i> {{ $data['pax'] }} Wisatawan
            </span>
            <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold fs-7">
                <i class="fa-solid fa-calendar-days text-warning me-1"></i> {{ \Carbon\Carbon::parse($data['start_date'])->diffInDays(\Carbon\Carbon::parse($data['end_date'])) }} Hari Liburan
            </span>
        </div>
    </div>
</section>

<!-- 2. Main Content Cards -->
<div class="container public-container py-5" style="margin-top: -30px;">
    @if(empty($options))
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center mx-auto" style="max-width: 600px;">
            <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 64px; height: 64px; font-size: 28px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Belum Ada Kombinasi yang Pas</h4>
            <p class="text-muted small mb-4">
                AI tidak dapat menemukan kombinasi layanan yang muat dalam budget Anda saat ini. Silakan coba naikkan budget atau sesuaikan pilihan kategori Anda.
            </p>
            <div>
                <a href="{{ route('tour-assistant.index') }}" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold" style="background: #047857; border: none;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Sesuaikan Form Kembali
                </a>
            </div>
        </div>
    @else
        <div class="row g-4 justify-content-center">
            @foreach($options as $key => $option)
                @php
                    $isOptimal = ($key === 'optimal');
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="ta-package-card {{ $isOptimal ? 'card-optimal' : '' }}">
                        <!-- Featured Badge for Optimal -->
                        @if($isOptimal)
                            <div class="text-white text-center py-2 fw-bold small text-uppercase tracking-wider" 
                                 style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                                <i class="fa-solid fa-crown text-warning me-1"></i> Paling Pas & Direkomendasikan
                            </div>
                        @else
                            <div class="text-secondary text-center py-2 small fw-bold text-uppercase bg-light border-bottom">
                                Opsi Alternatif
                            </div>
                        @endif

                        <div class="ta-package-header">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="fw-extrabold text-dark mb-0">{{ $option['name'] }}</h4>
                                <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill small fw-bold">
                                    {{ count($option['items']) }} Layanan
                                </span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1 mt-3">
                                <span class="text-muted small">Total:</span>
                                <span class="fs-2 fw-bolder text-emerald" style="color: #047857;">Rp {{ number_format($option['total_cost'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Item Breakdown List -->
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="small fw-bold text-uppercase text-secondary mb-3 tracking-wider">
                                Rincian Paket Terintegrasi:
                            </div>

                            <ul class="list-unstyled mb-4 flex-grow-1">
                                @foreach($option['items'] as $item)
                                    @php
                                        $offer = $item['offer'] ?? null;
                                        $entityName = $item['name'] ?? null;
                                        if (!$entityName) {
                                            if (is_object($offer)) {
                                                $entityName = $offer->catalogEntity?->name ?? $offer->name ?? 'Layanan Mitra';
                                            } elseif (is_array($offer)) {
                                                $entityName = $offer['catalog_entity']['name'] ?? $offer['name'] ?? 'Layanan Mitra';
                                            } else {
                                                $entityName = 'Layanan Mitra';
                                            }
                                        }
                                        $offerPrice = $item['unit_price'] ?? (is_object($offer) ? ($offer->price ?? 0) : ($offer['price'] ?? 0));
                                    @endphp
                                    <li class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom border-light">
                                        <div class="ta-item-icon-box" style="background: 
                                            @if($item['type'] === 'accommodation') #3b82f6
                                            @elseif($item['type'] === 'rental') #0ea5e9
                                            @elseif($item['type'] === 'culinary') #f97316
                                            @elseif($item['type'] === 'event') #a855f7
                                            @else #10b981 @endif;">
                                            @if($item['type'] === 'accommodation')
                                                <i class="fa-solid fa-bed"></i>
                                            @elseif($item['type'] === 'rental')
                                                <i class="fa-solid fa-car"></i>
                                            @elseif($item['type'] === 'culinary')
                                                <i class="fa-solid fa-utensils"></i>
                                            @elseif($item['type'] === 'event')
                                                <i class="fa-solid fa-calendar-star"></i>
                                            @else
                                                <i class="fa-solid fa-mountain-sun"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <strong class="text-dark text-sm">{{ $entityName }}</strong>
                                                <span class="fw-bold text-dark text-sm ms-2">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                            </div>
                                            <div class="text-muted small mt-0.5">
                                                @if($item['type'] === 'accommodation')
                                                    {{ $item['quantity'] }} Kamar &times; {{ $item['days'] }} Malam
                                                @elseif($item['type'] === 'rental')
                                                    {{ $item['quantity'] }} Kendaraan &times; {{ $item['days'] }} Hari
                                                @elseif($item['type'] === 'culinary')
                                                    {{ $item['quantity'] }} Voucher Makan (@ Rp {{ number_format($offerPrice, 0, ',', '.') }})
                                                @elseif($item['type'] === 'event')
                                                    {{ $item['quantity'] }} Tiket Event Masuk
                                                @else
                                                    {{ $item['quantity'] }} Tiket Wisata Masuk
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach

                                <!-- Sisa Uang Saku atau Selisih Budget -->
                                @if(isset($option['remaining_budget']) && $option['remaining_budget'] > 0)
                                    <li class="p-3 rounded-3 mt-2" style="background: #ecfdf5; border: 1px solid #a7f3d0;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-success small d-flex align-items-center gap-1.5">
                                                <i class="fa-solid fa-wallet"></i> Sisa Uang Saku
                                            </span>
                                            <span class="fw-bolder text-success">Rp {{ number_format($option['remaining_budget'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-success small opacity-75" style="font-size: 11px;">
                                            Tersimpan aman di dompet Anda dari budget Rp {{ number_format($data['budget'], 0, ',', '.') }}
                                        </div>
                                    </li>
                                @elseif(isset($option['remaining_budget']) && $option['remaining_budget'] < 0)
                                    <li class="p-3 rounded-3 mt-2" style="background: #fffbeb; border: 1px solid #fde68a;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-warning-emphasis small d-flex align-items-center gap-1.5">
                                                <i class="fa-solid fa-circle-info"></i> Selisih Di Atas Budget
                                            </span>
                                            <span class="fw-bolder text-danger">+Rp {{ number_format(abs($option['remaining_budget']), 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-muted small" style="font-size: 11px;">
                                            Paket kelas premium dengan fasilitas ekstra
                                        </div>
                                    </li>
                                @endif
                            </ul>

                            <!-- Checkout Form -->
                            <div class="pt-2 mt-auto">
                                <form action="{{ route('tour-assistant.checkout') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="package" value="{{ json_encode($option) }}">
                                    <input type="hidden" name="start_date" value="{{ $data['start_date'] }}">
                                    <input type="hidden" name="end_date" value="{{ $data['end_date'] }}">
                                    
                                    <button type="submit" class="btn w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 {{ $isOptimal ? 'text-white' : 'btn-dark' }}"
                                            style="{{ $isOptimal ? 'background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none; box-shadow: 0 8px 20px rgba(5, 150, 105, 0.4);' : '' }}">
                                        <span>Pilih & Bayar Paket</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('tour-assistant.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Sesuaikan Budget / Tanggal
            </a>
        </div>
    @endif
</div>
@endsection
