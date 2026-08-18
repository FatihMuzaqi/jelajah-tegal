@extends('layouts.dinas')

@section('title', 'Destinasi Wisata Binaan Pemda')
@section('page-title', 'Destinasi Wisata Binaan Pemda')
@section('page-description', 'Direktori dan ringkasan performa operasional seluruh objek wisata di bawah naungan dinas pemerintah daerah Tegal.')

@section('page-actions')
    <a href="{{ route('dinas.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 fw-semibold fs-7 d-inline-flex align-items-center gap-1.5 shadow-2xs">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Kembali ke Dashboard</span>
    </a>
@endsection

@section('content')
    <!-- 1. Search Panel -->
    <div class="dinas-panel mb-4">
        <form method="GET" action="{{ route('dinas.destinations.index') }}" class="d-flex align-items-center gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm bg-light border-start-0 fs-7 py-2"
                       placeholder="Cari destinasi dinas, nama badan pengelola, atau slug URL...">
            </div>
            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 fs-7 rounded-3 shadow-sm d-inline-flex align-items-center gap-1.5">
                <i class="fa-solid fa-filter"></i>
                <span>Cari</span>
            </button>
            @if(request('q'))
                <a href="{{ route('dinas.destinations.index') }}" class="btn btn-light border text-muted px-3 py-2 rounded-3 shadow-2xs" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- 2. Destinations Directory Grid -->
    <div class="row g-4">
        @forelse($destinations as $dest)
            @php($mitra = $dest['model'])
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 rounded-4 shadow-sm h-100 bg-white overflow-hidden d-flex flex-column justify-content-between">
                    <div>
                        <!-- Card Header -->
                        <div class="p-4 pb-3 border-bottom d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="dinas-icon-box"
                                     style="width: 48px; height: 48px; min-width: 48px; border-radius: 14px; background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%); color: #ffffff; font-size: 20px;">
                                    <i class="fa-solid fa-landmark"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0.5 fs-6">{{ $mitra->display_name }}</h6>
                                    <small class="text-muted d-block fs-8">{{ $mitra->legal_name }}</small>
                                </div>
                            </div>
                            <span class="dinas-badge bg-primary-subtle text-primary border border-primary-subtle fs-8">
                                <i class="fa-solid fa-building-columns"></i> Dinas
                            </span>
                        </div>

                        <!-- Card Metrics Body -->
                        <div class="p-4 py-3">
                            <div class="row g-2 text-center mb-3">
                                <div class="col-6">
                                    <div class="p-2.5 rounded-3 bg-light border">
                                        <small class="text-muted fs-8 d-block mb-0.5">Total PAD</small>
                                        <strong class="text-success fs-7 d-block">Rp {{ number_format($dest['revenue'], 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2.5 rounded-3 bg-light border">
                                        <small class="text-muted fs-8 d-block mb-0.5">Tiket Terjual</small>
                                        <strong class="text-dark fs-7 d-block">{{ number_format($dest['total_tickets'], 0, ',', '.') }} Tiket</strong>
                                    </div>
                                </div>
                            </div>

                            <ul class="list-unstyled mb-0 fs-8 text-muted d-flex flex-column gap-2">
                                <li class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-primary" style="width: 16px; text-align: center;"></i>
                                        <span>Wilayah:</span>
                                    </span>
                                    <strong class="text-dark">{{ $mitra->region?->name ?? 'Kabupaten/Kota Tegal' }}</strong>
                                </li>
                                <li class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-user-tie text-primary" style="width: 16px; text-align: center;"></i>
                                        <span>Penanggung Jawab:</span>
                                    </span>
                                    <span class="text-dark fw-medium">{{ $mitra->owner?->name ?? '—' }}</span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-door-open text-primary" style="width: 16px; text-align: center;"></i>
                                        <span>Loket Gatekeeper:</span>
                                    </span>
                                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                        {{ $dest['active_gates'] }} Pos Loket Aktif
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="p-3 px-4 border-top bg-light d-flex justify-content-between align-items-center">
                        <a href="{{ route('public.mitra.show', $mitra->slug) }}" target="_blank" class="btn btn-sm btn-link text-decoration-none p-0 fs-8 fw-semibold text-primary d-inline-flex align-items-center gap-1">
                            <span>Halaman Publik</span>
                            <i class="fa-solid fa-arrow-up-right-from-square fs-8"></i>
                        </a>
                        <a href="{{ route('dinas.ticket-sales.index', ['mitra_id' => $mitra->id]) }}" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fs-8 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-ticket"></i>
                            <span>Lihat Tiket &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
                    <div class="dinas-icon-box bg-light text-muted mx-auto mb-3" style="width: 56px; height: 56px; min-width: 56px; font-size: 24px; border-radius: 16px;">
                        <i class="fa-solid fa-landmark"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1 fs-6">Belum Ada Destinasi Dinas</h6>
                    <p class="text-muted fs-8 mb-0">Tenant dengan kategori dinas belum terdaftar atau tidak sesuai dengan kata kunci pencarian Anda.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection
