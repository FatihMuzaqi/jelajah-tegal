@extends('layouts.consumer')

@section('title', 'Rencana Liburan AI')
@section('page-title', 'Rencana Liburan AI')
@section('page-description', 'Koleksi rancangan rencana perjalanan wisata cerdas yang di-generate oleh AI dan telah terbayar.')

@section('content')
<style>
    .itinerary-card {
        background: #ffffff;
        border: 1px solid var(--lokantara-border, #e2e8f0);
        border-radius: 18px;
        overflow: hidden;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .itinerary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(4, 120, 87, 0.12);
        border-color: #047857;
    }
    .itinerary-card-header {
        background: linear-gradient(135deg, #064e3b 0%, #047857 60%, #059669 100%);
        color: #ffffff;
        padding: 20px;
        position: relative;
    }
    .itinerary-badge-status {
        font-size: 11px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 99px;
    }
    .itinerary-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        background: #f8fafc;
        padding: 6px 12px;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
        font-weight: 600;
        color: #334155;
    }
    .itinerary-timeline-preview {
        border-left: 2px dashed #cbd5e1;
        margin-left: 8px;
        padding-left: 14px;
    }
</style>

<div class="mb-4">
    <!-- Top Stats & Filter Tabs -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <!-- Filter Tabs -->
        <div class="btn-group bg-white p-1 rounded-pill border shadow-sm" role="group">
            <a href="{{ route('consumer.itineraries.index', ['status' => 'paid']) }}" 
               class="btn btn-sm rounded-pill px-3 fw-bold {{ $status === 'paid' ? 'btn-emerald text-white' : 'text-muted' }}" 
               style="{{ $status === 'paid' ? 'background: #047857;' : '' }}">
                <i class="fa-solid fa-circle-check me-1"></i> Rencana Lunas ({{ $stats['total_paid'] }})
            </a>
            <a href="{{ route('consumer.itineraries.index', ['status' => 'pending']) }}" 
               class="btn btn-sm rounded-pill px-3 fw-bold {{ $status === 'pending' ? 'btn-emerald text-white' : 'text-muted' }}"
               style="{{ $status === 'pending' ? 'background: #047857;' : '' }}">
                <i class="fa-solid fa-clock me-1"></i> Menunggu Bayar ({{ $stats['total_pending'] }})
            </a>
            <a href="{{ route('consumer.itineraries.index', ['status' => 'all']) }}" 
               class="btn btn-sm rounded-pill px-3 fw-bold {{ $status === 'all' ? 'btn-emerald text-white' : 'text-muted' }}"
               style="{{ $status === 'all' ? 'background: #047857;' : '' }}">
                Semua ({{ $stats['total_all'] }})
            </a>
        </div>

        <!-- Action Button -->
        <a href="{{ route('tour-assistant.index') }}" class="btn btn-emerald rounded-pill px-4 fw-bold text-white shadow-sm d-inline-flex align-items-center gap-2" style="background: #047857;">
            <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
            <span>Buat Rencana Baru dengan AI</span>
        </a>
    </div>

    @if($itineraries->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white my-4">
            <div class="rounded-circle bg-emerald-subtle text-emerald d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 72px; height: 72px; font-size: 32px; background: #ecfdf5; color: #047857;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Belum Ada Rencana Liburan AI</h4>
            <p class="text-muted fs-7 mx-auto mb-4" style="max-width: 500px;">
                @if($status === 'paid')
                    Anda belum memiliki rencana perjalanan AI yang berstatus lunas. Rancang liburan impian Anda dan lakukan pemesanan sekarang!
                @else
                    Belum ada riwayat rencana liburan AI yang tersimpan.
                @endif
            </p>
            <div>
                <a href="{{ route('tour-assistant.index') }}" class="btn btn-emerald rounded-pill px-4 py-2.5 fw-bold text-white shadow-sm" style="background: #047857;">
                    <i class="fa-solid fa-compass me-1"></i> Rencanakan Liburan Sekarang
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($itineraries as $inv)
                @php
                    $meta = $inv->metadata ?? [];
                    $days = $meta['days'] ?? [];
                    $startDate = $meta['start_date'] ?? $inv->created_at->format('Y-m-d');
                    $endDate = $meta['end_date'] ?? $inv->created_at->format('Y-m-d');
                    $totalDays = $meta['total_days'] ?? (count($days) ?: 1);
                    $nights = $meta['nights'] ?? max(0, $totalDays - 1);
                    $pax = $meta['pax'] ?? 1;
                    $isPaid = $inv->isPaid();
                @endphp
                <div class="col-12 col-lg-6">
                    <article class="itinerary-card">
                        <div class="itinerary-card-header">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <span class="badge bg-white text-dark rounded-pill px-2.5 py-1 fw-bold fs-8 mb-1">
                                        <i class="fa-solid fa-sparkles text-warning me-1"></i> {{ $meta['package_name'] ?? 'Paket Rekomendasi AI' }}
                                    </span>
                                    <h5 class="fw-bold text-white mb-0" style="letter-spacing: -0.3px;">
                                        {{ $meta['headline'] ?? 'Liburan Eksplorasi Tegal' }}
                                    </h5>
                                </div>
                                <div>
                                    @if($isPaid)
                                        <span class="badge bg-success bg-opacity-75 text-white border border-white border-opacity-50 itinerary-badge-status">
                                            <i class="fa-solid fa-circle-check me-1"></i> LUNAS (PAID)
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark itinerary-badge-status">
                                            <i class="fa-solid fa-clock me-1"></i> MENUNGGU
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <small class="text-white-50 fs-8">
                                Ref Invoice: <strong>#{{ $inv->invoice_number }}</strong> &middot; Dipesan {{ $inv->created_at->translatedFormat('d M Y, H:i') }}
                            </small>
                        </div>

                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <!-- Key Parameters -->
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <div class="itinerary-meta-pill">
                                    <i class="fa-solid fa-calendar-days text-success"></i>
                                    <span>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }} ({{ $totalDays }}H {{ $nights }}M)</span>
                                </div>
                                <div class="itinerary-meta-pill">
                                    <i class="fa-solid fa-users text-primary"></i>
                                    <span>{{ $pax }} Wisatawan</span>
                                </div>
                                <div class="itinerary-meta-pill">
                                    <i class="fa-solid fa-wallet text-warning"></i>
                                    <span>Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Timeline Preview List -->
                            <div class="mb-4 flex-grow-1">
                                <h6 class="fs-8 fw-bold text-uppercase text-muted mb-2">Ringkasan Rute & Aktivitas:</h6>
                                <div class="itinerary-timeline-preview">
                                    @forelse(array_slice($days, 0, 3) as $d)
                                        <div class="mb-2">
                                            <strong class="fs-7 text-dark d-block">
                                                {{ $d['title'] ?? ('Hari ke-' . ($loop->iteration)) }}
                                            </strong>
                                            <small class="text-muted fs-8">
                                                @php
                                                    $actTitles = array_map(fn($a) => $a['title'], array_slice($d['activities'] ?? [], 0, 2));
                                                @endphp
                                                {{ implode(' • ', $actTitles) }}
                                                @if(count($d['activities'] ?? []) > 2)
                                                    <span class="text-emerald fw-semibold">+{{ count($d['activities']) - 2 }} aktivitas lainnya</span>
                                                @endif
                                            </small>
                                        </div>
                                    @empty
                                        <p class="text-muted fs-8 mb-0">Rincian aktivitas terjadwal otomatis.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Bottom Action Buttons -->
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                                @if($isPaid)
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('consumer.itineraries.show', $inv->id) }}" class="btn btn-emerald btn-sm rounded-pill px-3.5 py-2 fw-bold text-white" style="background: #047857;">
                                            <i class="fa-solid fa-eye me-1"></i> Buka Timeline & Detail
                                        </a>
                                        <a href="{{ route('consumer.itineraries.pdf', $inv->id) }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 fw-bold">
                                            <i class="fa-solid fa-file-pdf text-danger me-1"></i> Cetak Tabel PDF
                                        </a>
                                    </div>
                                    <a href="{{ route('consumer.trip-navigator.index') }}" class="btn btn-light btn-sm rounded-circle" title="Buka Navigasi Rute" style="width: 36px; height: 36px; display: grid; place-items: center;">
                                        <i class="fa-solid fa-map-location-dot text-success"></i>
                                    </a>
                                @else
                                    <a href="{{ route('consumer.invoices.show', $inv->invoice_number) }}" class="btn btn-warning btn-sm rounded-pill px-3.5 py-2 fw-bold text-dark w-100 text-center">
                                        <i class="fa-solid fa-credit-card me-1"></i> Selesaikan Pembayaran (Rp {{ number_format($inv->total_amount, 0, ',', '.') }})
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $itineraries->links() }}
        </div>
    @endif
</div>
@endsection
