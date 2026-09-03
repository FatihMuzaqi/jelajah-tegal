@extends('layouts.' . $surface)
@section('title', str($surface)->headline() . ' Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', $mitra ? 'Ringkasan operasional dan performa ' . $mitra->display_name . '.' : 'Ringkasan
    data aktual, metrik, dan operasional sesuai hak akses Anda.')

@section('content')
    {{-- Welcome & Quick Status Banner --}}
    <div class="card border-0 mb-4 shadow-sm"
        style="background: linear-gradient(135deg, rgba(31, 122, 92, 0.08) 0%, rgba(45, 140, 168, 0.08) 100%), var(--lokantara-surface); border-radius: 16px; border: 1px solid var(--lokantara-border);">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                        style="width: 52px; height: 52px; background: linear-gradient(135deg, var(--lokantara-primary), #175e47); font-size: 22px; flex-shrink: 0;">
                        <i class="fa-solid fa-gauge-high"></i>
                    </div>
                    <div>
                        <h2 class="h5 fw-bold mb-1 text-dark">
                            Selamat Datang, {{ auth()->user()->name }}! 
                        </h2>
                        <p class="text-muted small mb-0">
                            @if ($surface === 'mitra')
                                Mengelola tenant <strong
                                    class="text-dark">{{ $mitra?->display_name ?? 'Mitra Usaha' }}</strong> &middot; Status:
                                <span
                                    class="badge bg-success-subtle text-success">{{ str($mitra?->status ?? 'Active')->headline() }}</span>
                            @elseif($surface === 'admin')
                                Administrator Pengelola Sistem Platform Jelajah Tegal
                            @elseif($surface === 'super-admin')
                                Super Admin Governance & Security Controller
                            @elseif($surface === 'gatekeeper')
                                Petugas Validasi E-Tiket & Scanner Loket
                            @else
                                Akun Wisatawan & Pembeli Layanan
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Quick Action Shortcuts --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @if ($surface === 'mitra')
                        <a href="{{ route('mitra.orders.index') }}"
                            class="btn btn-sm btn-outline-primary rounded-pill d-inline-flex align-items-center gap-2 px-3">
                            <i class="fa-solid fa-receipt"></i>
                            <span>Pesanan Masuk</span>
                        </a>
                        <a href="{{ route('mitra.features.index') }}"
                            class="btn btn-sm btn-outline-secondary rounded-pill d-inline-flex align-items-center gap-2 px-3">
                            <i class="fa-solid fa-sliders"></i>
                            <span>Kelola Fitur</span>
                        </a>
                    @elseif($surface === 'admin')
                        <a href="{{ route('admin.mitras.index') }}"
                            class="btn btn-sm btn-outline-primary rounded-pill d-inline-flex align-items-center gap-2 px-3">
                            <i class="fa-solid fa-handshake"></i>
                            <span>Kelola Mitra</span>
                        </a>
                        <a href="{{ route('admin.features.index') }}"
                            class="btn btn-sm btn-outline-secondary rounded-pill d-inline-flex align-items-center gap-2 px-3">
                            <i class="fa-solid fa-layer-group"></i>
                            <span>Review Fitur</span>
                        </a>
                    @elseif($surface === 'gatekeeper')
                        <a href="{{ route('gatekeeper.event-tickets.index') }}"
                            class="btn btn-sm btn-success rounded-pill d-inline-flex align-items-center gap-2 px-3">
                            <i class="fa-solid fa-qrcode"></i>
                            <span>Buka Scanner Kamera</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Metric Stat Cards --}}
    <div class='stats-grid'>
        @foreach ($stats as $stat)
            @php($statLabel = $stat['label'])
            @php($statValue = $stat['value'])
            @php($statTone = $stat['tone'])
            <x-stat-card :label='$statLabel' :value='$statValue' :tone='$statTone' />
        @endforeach
    </div>

    @php($chartLabels = $chart['labels'] ?? [])
    @php($chartSeries = $chart['series'] ?? [])

    {{-- Full-Width Sales Chart & Popular Destinations for Mitra --}}
    @if ($surface === 'mitra' && isset($salesTrends))
        <div class="mb-4">
            @include('dashboards.partials.mitra-sales-chart', ['salesTrends' => $salesTrends])
        </div>
    @endif

    @if ($surface === 'mitra' && isset($popularDestinations))
        <div class="mb-4">
            @include('dashboards.partials.mitra-popular-destinations', ['popularDestinations' => $popularDestinations])
        </div>
    @endif

    {{-- Data Analytics & Timeline Activity --}}
    @if ($surface !== 'mitra')
        <div class='dashboard-grid'>
            <x-chart-card title='Distribusi Data Operasional' chart-id='surface-overview-chart' :labels='$chartLabels'
                :series='$chartSeries' />

            <x-content-card title='Aktivitas & Log Terbaru' subtitle='Data aktual yang tercatat otomatis pada sistem.'>
                @if ($activity->isEmpty())
                    <x-empty-state title='Belum ada aktivitas' description='Aktivitas baru akan tampil otomatis di area ini.'
                        compact />
                @else
                    <x-timeline>
                        @foreach ($activity as $item)
                            @php($activityTitle = $item instanceof \App\Models\DatabaseNotification ? data_get($item->data, 'title', 'Notifikasi') : str($item->event)->replace('.', ' ')->headline())
                            @php($activityDescription = $item instanceof \App\Models\DatabaseNotification ? data_get($item->data, 'message') : ($item->auditable_type ? class_basename($item->auditable_type) : null))
                            @php($activityTime = $item->created_at?->diffForHumans())
                            <x-activity-item :title='$activityTitle' :description='$activityDescription' :time='$activityTime' />
                        @endforeach
                    </x-timeline>
                @endif
            </x-content-card>
        </div>

        {{-- Surface Specific Tables for non-Mitra --}}
        @include('dashboards.tables.' . $surface)
    @else
        {{-- Side-by-Side: Anggota Mitra & Aktivitas Log --}}
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                @include('dashboards.tables.mitra')
            </div>

            <div class="col-12 col-lg-6">
                <x-content-card title='Aktivitas & Log Terbaru' subtitle='Data aktual yang tercatat otomatis pada sistem.'>
                    @if ($activity->isEmpty())
                        <x-empty-state title='Belum ada aktivitas' description='Aktivitas baru akan tampil otomatis di area ini.'
                            compact />
                    @else
                        <x-timeline>
                            @foreach ($activity as $item)
                                @php($activityTitle = $item instanceof \App\Models\DatabaseNotification ? data_get($item->data, 'title', 'Notifikasi') : str($item->event)->replace('.', ' ')->headline())
                                @php($activityDescription = $item instanceof \App\Models\DatabaseNotification ? data_get($item->data, 'message') : ($item->auditable_type ? class_basename($item->auditable_type) : null))
                                @php($activityTime = $item->created_at?->diffForHumans())
                                <x-activity-item :title='$activityTitle' :description='$activityDescription' :time='$activityTime' />
                            @endforeach
                        </x-timeline>
                    @endif
                </x-content-card>
            </div>
        </div>

        @include('dashboards.partials.mitra-operational', ['operational' => $operational])
    @endif
@endsection
