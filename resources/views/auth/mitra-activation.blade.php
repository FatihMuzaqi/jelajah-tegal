@extends('layouts.auth')

@section('title', 'Aktivasi Akses Mitra — Jelajah Tegal')
@section('meta-description', 'Aktivasi akun Mitra Jelajah Tegal untuk mengelola layanan wisata, penginapan, kuliner, event, dan armada rental.')
@section('robots', 'noindex,nofollow')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-container">
        <div class="auth-card-grid">
            <!-- Left Side: Branding & Mitra Portal Showcase -->
            <div class="auth-visual-side">
                <div>
                    <a href="{{ route('home') }}" class="auth-brand-logo" aria-label="Jelajah Tegal Beranda">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Jelajah Tegal" width="36" height="36" style="border-radius: 8px; object-fit: contain;">
                        <span>Jelajah Tegal</span>
                    </a>
                    
                    <h1 class="auth-hero-title">Undangan Bisnis Mitra Terverifikasi</h1>
                    <p class="auth-hero-desc">Selamat datang! Anda diundang bergabung ke dalam ekosistem bisnis <strong>{{ $invitation->mitra->display_name }}</strong> sebagai <strong>{{ str($invitation->role->name)->headline() }}</strong>.</p>
                    
                    <div class="auth-feature-list">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"><i class="fa-solid fa-store text-success"></i></div>
                            <div class="auth-feature-text">
                                <h6>Manajemen Tenant Mandiri</h6>
                                <p>Akses katalog wisata, kamar hotel, menu kuliner, tiket event, dan armada kendaraan.</p>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"><i class="fa-solid fa-chart-line text-success"></i></div>
                            <div class="auth-feature-text">
                                <h6>Buku Besar Ledger & Payout</h6>
                                <p>Pantau pencatatan transaksi seimbang real-time dan ajukan klaim penarikan saldo.</p>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"><i class="fa-solid fa-lock text-success"></i></div>
                            <div class="auth-feature-text">
                                <h6>Keamanan Akses Terisolasi</h6>
                                <p>Satu akun untuk multi-mitra dengan otorisasi berbasis peran dan izin yang aman.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="auth-visual-footer">
                    <span>&copy; {{ now()->year }} Jelajah Tegal</span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1">Undangan Resmi</span>
                </div>
            </div>

            <!-- Right Side: Activation Form Card -->
            <div class="auth-form-side">
                <!-- Mobile Brand Header -->
                <div class="d-lg-none text-center mb-3">
                    <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Jelajah Tegal" width="36" height="36" style="border-radius: 8px; object-fit: contain;">
                        <span class="fw-bold text-dark fs-6">Jelajah Tegal</span>
                    </a>
                </div>

                <div class="auth-header">
                    <h2>Aktivasi Akses Mitra</h2>
                    <p>Undangan untuk <strong>{{ $invitation->email }}</strong></p>
                </div>

                @if ($errors->any())
                    <div class="alert-custom alert-custom-danger" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($user->credential)
                    @if (auth()->id() === $user->id)
                        <div class="mb-4">
                            <p class="text-muted mb-4">Anda sedang masuk dengan akun <strong>{{ auth()->user()->email }}</strong>. Konfirmasi sekarang untuk menerima hak akses ke Mitra <strong>{{ $invitation->mitra->display_name }}</strong>.</p>
                            <form method="POST" action="{{ route('mitra.activation.store', $token) }}">
                                @csrf
                                <button type="submit" class="btn-submit-primary">Terima & Aktifkan Akses Sekarang</button>
                            </form>
                        </div>
                    @else
                        <div class="mb-4">
                            <p class="text-muted mb-4">Akun untuk email <strong>{{ $invitation->email }}</strong> sudah terdaftar. Silakan masuk terlebih dahulu untuk menerima undangan.</p>
                            <a class="btn-submit-primary d-inline-block text-center text-decoration-none" href="{{ route('login') }}">Masuk ke Akun Saya</a>
                        </div>
                    @endif
                @else
                    <form method="POST" action="{{ route('mitra.activation.store', $token) }}" id="activationForm">
                        @csrf

                        <!-- Password Input -->
                        <div class="form-input-group">
                            <label for="password">Buat Kata Sandi Baru</label>
                            <div class="form-input-wrapper">
                                <span class="form-input-icon"></span>
                                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="form-control-custom has-toggle">
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('password', this)" aria-label="Lihat kata sandi">
                                    <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password Input -->
                        <div class="form-input-group">
                            <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                            <div class="form-input-wrapper">
                                <span class="form-input-icon"><i class="fa-solid fa-key"></i></span>
                                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi baru" class="form-control-custom has-toggle">
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('password_confirmation', this)" aria-label="Lihat kata sandi">
                                    <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit-primary">
                            Aktifkan Akun & Masuk ke Dashboard
                        </button>
                    </form>
                @endif

                <!-- Footer Prompt -->
                <div class="auth-footer-prompt">
                    Sudah memiliki akun aktif?
                    <a href="{{ route('login') }}" class="forgot-password-link">Masuk ke Portal</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
    } else {
        input.type = 'password';
        btn.innerHTML = `<svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
    }
}
</script>
@endsection
