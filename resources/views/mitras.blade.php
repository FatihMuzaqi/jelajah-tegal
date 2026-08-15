@extends('layouts.app')

@section('title', 'Pilih Tenant Mitra Bisnis · Jelajah Tegal')

@section('content')
<div class="surface-selector-wrapper">
    <div class="surface-selector-container" style="max-width: 1080px;">
        
        <!-- Header & Profile Info -->
        <div class="surface-top-nav">
            <a href="{{ route('home') }}" class="surface-brand-logo text-decoration-none">
                <span class="surface-brand-badge">J</span>
                <span class="surface-brand-text">Jelajah Tegal</span>
            </a>

            <div class="surface-user-pill">
                <span class="user-avatar-circle">{{ str(auth()->user()->name)->substr(0,1)->upper() }}</span>
                <div class="user-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>
                        @if($isSuperAdmin)
                            <span class="badge bg-warning-subtle text-warning-emphasis">Super Admin Access</span>
                        @else
                            {{ auth()->user()->email }}
                        @endif
                    </small>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0 ms-2">
                    @csrf
                    <button type="submit" class="btn-logout-icon" title="Keluar">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Headline Hero -->
        <div class="surface-hero-section text-center mb-4">
            <span class="surface-eyebrow">
                @if($isSuperAdmin)
                    <i class="fa-solid fa-crown text-warning me-1"></i> Mode Master Switch Platform
                @else
                    Seleksi Tenant Mitra
                @endif
            </span>
            <h1 class="surface-title">
                @if($targetSurface === 'gatekeeper')
                    Pilih Lokasi Tenant untuk Scanner Gatekeeper
                @else
                    Pilih Unit Bisnis Mitra
                @endif
            </h1>
            <p class="surface-subtitle">
                @if($isSuperAdmin)
                    Sebagai <strong>Super Admin</strong>, Anda dapat memilih dan masuk ke unit bisnis mitra manapun di platform untuk menginspeksi operasional katalog, pesanan, dan scanner tiket.
                @else
                    Silakan pilih tenant mitra aktif yang ingin Anda operasionalkan saat ini:
                @endif
            </p>
        </div>

        @php
            $items = $isSuperAdmin ? $allMitras : $memberships->map(fn($m) => $m->mitra);
        @endphp

        @if($items->isEmpty())
            <div class="content-card p-5 text-center shadow-sm" style="border-radius: 20px; background: var(--lokantara-surface);">
                <x-empty-state 
                    title="Belum Ada Tenant Mitra Aktif" 
                    description="Belum ada unit bisnis mitra terdaftar dan aktif di platform saat ini." 
                />
                <div class="mt-4">
                    <a href="{{ route('surfaces.select') }}" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Pilih Surface
                    </a>
                </div>
            </div>
        @else
            <!-- Mitra Cards Grid Form -->
            <form method="POST" action="{{ route('mitra.choose') }}" class="surface-form">
                @csrf
                <input type="hidden" name="target_surface" value="{{ $targetSurface }}">

                <div class="row g-4 justify-content-center">
                    @foreach($items as $mitra)
                        @php
                            $isCurrentlyActive = session('active_mitra_id') === $mitra->id;
                            $features = $mitra->features->where('status', 'enabled');
                        @endphp

                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm surface-card-interactive {{ $isCurrentlyActive ? 'border-primary shadow' : '' }}" style="border-radius: 18px; border: 1.5px solid {{ $isCurrentlyActive ? 'var(--lokantara-primary)' : 'var(--lokantara-border)' }}; transition: all 0.25s ease;">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; background: linear-gradient(135deg, var(--lokantara-primary), #175e47); font-size: 18px;">
                                                <i class="fa-solid fa-store"></i>
                                            </div>
                                            @if($isCurrentlyActive)
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                                                    <i class="fa-solid fa-circle-check me-1"></i> Aktif Saat Ini
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted rounded-pill px-2 py-1 small">
                                                    {{ $mitra->region?->name ?? 'Tegal' }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="h5 fw-bold text-dark mb-1">{{ $mitra->display_name }}</h3>
                                        <p class="text-muted small mb-2">{{ $mitra->legal_name }}</p>
                                        
                                        <div class="small text-muted mb-3">
                                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                            {{ str($mitra->address ?? 'Wilayah Tegal')->limit(45) }}
                                        </div>

                                        {{-- Fitur Bisnis Badges --}}
                                        <div class="d-flex flex-wrap gap-1 mb-3">
                                            @forelse($features as $feat)
                                                <span class="badge bg-light text-dark border" style="font-size: 11px;">
                                                    {{ $feat->serviceType?->name ?? $feat->serviceType?->code }}
                                                </span>
                                            @empty
                                                <span class="text-muted small fst-italic">Belum ada fitur aktif</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="pt-3 border-top d-flex flex-column gap-2">
                                        <button type="submit" name="mitra_id" value="{{ $mitra->id }}" class="btn btn-lokantara w-100 rounded-pill py-2 d-flex align-items-center justify-content-center gap-2">
                                            @if($targetSurface === 'gatekeeper')
                                                <i class="fa-solid fa-qrcode"></i>
                                                <span>Masuk Scanner Gatekeeper</span>
                                            @else
                                                <i class="fa-solid fa-gauge"></i>
                                                <span>{{ $isCurrentlyActive ? 'Lanjutkan ke Dashboard' : 'Buka Dashboard Mitra' }}</span>
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        @endif

        <div class="surface-footer-note text-center mt-5 text-muted small">
            <p>Jelajah Tegal Platform &middot; Super Admin Master Tenant Switch Activated</p>
            <a href="{{ route('surfaces.select') }}" class="text-decoration-none text-primary fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Menu Surface
            </a>
        </div>

    </div>
</div>

<style>
.surface-card-interactive:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(31, 122, 92, 0.15) !important;
}
</style>
@endsection
