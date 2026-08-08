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
            <span class="surface-eyebrow">Akses Peran Multi-Surface</span>
            <h1 class="surface-title">Pilih Portal Surface Anda</h1>
            <p class="surface-subtitle">Akun Anda terdaftar memiliki beberapa hak akses portal. Silakan pilih surface yang ingin Anda buka saat ini:</p>
        </div>

        <!-- Surface Cards Grid Form -->
        <form method="POST" action="{{ route('surfaces.choose') }}" class="surface-form">
            @csrf

            <div class="surface-cards-grid">
                @php
                    $surfaceMetadata = [
                        'super-admin' => [
                            'title' => 'Super Admin System',
                            'description' => 'Kelola otorisasi RBAC, permission matrix, feature flags, dan konfigurasi platform.',
                            'badge' => 'Akses Penuh',
                            'tone' => 'superadmin',
                            'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>'
                        ],
                        'admin' => [
                            'title' => 'Administrator Platform',
                            'description' => 'Moderasi pengajuan catalog, persetujuan KYC mitra, klaim saldo, dan broadcast.',
                            'badge' => 'Manajemen',
                            'tone' => 'admin',
                            'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'
                        ],
                        'mitra' => [
                            'title' => 'Portal Bisnis Mitra',
                            'description' => 'Kelola produk wisata, penginapan, kuliner, stok rental, event, dan laporan keuangan.',
                            'badge' => 'Bisnis',
                            'tone' => 'mitra',
                            'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path><path d="M2 7h20"></path></svg>'
                        ],
                        'gatekeeper' => [
                            'title' => 'Gatekeeper Validator',
                            'description' => 'Validasi pemindaian QR Code tiket pelanggan di pintu masuk venue event atau destinasi.',
                            'badge' => 'Operasional',
                            'tone' => 'gatekeeper',
                            'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>'
                        ],
                        'consumer' => [
                            'title' => 'Portal Consumer',
                            'description' => 'Jelajahi destinasi, penginapan, event, transaksi pemesanan, dan ulasan favorit Anda.',
                            'badge' => 'Publik',
                            'tone' => 'consumer',
                            'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>'
                        ],
                    ];
                @endphp

                @foreach($surfaces as $surfaceKey)
                    @php
                        $meta = $surfaceMetadata[$surfaceKey] ?? [
                            'title' => str($surfaceKey)->headline().' Portal',
                            'description' => 'Akses portal khusus untuk peran '.str($surfaceKey)->headline().'.',
                            'badge' => 'Portal',
                            'tone' => 'default',
                            'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle></svg>'
                        ];
                    @endphp

                    <button type="submit" name="surface" value="{{ $surfaceKey }}" class="surface-card-btn tone-{{ $meta['tone'] }}">
                        <div class="card-top">
                            <div class="surface-icon-box">
                                {!! $meta['icon'] !!}
                            </div>
                            <span class="surface-badge">{{ $meta['badge'] }}</span>
                        </div>
                        
                        <div class="card-content">
                            <h3 class="surface-card-title">{{ $meta['title'] }}</h3>
                            <p class="surface-card-desc">{{ $meta['description'] }}</p>
                        </div>

                        <div class="card-footer-action">
                            <span>Masuk Portal</span>
                            <svg class="arrow-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </button>
                @endforeach
            </div>
        </form>

        <div class="surface-footer-note">
            <p>Lokantara Remake Platform · Terautentikasi secara aman dengan Spatie RBAC & Active Tenant Context.</p>
        </div>

    </div>
</div>
@endsection
