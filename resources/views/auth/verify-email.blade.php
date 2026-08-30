@extends('layouts.auth')

@section('title', 'Verifikasi Alamat Email — Jelajah Tegal')
@section('meta-description', 'Verifikasi alamat email Anda untuk mengaktifkan akun Jelajah Tegal dan mulai memesan tiket.')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-container">
        <div class="auth-card-grid">
            <!-- Left Side: Branding & Info -->
            <div class="auth-visual-side">
                <div>
                    <a href="{{ route('home') }}" class="auth-brand-logo" aria-label="Jelajah Tegal Beranda">
                        <span class="auth-brand-badge">J</span>
                        <span>Jelajah Tegal</span>
                    </a>

                    <h1 class="auth-hero-title">Satu Langkah Lagi Menuju Petualangan Anda!</h1>
                    <p class="auth-hero-desc">Verifikasi email diperlukan untuk melindungi transaksi, pemesanan tiket wisata, dan keamanan akun Anda.</p>

                    <div class="auth-feature-list">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">️</div>
                            <div class="auth-feature-text">
                                <h6>Keamanan Akun Terjamin</h6>
                                <p>Melindungi data pesanan tiket dan riwayat transaksi dari akses tidak sah.</p>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">️</div>
                            <div class="auth-feature-text">
                                <h6>E-Tiket Langsung Aktif</h6>
                                <p>Nikmati kemudahan pemesanan destinasi, akomodasi, dan event tanpa hambatan.</p>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"></div>
                            <div class="auth-feature-text">
                                <h6>Konfirmasi & Invoice Resmi</h6>
                                <p>Bukti pembayaran dan e-tiket QR otomatis dikirim ke kotak masuk email Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="auth-visual-footer">
                    <span>&copy; {{ now()->year }} Jelajah Tegal</span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1">Verifikasi Akun</span>
                </div>
            </div>

            <!-- Right Side: Verification Action Card -->
            <div class="auth-form-side">
                <div class="text-center mb-4">
                    <div style="width: 72px; height: 72px; border-radius: 50%; background: #f0fdf4; border: 2px solid #bbf7d0; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; color: #15803d; margin-bottom: 16px;">
                        ️
                    </div>
                    <h2 class="fw-extrabold text-dark mb-1" style="font-size: 22px;">Periksa Email Anda</h2>
                    <p class="text-muted fs-7 mb-0">
                        Terima kasih telah mendaftar di <strong>Jelajah Tegal</strong>! Tautan verifikasi telah kami kirimkan ke:
                    </p>
                    <div class="mt-2.5 p-2 px-3 rounded-pill bg-light border d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-envelope text-success fs-8"></i>
                        <strong class="text-dark fs-7 font-monospace">{{ auth()->user()?->email ?? 'email Anda' }}</strong>
                    </div>
                </div>

                @if (session('status'))
                    <div class="alert-custom alert-custom-success mb-3" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <div>
                            <strong>Berhasil!</strong> {{ session('status') }}
                        </div>
                    </div>
                @endif

                <div class="p-3 rounded-4 bg-light border mb-4 text-muted fs-8">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-circle-info text-primary mt-1"></i>
                        <div>
                            Silakan klik tautan verifikasi di dalam email tersebut. Jika email belum masuk dalam 1–2 menit, periksa folder <strong>Spam / Promosi</strong>.
                        </div>
                    </div>
                </div>

                <!-- Resend Verification Email Button -->
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-submit-primary mb-3">
                        <i class="fa-solid fa-paper-plane me-1"></i>
                        <span>Kirim Ulang Email Verifikasi</span>
                    </button>
                </form>

                <!-- Logout / Switch Account -->
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                    <a href="{{ route('home') }}" class="text-decoration-none text-muted fs-8 fw-semibold">
                        <i class="fa-solid fa-arrow-left me-1"></i> Beranda
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 text-decoration-none text-danger fs-8 fw-semibold" style="border: none; background: none;">
                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Keluar / Ganti Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
