@extends('layouts.app')

@section('content')
<div class="surface-selector-wrapper">
    <div class="surface-selector-container">
        
        <!-- Header & Profile Info -->
        <div class="surface-top-nav">
            <div class="surface-brand-logo">
                <span class="surface-brand-badge">L</span>
                <span class="surface-brand-text">Lokantara</span>
            </div>

            <div class="surface-user-pill">
                <span class="user-avatar-circle">{{ str(auth()->user()->name)->substr(0,1)->upper() }}</span>
                <div class="user-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->email }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0 ms-2">
                    @csrf
                    <button type="submit" class="btn-logout-icon" title="Keluar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Headline Hero -->
        <div class="surface-hero-section">
            <span class="surface-eyebrow">Seleksi Tenant Mitra</span>
            <h1 class="surface-title">Pilih Tenant Mitra Bisnis</h1>
            <p class="surface-subtitle">Anda terdaftar sebagai anggota pada beberapa unit bisnis mitra. Silakan pilih tenant mitra yang ingin Anda operasionalkan saat ini:</p>
        </div>

        @if($memberships->isEmpty())
            <div class="content-card p-4 text-center">
                <x-empty-state 
                    title="Belum Terdaftar di Tenant Mitra" 
                    description="Akun Anda saat ini belum terhubung dengan unit bisnis mitra aktif. Silakan hubungi Administrator Platform." 
                />
                <div class="mt-3">
                    <a href="{{ route('surfaces.select') }}" class="btn btn-outline-primary">Kembali ke Pilih Surface</a>
                </div>
            </div>
        @else
            <!-- Mitra Cards Grid Form -->
            <form method="POST" action="{{ route('mitra.choose') }}" class="surface-form">
                @csrf

                <div class="surface-cards-grid">
                    @foreach($memberships as $m)
                        @php
                            $isCurrentlyActive = session('active_mitra_id') === $m->mitra_id;
                        @endphp

                        <button type="submit" name="mitra_id" value="{{ $m->mitra_id }}" class="surface-card-btn tone-mitra {{ $isCurrentlyActive ? 'border-primary' : '' }}">
                            <div class="card-top">
                                <div class="surface-icon-box">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path><path d="M2 7h20"></path></svg>
                                </div>
                                <span class="surface-badge {{ $isCurrentlyActive ? 'bg-success text-white' : '' }}">
                                    {{ $isCurrentlyActive ? 'Tenant Aktif Saat Ini' : 'Mitra Terverifikasi' }}
                                </span>
                            </div>
                            
                            <div class="card-content">
                                <h3 class="surface-card-title">{{ $m->mitra->display_name }}</h3>
                                <p class="surface-card-desc">
                                    <strong>{{ $m->mitra->legal_name }}</strong><br>
                                    <small class="text-muted">📍 {{ $m->mitra->address ?? 'Lokasi Tegal' }}</small>
                                </p>
                            </div>

                            <div class="card-footer-action">
                                <span>{{ $isCurrentlyActive ? 'Lanjutkan ke Dashboard' : 'Pilih Tenant Ini' }}</span>
                                <svg class="arrow-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </div>
                        </button>
                    @endforeach
                </div>
            </form>
        @endif

        <div class="surface-footer-note">
            <p>Lokantara Multi-Tenant Context · Sesuai Spesifikasi Arsitektur `docs/02-target-architecture/06-multi-mitra-context.md`</p>
        </div>

    </div>
</div>
@endsection
