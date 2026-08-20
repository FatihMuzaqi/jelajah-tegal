@extends('layouts.consumer')

@section('title', 'Detail Rencana Liburan AI #' . $invoice->invoice_number)
@section('page-title', 'Detail Rencana Perjalanan AI')
@section('page-description', 'Timeline kronologis jam-per-jam, e-tiket, dan panduan perjalanan liburan Anda di Tegal.')

@section('page-actions')
<div class="d-flex align-items-center gap-2 flex-wrap">
    <a href="{{ route('consumer.itineraries.pdf', $invoice->id) }}" target="_blank" class="btn btn-danger btn-sm rounded-pill px-3.5 py-2 fw-bold text-white shadow-sm d-inline-flex align-items-center gap-1.5">
        <i class="fa-solid fa-file-pdf"></i>
        <span>Cetak Tabel PDF</span>
    </a>
    <a href="{{ route('consumer.trip-navigator.index') }}" class="btn btn-warning btn-sm rounded-pill px-3.5 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="background: #facc15; color: #064e3b; border: none;">
        <i class="fa-solid fa-map-location-dot"></i>
        <span>Rute Navigasi GPS</span>
    </a>
    <a href="{{ route('consumer.itineraries.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 fw-semibold">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<style>
    .itinerary-detail-hero {
        background: linear-gradient(135deg, #064e3b 0%, #047857 55%, #059669 100%);
        border-radius: 20px;
        color: #ffffff;
        padding: 28px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .itinerary-detail-hero::after {
        content: '';
        position: absolute;
        right: -50px;
        bottom: -50px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .ai-timeline-container {
        position: relative;
        padding-left: 32px;
    }
    .ai-timeline-container::before {
        content: '';
        position: absolute;
        top: 15px;
        bottom: 15px;
        left: 11px;
        width: 2px;
        background: #cbd5e1;
    }
    .ai-timeline-card-item {
        position: relative;
        margin-bottom: 24px;
    }
    .ai-timeline-dot-circle {
        position: absolute;
        left: -32px;
        top: 6px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        z-index: 2;
    }
    .ai-day-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 6px;
    }
    .ai-day-btn {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .ai-day-btn.active, .ai-day-btn:hover {
        background: #047857;
        color: #ffffff;
        border-color: #047857;
    }
    .ticket-order-card {
        background: #ffffff;
        border: 1px solid var(--lokantara-border, #e2e8f0);
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }
    .ticket-order-card:hover {
        border-color: #047857;
        box-shadow: 0 4px 12px rgba(4, 120, 87, 0.08);
    }
</style>

@php
    $meta = $itinerary ?? [];
    $days = $meta['days'] ?? [];
    $startDate = $meta['start_date'] ?? $invoice->created_at->format('Y-m-d');
    $endDate = $meta['end_date'] ?? $invoice->created_at->format('Y-m-d');
    $totalDays = $meta['total_days'] ?? (count($days) ?: 1);
    $nights = $meta['nights'] ?? max(0, $totalDays - 1);
    $pax = $meta['pax'] ?? 1;
@endphp

<div class="mb-5">
    <!-- 1. Hero Summary Card -->
    <div class="itinerary-detail-hero shadow-sm">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 position-relative" style="z-index: 2;">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold fs-8">
                        <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> {{ $meta['package_name'] ?? 'Paket Rekomendasi AI' }}
                    </span>
                    @if($invoice->isPaid())
                        <span class="badge bg-success bg-opacity-75 text-white border border-white border-opacity-50 rounded-pill px-3 py-1 fw-bold fs-8">
                            <i class="fa-solid fa-circle-check me-1"></i> STATUS: LUNAS (PAID)
                        </span>
                    @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold fs-8">
                            <i class="fa-solid fa-clock me-1"></i> MENUNGGU PEMBAYARAN
                        </span>
                    @endif
                </div>

                <h3 class="fw-extrabold text-white mb-2" style="letter-spacing: -0.5px;">
                    {{ $meta['headline'] ?? 'Rencana Liburan Eksplorasi Tegal' }}
                </h3>

                <p class="text-white-50 mb-0 fs-7">
                    Invoice: <strong>#{{ $invoice->invoice_number }}</strong> &middot; Terbayar {{ $invoice->paid_at ? $invoice->paid_at->translatedFormat('d F Y, H:i WIB') : '-' }}
                </p>
            </div>

            <!-- Summary Chips -->
            <div class="d-flex flex-wrap gap-2">
                <div class="bg-white bg-opacity-15 backdrop-blur border border-white border-opacity-25 rounded-3 px-3 py-2 text-center text-white">
                    <small class="d-block text-white-50 fs-8 fw-semibold text-uppercase">Durasi Liburan</small>
                    <strong class="fs-6">{{ $totalDays }} Hari / {{ $nights }} Malam</strong>
                </div>
                <div class="bg-white bg-opacity-15 backdrop-blur border border-white border-opacity-25 rounded-3 px-3 py-2 text-center text-white">
                    <small class="d-block text-white-50 fs-8 fw-semibold text-uppercase">Wisatawan</small>
                    <strong class="fs-6">{{ $pax }} Orang</strong>
                </div>
                <div class="bg-white bg-opacity-15 backdrop-blur border border-white border-opacity-25 rounded-3 px-3 py-2 text-center text-white">
                    <small class="d-block text-white-50 fs-8 fw-semibold text-uppercase">Total Biaya</small>
                    <strong class="fs-6">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Main Content: Left Timeline & Right Orders -->
    <div class="row g-4">
        <!-- Sisi Kiri: Timeline Kronologis Lengkap -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-timeline text-emerald me-1.5" style="color: #047857;"></i> Timeline Jadwal Kronologis
                    </h5>
                    <span class="badge bg-light text-muted border px-2.5 py-1 fs-8">
                        {{ count($days) }} Hari Terjadwal
                    </span>
                </div>

                <!-- Day Filter Tabs -->
                <div class="ai-day-tabs mb-4 pb-2 border-bottom">
                    <button type="button" class="ai-day-btn active" onclick="filterDayDetail('all', this)">
                        <i class="fa-solid fa-calendar-day me-1"></i> Semua Hari
                    </button>
                    @foreach($days as $day)
                        <button type="button" class="ai-day-btn" onclick="filterDayDetail('{{ $day['day_number'] }}', this)">
                            Hari {{ $day['day_number'] }} ({{ \Carbon\Carbon::parse($day['date'])->translatedFormat('d M') }})
                        </button>
                    @endforeach
                </div>

                <!-- Timeline Body -->
                @forelse($days as $day)
                    <div class="day-detail-group day-detail-{{ $day['day_number'] }} mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3 bg-light p-2.5 rounded-3 border">
                            <div class="rounded-circle bg-emerald text-white d-flex align-items-center justify-content-center fw-bold fs-7" style="width: 28px; height: 28px; background: #047857;">
                                {{ $day['day_number'] }}
                            </div>
                            <div>
                                <strong class="text-dark fs-7">{{ $day['title'] }}</strong>
                                <small class="text-muted fs-8 ms-2">{{ $day['formatted_date'] }}</small>
                            </div>
                        </div>

                        <div class="ai-timeline-container">
                            @foreach($day['activities'] as $act)
                                @php
                                    $dotColor = $act['color'] ?? '#047857';
                                    $actType = $act['type'] ?? 'tourism';
                                    $categoryLabel = match($actType) {
                                        'tourism' => 'Wisata',
                                        'accommodation' => 'Penginapan',
                                        'culinary' => 'Kuliner',
                                        'rental' => 'Rental',
                                        'event' => 'Event',
                                        default => 'Aktivitas'
                                    };
                                    $itemData = $act['item'] ?? null;
                                    $estimatedCost = ($itemData && !empty($itemData['subtotal'])) ? ('Rp ' . number_format($itemData['subtotal'], 0, ',', '.')) : 'Termasuk dalam Paket';
                                    $locationName = $act['location'] ?? ($act['location_name'] ?? 'Tegal');
                                @endphp
                                <div class="ai-timeline-card-item">
                                    <div class="ai-timeline-dot-circle" style="background: {{ $dotColor }};"></div>
                                    
                                    <div class="card border rounded-3 p-3 bg-white shadow-sm">
                                        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-1.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge rounded-pill px-2.5 py-1 fw-bold fs-8 text-white" style="background: {{ $dotColor }};">
                                                    <i class="fa-regular fa-clock me-1"></i> {{ $act['time'] }}
                                                </span>
                                                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                                    {{ $categoryLabel }}
                                                </span>
                                            </div>
                                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2.5 py-0.5 fs-8 fw-bold">
                                                {{ $estimatedCost }}
                                            </span>
                                        </div>

                                        <h6 class="fw-bold text-dark mb-1 fs-6">
                                            {{ $act['title'] }}
                                        </h6>
                                        <p class="text-muted fs-7 mb-2">
                                            {{ $act['description'] }}
                                        </p>

                                        @if(!empty($locationName))
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top fs-8">
                                                <span class="text-muted">
                                                    <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $locationName }}
                                                </span>
                                                <a href="https://maps.google.com/?q={{ urlencode($locationName . ' Tegal') }}" target="_blank" class="text-decoration-none fw-bold text-emerald" style="color: #047857;">
                                                    Petunjuk Arah <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4">Jadwal kegiatan terkelola otomatis.</p>
                @endforelse
            </div>
        </div>

        <!-- Sisi Kanan: Tiket & Pesanan Layanan Mitra Terverifikasi -->
        <div class="col-12 col-lg-4">
            <!-- Voucher & E-Tiket Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-ticket text-warning me-1.5"></i> Voucher & E-Tiket Layanan
                </h5>
                <p class="text-muted fs-8 mb-3">
                    Tunjukkan QR Code e-tiket ini kepada petugas mitra saat mengunjungi lokasi atau check-in.
                </p>

                @forelse($invoice->orders as $order)
                    @foreach($order->items as $item)
                        @php
                            $tickets = $item->tickets;
                        @endphp
                        <div class="ticket-order-card">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge bg-emerald-subtle text-emerald rounded-pill px-2 py-0.5 fs-8 fw-bold" style="background: #ecfdf5; color: #047857;">
                                    {{ str($item->resource_type)->headline() }}
                                </span>
                                <span class="fw-bold text-dark fs-8">
                                    Rp {{ number_format($item->line_total, 0, ',', '.') }}
                                </span>
                            </div>

                            <strong class="fs-7 text-dark d-block mb-1">
                                {{ $item->item_name }}
                            </strong>

                            <div class="d-flex align-items-center justify-content-between fs-8 text-muted mb-2">
                                <span><i class="fa-solid fa-store text-success me-1"></i> {{ $order->mitra?->display_name ?? 'Mitra Jelajah Tegal' }}</span>
                                <span>Qty: <strong>{{ $item->quantity }}</strong></span>
                            </div>

                            @if($tickets->isNotEmpty())
                                <div class="d-flex flex-column gap-1 pt-2 border-top">
                                    @foreach($tickets as $t)
                                        <div class="d-flex align-items-center justify-content-between bg-light p-1.5 rounded-2">
                                            <span class="fs-8 fw-bold font-monospace text-dark">#{{ $t->ticket_code }}</span>
                                            <a href="{{ route('consumer.tickets.qr', $t->id) }}" target="_blank" class="btn btn-xs btn-outline-dark rounded-pill px-2 py-0.5 fs-8">
                                                <i class="fa-solid fa-qrcode me-1"></i> Buka QR
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @empty
                    <p class="text-muted fs-8 mb-0">Belum ada rincian pesanan.</p>
                @endforelse
            </div>

            <!-- Travel Assistant Quick Support -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="fa-solid fa-headset text-primary me-1.5"></i> Butuh Bantuan Perjalanan?
                </h6>
                <p class="text-muted fs-8 mb-3">
                    Pusat Informasi Pariwisata & Layanan Dukungan Jelajah Tegal siap membantu koordinasi liburan Anda.
                </p>
                <div class="d-grid gap-2">
                    @php
                        $waText = "Halo Jelajah Tegal, saya ingin bertanya seputar Rencana Liburan AI dengan Invoice #{$invoice->invoice_number} atas nama {$invoice->user?->name}.";
                        $waLink = "https://wa.me/6281234567890?text=" . urlencode($waText);
                    @endphp
                    <a href="{{ $waLink }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill fw-bold py-2">
                        <i class="fa-brands fa-whatsapp me-1"></i> Chat Dukungan WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterDayDetail(dayNumber, btn) {
    document.querySelectorAll('.ai-day-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const dayGroups = document.querySelectorAll('.day-detail-group');
    if (dayNumber === 'all') {
        dayGroups.forEach(g => g.classList.remove('d-none'));
    } else {
        dayGroups.forEach(g => {
            if (g.classList.contains('day-detail-' + dayNumber)) {
                g.classList.remove('d-none');
            } else {
                g.classList.add('d-none');
            }
        });
    }
}
</script>
@endpush
@endsection
