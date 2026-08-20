@extends('layouts.public')

@section('title', 'Hasil Rencana Liburan AI - Tour Assistant Jelajah Tegal')
@section('meta-description', 'Rencana perjalanan liburan terstruktur per hari dan per jam yang dirancang cerdas oleh AI Jelajah Tegal.')

@section('content')
<style>
@media screen {
    .print-table-document {
        display: none !important;
    }
}

@media print {
    .public-header, 
    .public-footer, 
    .ai-result-hero, 
    .btn-print-hide, 
    .web-interactive-view, 
    .ai-pkg-nav,
    .skip-link, 
    #floating-alert-toast, 
    .chatbot-floating-widget {
        display: none !important;
    }

    body, html {
        background: #ffffff !important;
        color: #0f172a !important;
        font-size: 11px !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .container, .public-container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .package-content-pane.d-none {
        display: none !important;
    }

    .print-table-document {
        display: block !important;
        width: 100% !important;
        padding: 10px 15px !important;
    }

    .print-doc-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border-bottom: 2px solid #047857;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }

    .print-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .print-brand img {
        width: 44px;
        height: 44px;
        object-fit: contain;
    }

    .print-brand-title {
        font-size: 18px;
        font-weight: 800;
        color: #064e3b;
        line-height: 1.1;
    }

    .print-brand-sub {
        font-size: 10px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }

    .print-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 18px;
    }

    .print-summary-grid small {
        display: block;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 2px;
    }

    .print-summary-grid strong {
        font-size: 11.5px;
        font-weight: 700;
        color: #0f172a;
    }

    .print-section-title {
        font-size: 12px;
        font-weight: 800;
        color: #064e3b;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 8px;
        padding-bottom: 3px;
        border-bottom: 1.5px solid #cbd5e1;
    }

    .print-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 10.5px;
    }

    .print-table th {
        background: #0f172a !important;
        color: #ffffff !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 9.5px;
        padding: 6px 8px;
        border: 1px solid #0f172a;
        text-align: left;
    }

    .print-table td {
        padding: 6px 8px;
        border: 1px solid #cbd5e1;
        color: #1e293b;
        vertical-align: middle;
    }

    .print-table tr.day-row {
        background: #e2e8f0 !important;
        font-weight: 800;
    }

    .print-table tr.day-row td {
        padding: 5px 8px;
        font-size: 10px;
        text-transform: uppercase;
        border-top: 1.5px solid #94a3b8;
        border-bottom: 1.5px solid #94a3b8;
    }

    .print-footer {
        border-top: 1px solid #cbd5e1;
        padding-top: 12px;
        font-size: 9.5px;
        color: #64748b;
        line-height: 1.4;
    }
}
</style>

<!-- 1. Hero Header -->
<section class="ai-result-hero btn-print-hide">
    <div class="container public-container text-center">
        <div class="ai-result-badge">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Rencana Liburan Teroptimalisasi
        </div>
        <h1 class="fw-bolder text-white mb-2" style="font-size: clamp(26px, 4.5vw, 42px); letter-spacing: -0.5px;">
            Rencana Perjalanan Impian Anda di Tegal ✨
        </h1>
        <p class="text-white-50 mx-auto mb-4 fs-6" style="max-width: 650px;">
            AI telah menyusun jadwal kronologis aktivitas, estimasi jam, destinasi, dan optimasi biaya sesuai preferensi Anda.
        </p>

        <!-- Parameter Chips Bar -->
        <div class="d-inline-flex align-items-center justify-content-center gap-2 flex-wrap p-2 rounded-pill shadow-sm" 
             style="background: rgba(0, 0, 0, 0.35); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);">
            <span class="badge bg-white text-dark rounded-pill px-3.5 py-2 fw-bold fs-7">
                <i class="fa-solid fa-calendar-days text-success me-1"></i> 
                {{ \Carbon\Carbon::parse($data['start_date'])->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($data['end_date'])->translatedFormat('d M Y') }}
                ({{ \Carbon\Carbon::parse($data['start_date'])->diffInDays(\Carbon\Carbon::parse($data['end_date'])) + 1 }} Hari)
            </span>
            <span class="badge bg-white text-dark rounded-pill px-3.5 py-2 fw-bold fs-7">
                <i class="fa-solid fa-wallet text-primary me-1"></i> Budget: Rp {{ number_format($data['budget'], 0, ',', '.') }}
            </span>
            <span class="badge bg-white text-dark rounded-pill px-3.5 py-2 fw-bold fs-7">
                <i class="fa-solid fa-users text-warning me-1"></i> {{ $data['pax'] }} Wisatawan
            </span>
            <span class="badge bg-emerald-subtle text-emerald rounded-pill px-3 py-2 fw-bold fs-7" style="background: rgba(52, 211, 153, 0.2); color: #34d399;">
                <i class="fa-solid fa-sparkles me-1"></i> Teroptimalisasi ✨
            </span>
        </div>
    </div>
</section>

<!-- 2. Main Itinerary & Checkout Container -->
<div class="container public-container py-5" style="margin-top: -30px;">
    @if(empty($options))
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center mx-auto" style="max-width: 600px;">
            <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 64px; height: 64px; font-size: 28px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Belum Ada Kombinasi yang Pas</h4>
            <p class="text-muted fs-7 mb-4">
                Kombinasi budget dan preferensi Anda belum mencukupi untuk paket liburan lengkap. Silakan sesuaikan budget atau durasi perjalanan Anda.
            </p>
            <a href="{{ route('tour-assistant.index') }}" class="btn btn-lokantara rounded-pill px-4 py-2 fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Ubah Preferensi Liburan
            </a>
        </div>
    @else
        <!-- Package Switcher Tabs (Hemat, Pas Budget/Rekomendasi Utama, Premium) -->
        <div class="text-center mb-4 btn-print-hide">
            <div class="ai-pkg-nav">
                @if(isset($options['optimal']))
                    <button type="button" class="ai-pkg-btn active" onclick="switchPackage('optimal', this)">
                        <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                        <span>Paket Pas Budget (Rekomendasi AI)</span>
                    </button>
                @endif
                @if(isset($options['economy']))
                    <button type="button" class="ai-pkg-btn {{ !isset($options['optimal']) ? 'active' : '' }}" onclick="switchPackage('economy', this)">
                        <i class="fa-solid fa-piggy-bank text-success"></i>
                        <span>Paket Hemat</span>
                    </button>
                @endif
                @if(isset($options['premium']))
                    <button type="button" class="ai-pkg-btn" onclick="switchPackage('premium', this)">
                        <i class="fa-solid fa-crown text-warning"></i>
                        <span>Paket Premium</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Render Package Contents -->
        @foreach($options as $pkgKey => $pkg)
            <?php $isActivePackage = ($pkgKey === 'optimal' || (!isset($options['optimal']) && $loop->first)); ?>
            <div class="package-content-pane {{ $isActivePackage ? '' : 'd-none' }}" id="package-pane-{{ $pkgKey }}">
                
                <!-- 1. TAMPILAN INTERAKTIF WEB -->
                <div class="web-interactive-view">
                    <div class="row g-4">
                        <!-- Sisi Kiri: Itinerary Timeline Kronologis -->
                        <div class="col-12 col-lg-8">
                            <!-- Card Header Itinerary -->
                            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold fs-8 mb-1.5">
                                        RENCANA AI #0{{ substr(md5($pkg['name']), 0, 3) }}
                                    </span>
                                    <h3 class="fw-extrabold text-dark mb-1" style="letter-spacing: -0.5px;">
                                        {{ $pkg['headline'] }}
                                    </h3>
                                    <p class="text-muted fs-7 mb-0">
                                        Rancangan liburan selama {{ $pkg['total_days'] }} Hari ({{ $pkg['nights'] }} Malam) untuk {{ $data['pax'] }} Orang
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-dark text-white rounded-pill px-3 py-1.5 fw-bold fs-7">
                                        {{ $pkg['total_days'] }} Hari Liburan
                                    </span>
                                </div>
                            </div>

                            <!-- Day Tabs Filter -->
                            <div class="mt-3 pt-3 border-top btn-print-hide">
                                <div class="ai-day-tabs" id="day-tabs-{{ $pkgKey }}">
                                    <button type="button" class="ai-day-btn active" onclick="filterDay('all', '{{ $pkgKey }}', this)">
                                        <i class="fa-solid fa-calendar-day me-1"></i> Semua Hari (Full Timeline)
                                    </button>
                                    @foreach($pkg['days'] as $day)
                                        <button type="button" class="ai-day-btn" onclick="filterDay('{{ $day['day_number'] }}', '{{ $pkgKey }}', this)">
                                            Hari {{ $day['day_number'] }} ({{ \Carbon\Carbon::parse($day['date'])->translatedFormat('d M') }})
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Schedule Body -->
                        @foreach($pkg['days'] as $day)
                            <div class="day-group-wrap day-group-{{ $pkgKey }}-{{ $day['day_number'] }} mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold fs-7" style="width: 32px; height: 32px;">
                                        {{ $day['day_number'] }}
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0 fs-6">{{ $day['title'] }}</h5>
                                        <small class="text-muted fs-8">{{ $day['formatted_date'] }}</small>
                                    </div>
                                </div>

                                <div class="ai-timeline-container">
                                    @foreach($day['activities'] as $act)
                                        <?php
                                            $dotColor = $act['color'] ?? '#3b82f6';
                                            $itemData = $act['item'] ?? null;
                                            $offer = $itemData['offer'] ?? null;
                                            $entity = $offer ? $offer->catalogEntity : null;
                                            $mediaUrl = ($entity && $entity->media && $entity->media->first()) ? $entity->media->first()->file_url : null;
                                        ?>
                                        <div class="ai-timeline-card-item">
                                            <div class="ai-timeline-dot-circle" style="background: {{ $dotColor }};"></div>
                                            
                                            <div class="ai-time-pill">
                                                <i class="fa-regular fa-clock" style="color: {{ $dotColor }};"></i>
                                                <span>HARI {{ $day['day_number'] }} — {{ $act['time'] }}</span>
                                            </div>

                                            <div class="ai-activity-card">
                                                <div class="row align-items-start g-3">
                                                    @if($mediaUrl)
                                                        <div class="col-12 col-sm-3 col-md-3">
                                                            <img src="{{ $mediaUrl }}" alt="{{ $act['title'] }}" 
                                                                 class="w-100 rounded-3 object-fit-cover shadow-2xs" 
                                                                 style="height: 95px;"
                                                                 onerror="this.style.display='none'">
                                                        </div>
                                                    @endif

                                                    <div class="col">
                                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-1 mb-1">
                                                            <h5 class="fw-bold text-dark mb-0 fs-6">
                                                                {{ $act['title'] }}
                                                            </h5>
                                                            @if($act['type'] === 'accommodation')
                                                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-0.5 fs-9 fw-bold">Penginapan</span>
                                                            @elseif($act['type'] === 'tourism')
                                                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-9 fw-bold">Wisata Alam</span>
                                                            @elseif($act['type'] === 'culinary')
                                                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-0.5 fs-9 fw-bold">Kuliner Khas</span>
                                                            @elseif($act['type'] === 'rental')
                                                                <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-0.5 fs-9 fw-bold">Rental Mobil</span>
                                                            @elseif($act['type'] === 'event')
                                                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-0.5 fs-9 fw-bold">Event & Seni</span>
                                                            @endif
                                                        </div>

                                                        <p class="text-muted fs-7 mb-2" style="line-height: 1.5;">
                                                            {{ $act['description'] }}
                                                        </p>

                                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top border-light">
                                                            <small class="text-muted fs-8">
                                                                <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $act['location'] }}
                                                            </small>

                                                            @if($itemData)
                                                                <span class="text-dark fw-bold fs-8 font-monospace">
                                                                    Rp {{ number_format($itemData['unit_price'], 0, ',', '.') }} 
                                                                    <span class="text-muted fw-normal">× {{ $itemData['quantity'] }} {{ $act['type'] === 'accommodation' ? 'kamar' : ($act['type'] === 'rental' ? 'unit' : 'org') }}</span>
                                                                    @if($itemData['days'] > 1)
                                                                        <span class="text-muted fw-normal">({{ $itemData['days'] }} hr)</span>
                                                                    @endif
                                                                    = <strong class="text-success">Rp {{ number_format($itemData['subtotal'], 0, ',', '.') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Sisi Kanan: Ringkasan Biaya & 1-Click Checkout Card -->
                    <div class="col-12 col-lg-4">
                        <div class="ai-summary-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark mb-0 fs-6">
                                    <i class="fa-solid fa-receipt text-success me-1"></i> Ringkasan Paket Liburan
                                </h5>
                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fs-9 fw-bold">
                                    Ready to Book
                                </span>
                            </div>

                            <div class="p-3 rounded-4 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted fs-7">Total Biaya Paket:</span>
                                    <h4 class="fw-extrabold text-success mb-0 fs-4">
                                        Rp {{ number_format($pkg['total_cost'], 0, ',', '.') }}
                                    </h4>
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-8 pt-2 border-top border-light">
                                    <span class="text-muted">Estimasi Biaya per Orang:</span>
                                    <strong class="text-dark">Rp {{ number_format($pkg['cost_per_pax'], 0, ',', '.') }} / pax</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-8 pt-1">
                                    <span class="text-muted">Sisa Anggaran Budget:</span>
                                    <span class="text-secondary fw-semibold">Rp {{ number_format($pkg['remaining_budget'], 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- List Item Rencana -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-dark fs-8 mb-2">Item Layanan Termasuk:</h6>
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                    @foreach($pkg['items'] as $it)
                                        <li class="d-flex justify-content-between align-items-center fs-8">
                                            <span class="text-truncate me-2 text-dark" style="max-width: 190px;">
                                                <i class="fa-solid fa-check text-success me-1"></i>
                                                {{ $it['offer']->catalogEntity->name ?? 'Layanan' }}
                                            </span>
                                            <strong class="text-dark font-monospace">Rp {{ number_format($it['subtotal'], 0, ',', '.') }}</strong>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Form Checkout 1-Click Booking -->
                            <form action="{{ route('tour-assistant.checkout') }}" method="POST" class="mb-3">
                                @csrf
                                <input type="hidden" name="package" value="{{ json_encode($pkg) }}">
                                <input type="hidden" name="start_date" value="{{ $data['start_date'] }}">
                                <input type="hidden" name="end_date" value="{{ $data['end_date'] }}">
                                <input type="hidden" name="pax" value="{{ $data['pax'] }}">

                                <button type="submit" class="btn btn-lokantara btn-lg w-100 rounded-pill py-3 fw-bold fs-7 shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <span>Pesan Rencana Ini (1-Click)</span>
                                </button>
                            </form>

                            <!-- Utility Buttons -->
                            <div class="d-flex gap-2 mb-2 btn-print-hide">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill w-100 py-2 fs-8 fw-semibold" onclick="window.print()">
                                    <i class="fa-solid fa-print me-1"></i> Simpan PDF
                                </button>
                                <a href="https://api.whatsapp.com/send?text={{ urlencode('Halo! Ini rencana liburan saya di Tegal: ' . $pkg['headline'] . ' untuk ' . $pkg['total_days'] . ' hari dengan total budget Rp ' . number_format($pkg['total_cost'], 0, ',', '.') . '. Selengkapnya: ' . url()->current()) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-success rounded-pill w-100 py-2 fs-8 fw-semibold">
                                    <i class="fa-brands fa-whatsapp me-1"></i> Share WA
                                </a>
                            </div>

                            <a href="{{ route('tour-assistant.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none w-100 text-center fs-8 btn-print-hide">
                                <i class="fa-solid fa-rotate-left me-1"></i> Atur Ulang Rencana Liburan
                            </a>
                        </div>
                    </div>
                </div>
                </div><!-- End .web-interactive-view -->

                <!-- 2. DOKUMEN CETAK PDF FORMAT TABEL RESMI -->
                <div class="print-table-document">
                    <!-- Kop Dokumen Resmi -->
                    <div class="print-doc-header">
                        <div class="print-brand">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Jelajah Tegal">
                            <div>
                                <h1 class="print-brand-title">JELAJAH TEGAL</h1>
                                <p class="print-brand-sub">Platform Digital Terpadu Pariwisata & Ekonomi Kreatif Tegal</p>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="display: inline-block; background: #ecfdf5; color: #047857; font-weight: 800; font-size: 10px; padding: 3px 10px; border-radius: 99px; text-transform: uppercase; border: 1px solid rgba(4,120,87,0.2);">
                                Dokumen Rencana Liburan AI
                            </span>
                            <div style="font-size: 12px; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ $pkg['name'] }}</div>
                            <div style="font-size: 10px; color: #64748b;">Tema: {{ $pkg['headline'] }}</div>
                        </div>
                    </div>

                    <!-- Summary Grid Parameter Wisatawan -->
                    <div class="print-summary-grid">
                        <div>
                            <small>Periode Liburan</small>
                            <strong>{{ \Carbon\Carbon::parse($data['start_date'])->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($data['end_date'])->translatedFormat('d M Y') }}</strong>
                        </div>
                        <div>
                            <small>Durasi & Jumlah Wisatawan</small>
                            <strong>{{ $pkg['total_days'] }} Hari / {{ $pkg['nights'] }} Malam ({{ $data['pax'] }} Orang)</strong>
                        </div>
                        <div>
                            <small>Total Estimasi Biaya</small>
                            <strong style="color: #047857;">Rp {{ number_format($pkg['total_cost'], 0, ',', '.') }}</strong>
                        </div>
                        <div>
                            <small>Estimasi / Wisatawan</small>
                            <strong>Rp {{ number_format($pkg['cost_per_pax'], 0, ',', '.') }} / pax</strong>
                        </div>
                    </div>

                    <!-- TABEL 1: JADWAL KRONOLOGIS LIBURAN (HARI & JAM) -->
                    <div class="print-section-title">
                        <i class="fa-solid fa-calendar-days text-success me-1"></i> Tabel 1: Jadwal & Rencana Perjalanan Lengkap (Hari & Jam)
                    </div>

                    <table class="print-table">
                        <thead>
                            <tr>
                                <th style="width: 30px; text-align: center;">No</th>
                                <th style="width: 80px;">Waktu (WIB)</th>
                                <th style="width: 85px;">Kategori</th>
                                <th>Destinasi & Rincian Agenda Kegiatan</th>
                                <th style="width: 140px;">Lokasi / Wilayah</th>
                                <th style="width: 100px; text-align: right;">Estimasi Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rowNo = 1; @endphp
                            @foreach($pkg['days'] as $day)
                                <tr class="day-row">
                                    <td colspan="6">
                                        <strong>{{ $day['title'] }}</strong> &mdash; {{ $day['formatted_date'] }}
                                    </td>
                                </tr>
                                @foreach($day['activities'] as $act)
                                    @php
                                        $actType = $act['type'] ?? 'tourism';
                                        $catLabel = match($actType) {
                                            'tourism' => 'Wisata',
                                            'accommodation' => 'Penginapan',
                                            'culinary' => 'Kuliner',
                                            'rental' => 'Rental',
                                            'event' => 'Event',
                                            default => 'Aktivitas'
                                        };
                                        $itemData = $act['item'] ?? null;
                                        $costText = ($itemData && !empty($itemData['subtotal'])) ? ('Rp ' . number_format($itemData['subtotal'], 0, ',', '.')) : 'Termasuk Paket';
                                        $locName = $act['location'] ?? ($act['location_name'] ?? 'Kab./Kota Tegal');
                                    @endphp
                                    <tr>
                                        <td style="text-align: center; color: #64748b; font-weight: 600;">
                                            {{ $rowNo++ }}
                                        </td>
                                        <td style="font-family: monospace; font-weight: 700; color: #064e3b;">
                                            {{ $act['time'] }}
                                        </td>
                                        <td>
                                            <span style="background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-size: 9.5px; font-weight: 700;">
                                                {{ $catLabel }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong style="color: #0f172a; display: block; margin-bottom: 2px;">{{ $act['title'] }}</strong>
                                            <span style="font-size: 10px; color: #64748b;">{{ $act['description'] }}</span>
                                        </td>
                                        <td style="font-size: 10px; font-weight: 600; color: #334155;">
                                            <i class="fa-solid fa-location-dot" style="color: #ef4444; font-size: 9px; margin-right: 2px;"></i>
                                            {{ $locName }}
                                        </td>
                                        <td style="text-align: right; font-weight: 700; white-space: nowrap;">
                                            {{ $costText }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>

                    <!-- TABEL 2: RINCIAN ESTIMASI BIAYA & LAYANAN TERPILIH -->
                    <div class="print-section-title" style="margin-top: 15px;">
                        <i class="fa-solid fa-receipt text-success me-1"></i> Tabel 2: Rincian Estimasi Biaya & Layanan Terpilih
                    </div>

                    <table class="print-table">
                        <thead>
                            <tr>
                                <th style="width: 30px; text-align: center;">No</th>
                                <th style="width: 110px;">Kategori Layanan</th>
                                <th>Item Layanan / Kamar / Destinasi</th>
                                <th style="width: 60px; text-align: center;">Qty</th>
                                <th style="width: 100px; text-align: right;">Harga Satuan</th>
                                <th style="width: 110px; text-align: right;">Subtotal Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $itemNo = 1; @endphp
                            @foreach($pkg['items'] as $it)
                                <tr>
                                    <td style="text-align: center; color: #64748b; font-weight: 600;">
                                        {{ $itemNo++ }}
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; color: #047857;">{{ str($it['type'])->headline() }}</span>
                                    </td>
                                    <td>
                                        <strong style="color: #0f172a;">{{ $it['offer']->catalogEntity->name ?? 'Layanan' }}</strong>
                                        <div style="font-size: 9.5px; color: #64748b;">{{ $it['offer']->name ?? '' }}</div>
                                    </td>
                                    <td style="text-align: center; font-weight: 700;">
                                        {{ $it['quantity'] }} {{ $it['type'] === 'accommodation' ? 'kamar' : ($it['type'] === 'rental' ? 'unit' : 'org') }}
                                        @if(($it['days'] ?? 1) > 1)
                                            <div style="font-size: 9px; color: #64748b;">({{ $it['days'] }} hari)</div>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        Rp {{ number_format($it['unit_price'], 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #064e3b;">
                                        Rp {{ number_format($it['subtotal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background: #f1f5f9; font-weight: 800; font-size: 11.5px;">
                                <td colspan="5" style="text-align: right; padding: 7px 9px; text-transform: uppercase;">
                                    TOTAL ESTIMASI BIAYA PAKET:
                                </td>
                                <td style="text-align: right; padding: 7px 9px; color: #047857; font-size: 12px;">
                                    Rp {{ number_format($pkg['total_cost'], 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Footer Notes -->
                    <div class="print-footer">
                        <strong style="color: #0f172a; display: block; margin-bottom: 2px;">Catatan & Panduan Wisata:</strong>
                        <ul style="padding-left: 14px; margin-bottom: 6px;">
                            <li>Rencana perjalanan ini disusun otomatis secara cerdas oleh AI Jelajah Tegal berdasarkan optimasi budget dan preferensi Anda.</li>
                            <li>Estimasi jam dan durasi kegiatan dapat disesuaikan fleksibel dengan kondisi lalu lintas dan operasional di lokasi.</li>
                            <li>Lakukan pemesanan resmi melalui platform Jelajah Tegal untuk mendapatkan tiket terverifikasi dan reservasi kamar instan.</li>
                        </ul>
                        <div style="font-size: 9px; color: #94a3b8;">
                            Dicetak dari Platform Resmi Jelajah Tegal pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@push('scripts')
<script>
function switchPackage(key, btn) {
    document.querySelectorAll('.ai-pkg-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.package-content-pane').forEach(pane => pane.classList.add('d-none'));
    const targetPane = document.getElementById('package-pane-' + key);
    if (targetPane) {
        targetPane.classList.remove('d-none');
    }
}

function filterDay(dayNumber, pkgKey, btn) {
    const tabContainer = document.getElementById('day-tabs-' + pkgKey);
    if (tabContainer) {
        tabContainer.querySelectorAll('.ai-day-btn').forEach(b => b.classList.remove('active'));
    }
    btn.classList.add('active');

    const pane = document.getElementById('package-pane-' + pkgKey);
    if (!pane) return;

    const dayGroups = pane.querySelectorAll('.day-group-wrap');
    if (dayNumber === 'all') {
        dayGroups.forEach(g => g.classList.remove('d-none'));
    } else {
        dayGroups.forEach(g => {
            if (g.classList.contains('day-group-' + pkgKey + '-' + dayNumber)) {
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
