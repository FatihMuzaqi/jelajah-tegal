@extends('layouts.consumer')

@section('title', 'Consumer Dashboard')
@section('page-title', 'Dashboard Wisatawan')
@section('page-description', 'Pantau jadwal liburan, e-tiket, panduan rute navigasi, dan pesanan layanan wisata Anda.')

@section('content')
<style>
    /* Responsive Dashboard Container & Cards */
    .consumer-hero-card {
        background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #059669 100%);
        border-radius: 20px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .consumer-hero-card::after {
        content: '';
        position: absolute;
        right: -60px;
        bottom: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Metric Cards Grid */
    .consumer-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 991.98px) {
        .consumer-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 575.98px) {
        .consumer-stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    .stat-card-custom {
        background: #ffffff;
        border: 1px solid var(--lokantara-border, #e2e8f0);
        border-radius: 16px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }
    .stat-card-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    /* Action Banner Grid */
    .action-banners-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 991.98px) {
        .action-banners-grid {
            grid-template-columns: 1fr;
        }
    }

    .action-banner-card {
        border-radius: 16px;
        padding: 20px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 150px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-decoration: none;
    }
    .action-banner-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        color: #ffffff;
    }

    /* Main 2-Column Responsive Layout */
    .dashboard-two-col {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 20px;
    }
    @media (max-width: 991.98px) {
        .dashboard-two-col {
            grid-template-columns: 1fr;
        }
    }

    /* Responsive Table to Cards on Mobile */
    @media (max-width: 767.98px) {
        .table-responsive-cards thead {
            display: none;
        }
        .table-responsive-cards, 
        .table-responsive-cards tbody, 
        .table-responsive-cards tr, 
        .table-responsive-cards td {
            display: block;
            width: 100%;
        }
        .table-responsive-cards tr {
            margin-bottom: 12px;
            background: #ffffff;
            border: 1px solid var(--lokantara-border, #e2e8f0);
            border-radius: 12px;
            padding: 12px 14px;
        }
        .table-responsive-cards td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border: none;
            text-align: right;
            font-size: 13px;
        }
        .table-responsive-cards td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #64748b;
            text-align: left;
            padding-right: 12px;
        }
        .table-responsive-cards td:last-child {
            border-top: 1px dashed #e2e8f0;
            margin-top: 6px;
            padding-top: 10px;
        }
    }

    /* Popular Destination Item */
    .popular-dest-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border-radius: 12px;
        transition: background 0.2s ease;
        text-decoration: none;
        color: inherit;
    }
    .popular-dest-item:hover {
        background: #f8fafc;
    }
    .popular-dest-thumb {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        background: #e2e8f0;
    }
</style>

<div class="mb-4">
    <!-- 1. HERO WELCOME CARD -->
    <div class="consumer-hero-card p-4 p-md-4 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 position-relative" style="z-index: 1;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; font-size: 22px; font-weight: bold; color: #047857 !important; flex-shrink: 0;">
                    {{ str($u->name)->substr(0, 1)->upper() }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h4 class="fw-bold mb-0 text-white">{{ $u->name }}</h4>
                        <span class="badge rounded-pill bg-white text-dark fw-bold px-2.5 py-1" style="font-size: 11px;">
                            <i class="fa-solid fa-circle-check text-success me-1"></i> Wisatawan Terverifikasi
                        </span>
                    </div>
                    <p class="text-white-50 small mb-0">{{ $u->email }} &middot; Jelajah Keindahan Wisata Kabupaten & Kota Tegal</p>
                </div>
            </div>

            <!-- Quick Action Button in Header -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('consumer.trip-navigator.index') }}" class="btn btn-warning fw-bold px-3.5 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2" style="background: #facc15; color: #064e3b; border: none;">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Rute Destinasi GPS</span>
                </a>
                <a href="{{ route('tour-assistant.index') }}" class="btn btn-outline-light fw-semibold px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                    <span>AI Assistant</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. STAT METRICS GRID (4 CARDS) -->
    <div class="consumer-stats-grid">
        <div class="stat-card-custom">
            <div class="stat-icon-wrapper" style="background: #ecfdf5; color: #047857;">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <span class="text-muted d-block small fw-semibold">Total Pesanan</span>
                <h4 class="fw-bold text-dark mb-0">{{ $totalOrders }}</h4>
            </div>
        </div>

        <div class="stat-card-custom">
            <div class="stat-icon-wrapper" style="background: #eef2ff; color: #4f46e5;">
                <i class="fa-solid fa-map-pin"></i>
            </div>
            <div>
                <span class="text-muted d-block small fw-semibold">Destinasi Terbayar</span>
                <h4 class="fw-bold text-dark mb-0" style="color: #4f46e5 !important;">{{ $paidOrdersCount }}</h4>
            </div>
        </div>

        <a href="{{ route('consumer.itineraries.index') }}" class="stat-card-custom text-decoration-none">
            <div class="stat-icon-wrapper" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div>
                <span class="text-muted d-block small fw-semibold">Rencana Liburan AI</span>
                <h4 class="fw-bold text-dark mb-0">{{ $paidItinerariesCount }} <small class="text-success fs-8 fw-bold">Lunas</small></h4>
            </div>
        </a>

        <div class="stat-card-custom">
            <div class="stat-icon-wrapper" style="background: #f0fdf4; color: #16a34a;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <span class="text-muted d-block small fw-semibold">Dokumen Sewa</span>
                <h4 class="fw-bold text-dark mb-0">{{ $renterDocsCount }}</h4>
            </div>
        </div>
    </div>

    <!-- 3. ACTION BANNERS GRID -->
    <div class="action-banners-grid">
        <!-- Banner 1: Rute Destinasi GPS -->
        <a href="{{ route('consumer.trip-navigator.index') }}" class="action-banner-card shadow-sm" style="background: linear-gradient(135deg, #312e81 0%, #4338ca 100%);">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="badge bg-white text-dark fw-bold mb-2" style="font-size: 10px;">NAVIGASI LIVE</span>
                    <h5 class="fw-bold text-white mb-1">Peta & Rute Terbayar</h5>
                    <p class="text-white-50 small mb-0" style="font-size: 12px;">Panduan jalan langsung dari posisi Anda ke lokasi wisata & hotel.</p>
                </div>
                <div class="rounded-circle d-grid place-items-center text-white" style="width: 42px; height: 42px; background: rgba(255,255,255,0.2); font-size: 18px;">
                    <i class="fa-solid fa-diamond-turn-right"></i>
                </div>
            </div>
            <div class="fw-bold text-warning small mt-3 d-inline-flex align-items-center gap-1">
                Buka Peta Navigasi <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Banner 2: AI Itinerary Active / Generator -->
        <a href="{{ route('consumer.itineraries.index') }}" class="action-banner-card shadow-sm" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%);">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="badge bg-white text-dark fw-bold mb-2" style="font-size: 10px;">AI ITINERARY</span>
                    <h5 class="fw-bold text-white mb-1">Rencana Liburan AI</h5>
                    <p class="text-white-50 small mb-0" style="font-size: 12px;">Lihat jadwal jam-per-jam dan cetak dokumen PDF liburan Anda.</p>
                </div>
                <div class="rounded-circle d-grid place-items-center text-white" style="width: 42px; height: 42px; background: rgba(255,255,255,0.2); font-size: 18px;">
                    <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                </div>
            </div>
            <div class="fw-bold text-white small mt-3 d-inline-flex align-items-center gap-1">
                Buka Rencana Liburan <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Banner 3: Rental & Dokumen -->
        <a href="{{ route('consumer.renter-documents.index') }}" class="action-banner-card shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="badge bg-white text-dark fw-bold mb-2" style="font-size: 10px;">RENTAL MOBIL & MOTOR</span>
                    <h5 class="fw-bold text-white mb-1">Dokumen Sewa</h5>
                    <p class="text-white-50 small mb-0" style="font-size: 12px;">Kelola KTP, SIM, dan syarat verifikasi rental kendaraan Anda.</p>
                </div>
                <div class="rounded-circle d-grid place-items-center text-white" style="width: 42px; height: 42px; background: rgba(255,255,255,0.2); font-size: 18px;">
                    <i class="fa-solid fa-car"></i>
                </div>
            </div>
            <div class="fw-bold text-white small mt-3 d-inline-flex align-items-center gap-1">
                Kelola Dokumen <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>
    </div>

    @if($latestItinerary)
        @php
            $lMeta = $latestItinerary->metadata ?? [];
            $lDays = $lMeta['days'] ?? [];
        @endphp
        <!-- ACTIVE AI ITINERARY BANNER -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #022c22 0%, #064e3b 60%, #047857 100%); color: #ffffff;">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1.5">
                        <span class="badge bg-white text-dark rounded-pill px-2.5 py-1 fw-bold fs-8">
                            <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> RENCANA LIBURAN AKTIF
                        </span>
                        <span class="badge bg-success bg-opacity-75 text-white border border-white border-opacity-50 rounded-pill px-2.5 py-1 fw-bold fs-8">
                            <i class="fa-solid fa-circle-check me-1"></i> LUNAS
                        </span>
                    </div>
                    <h5 class="fw-bold text-white mb-1">{{ $lMeta['headline'] ?? 'Liburan Eksplorasi Tegal' }}</h5>
                    <p class="text-white-50 small mb-0">
                        {{ \Carbon\Carbon::parse($lMeta['start_date'] ?? now())->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($lMeta['end_date'] ?? now())->translatedFormat('d M Y') }} &middot; {{ $lMeta['total_days'] ?? count($lDays) }} Hari ({{ $lMeta['pax'] ?? 1 }} Orang) &middot; Ref: #{{ $latestItinerary->invoice_number }}
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('consumer.itineraries.show', $latestItinerary->id) }}" class="btn btn-warning fw-bold px-3.5 py-2 rounded-pill shadow-sm" style="background: #facc15; color: #064e3b; border: none;">
                        <i class="fa-solid fa-timeline me-1"></i> Buka Timeline Jam
                    </a>
                    <a href="{{ route('consumer.itineraries.pdf', $latestItinerary->id) }}" target="_blank" class="btn btn-outline-light fw-bold px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-file-pdf text-danger me-1"></i> Cetak Tabel PDF
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- 4. TWO-COLUMN RESPONSIVE LAYOUT -->
    <div class="dashboard-two-col">
        <!-- LEFT: PESANAN & TIKET TERBARU -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white p-3 p-md-3.5 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                    <h6 class="fw-bold mb-0 text-dark">Pesanan & E-Tiket Terbaru</h6>
                </div>
                <a href="{{ route('consumer.orders.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold small text-primary">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="p-0">
                @if($recentOrders->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-responsive-cards">
                            <thead class="table-light">
                                <tr class="text-secondary small fw-bold text-uppercase">
                                    <th class="ps-3 py-2.5">No. Pesanan</th>
                                    <th class="py-2.5">Layanan</th>
                                    <th class="py-2.5">Total</th>
                                    <th class="py-2.5 text-center">Status</th>
                                    <th class="pe-3 py-2.5 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="ps-3 py-3" data-label="No. Pesanan">
                                            <span class="fw-bold text-primary font-mono small d-block">{{ $order->order_number }}</span>
                                            <small class="text-muted" style="font-size: 11px;">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</small>
                                        </td>
                                        <td class="py-3" data-label="Layanan">
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 220px; font-size: 13px;">
                                                {{ $order->items->first()?->item_name ?? 'Item Layanan' }}
                                            </div>
                                            <small class="text-muted" style="font-size: 11px;">{{ $order->mitra?->display_name ?? 'Mitra Jelajah Tegal' }}</small>
                                        </td>
                                        <td class="py-3" data-label="Total">
                                            <strong class="text-dark" style="font-size: 13px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                        </td>
                                        <td class="py-3 text-md-center" data-label="Status">
                                            @if($order->payment_status?->value === 'paid' || $order->status?->value === 'paid' || $order->status?->value === 'completed')
                                                <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                                                    <i class="fa-solid fa-check me-1"></i> Lunas
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                                                    {{ str($order->payment_status?->value ?? $order->status?->value ?? 'Pending')->headline() }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="pe-3 py-3 text-end" data-label="Aksi">
                                            <a href="{{ route('consumer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary fw-semibold px-2.5 py-1 rounded-3" style="font-size: 12px;">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 px-3">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px;">
                            <i class="fa-solid fa-ticket text-muted fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Riwayat Pesanan</h6>
                        <p class="text-muted small mb-3">Mulai jelajahi berbagai destinasi wisata, hotel, dan kuliner khas Tegal sekarang.</p>
                        <a href="{{ route('tourism.index') }}" class="btn btn-sm btn-lokantara fw-bold px-3 py-2">
                            Jelajahi Wisata Tegal
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT: REKOMENDASI DESTINASI & PUSAT BANTUAN -->
        <div class="d-flex flex-column gap-3">
            <!-- Rekomendasi Destinasi Populer -->
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-compass text-primary"></i> Destinasi Populer
                    </h6>
                    <a href="{{ route('tourism.index') }}" class="small text-primary text-decoration-none fw-bold">Lihat Semua</a>
                </div>

                <div class="d-flex flex-column gap-2">
                    @forelse($popularTourism as $tour)
                        @php($coverMedia = $tour->media->where('pivot.role', 'cover')->first() ?? $tour->media->first())
                        @php($coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : asset('images/placeholders/tourism.jpg'))
                        
                        <a href="{{ route('tourism.show', $tour->slug) }}" class="popular-dest-item border rounded-3">
                            <img src="{{ $coverUrl }}" alt="{{ $tour->name }}" class="popular-dest-thumb" onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=120&auto=format&fit=crop&q=60'">
                            <div class="min-w-0 flex-grow-1">
                                <span class="badge bg-light text-dark border fw-semibold" style="font-size: 10px;">{{ $tour->category?->name ?? 'Wisata' }}</span>
                                <strong class="d-block text-dark text-truncate mb-0" style="font-size: 13px;">{{ $tour->name }}</strong>
                                <small class="text-muted d-block text-truncate" style="font-size: 11px;"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $tour->region?->name ?? 'Tegal' }}</small>
                            </div>
                            <i class="fa-solid fa-chevron-right text-muted small me-1"></i>
                        </a>
                    @empty
                        <p class="text-muted small text-center py-2">Belum ada destinasi ditampilkan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Pusat Bantuan & Kontak Wisata -->
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-grid place-items-center text-white" style="width: 44px; height: 44px; background: #047857; font-size: 20px; flex-shrink: 0;">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Butuh Bantuan Perjalanan?</h6>
                        <p class="text-muted small mb-0">Hubungi layanan panduan atau tanyakan ke AI Assistant kami 24/7.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mt-3">
                    <a href="{{ route('public.faq') }}" class="btn btn-sm btn-outline-secondary rounded-3 w-50 fw-semibold">
                        <i class="fa-solid fa-circle-question me-1"></i> FAQ
                    </a>
                    <a href="{{ route('public.contact') }}" class="btn btn-sm btn-outline-primary rounded-3 w-50 fw-semibold">
                        <i class="fa-solid fa-envelope me-1"></i> Kontak
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
