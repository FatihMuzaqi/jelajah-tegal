@extends('layouts.auth')

@section('title', 'Masuk Akun — Lokantara')
@section('meta-description',
    'Masuk ke portal Lokantara untuk mengelola layanan wisata, akomodasi, kuliner, event, dan
    transaksi lokal.')

@section('content')
    <div class="auth-page-wrapper">
        <div class="auth-container">
            <div class="auth-card-grid">
                <!-- Left Side: Branding & Platform Features Showcase -->
                <div class="auth-visual-side">
                    <div>
                        <a href="{{ route('home') }}" class="auth-brand-logo" aria-label="Lokantara Beranda">
                            <span class="auth-brand-badge">J</span>
                            <span>Jelajah Tegal</span>
                        </a>
                        <h1 class="auth-hero-title">Jelajahi Wisata & Potensi Lokal</h1>
                        <p class="auth-hero-desc">Satu platform terintegrasi untuk destinasi wisata, akomodasi, kuliner,
                            acara, dan rental lokal terverifikasi.</p>
                        <div class="auth-feature-list">
                            <div class="auth-feature-item">
                                <div class="auth-feature-icon">🛡️</div>
                                <div class="auth-feature-text">
                                    <h6>Keamanan & Privasi Tingkat Tinggi</h6>
                                    <p>Dilindungi otorisasi Spatie RBAC, TOTP MFA, dan penyimpanan KYC private storage.</p>
                                </div>
                            </div>
                            <div class="auth-feature-item">
                                <div class="auth-feature-icon">💳</div>
                                <div class="auth-feature-text">
                                    <h6>Transaksi & Ledger Terpercaya</h6>
                                    <p>Idempotensi checkout, double-entry ledger seimbang, dan Midtrans Snap gateway.</p>
                                </div>
                            </div>
                            <div class="auth-feature-item">
                                <div class="auth-feature-icon">🎟️</div>
                                <div class="auth-feature-text">
                                    <h6>Tiket QR Single-Use Atomik</h6>
                                    <p>Fulfillment presisi dengan verifikasi Gatekeeper dan perlindungan duplicate-scan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="auth-visual-footer">
                        <span>&copy; {{ now()->year }} Jelajah Tegal</span>
                        <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1">100% Real
                            Database</span>
                    </div>
                </div>

                <!-- Right Side: Login Form Card -->
                <div class="auth-form-side">
                    <!-- Mobile Top Brand Logo -->
                    <div class="auth-mobile-brand d-lg-none text-center mb-3">
                        <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                            <img src="{{ asset('images/icon-192.png') }}" alt="Logo Jelajah Tegal" style="height: 38px; width: 38px; border-radius: 10px; box-shadow: 0 4px 10px rgba(21,128,61,0.2);">
                            <span class="fw-bold text-dark fs-5" style="letter-spacing: -0.02em;">Jelajah Tegal</span>
                        </a>
                    </div>

                    <div class="auth-header">
                        <h2>Masuk ke Akun</h2>
                        <p>Silakan masukkan email & kata sandi akun Anda.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert-custom alert-custom-success" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-check-circle">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert-custom alert-custom-danger" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-alert-triangle">
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                                </path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert-custom alert-custom-danger" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-alert-triangle">
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                                </path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Email Input -->
                        <div class="form-input-group">
                            <label for="email">Alamat Email</label>
                            <div class="form-input-wrapper">
                                <span class="form-input-icon">✉️</span>
                                <input id="email" name="email" type="email"
                                    class="form-control-custom @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="contoh: nama@domain.com" required autofocus
                                    autocomplete="email">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="form-input-group">
                            <label for="password">Kata Sandi</label>
                            <div class="form-input-wrapper">
                                <span class="form-input-icon">🔒</span>
                                <input id="password" name="password" type="password"
                                    class="form-control-custom has-toggle @error('password') is-invalid @enderror"
                                    placeholder="Masukkan kata sandi" required autocomplete="current-password">
                                <button type="button" class="password-toggle-btn" id="togglePasswordBtn"
                                    onclick="togglePasswordVisibility()" aria-label="Tampilkan atau sembunyikan kata sandi">
                                    <span id="toggleIcon">👁️</span>
                                </button>
                            </div>
                        </div>

                        <!-- Options Row: Remember Me & Forgot Password -->
                        <div class="form-actions-row">
                            <label class="custom-checkbox-wrapper">
                                <input type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <span>Ingat saya</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-password-link">Lupa kata sandi?</a>
                            @endif
                        </div>

                        <!-- Primary Submit Button -->
                        <button type="submit" class="btn-submit-primary" id="btnSubmit">
                            <span>Masuk ke Akun</span>
                        </button>
                    </form>

                    <!-- OAuth Divider & Google Button -->
                    <div class="auth-divider">
                        <span>atau masuk dengan</span>
                    </div>

                    <a href="{{ route('google.redirect') }}" class="btn-google-oauth">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M23.766 12.2764C23.766 11.4607 23.6999 10.6406 23.5588 9.83807H12.24V14.4591H18.7217C18.4528 15.9494 17.5885 17.2678 16.323 18.1056V21.1039H20.19C22.4608 19.0139 23.766 15.9274 23.766 12.2764Z"
                                fill="#4285F4" />
                            <path
                                d="M12.24 24C15.4765 24 18.2059 22.9282 20.1945 21.1039L16.3275 18.1056C15.2517 18.8375 13.8627 19.3006 12.2445 19.3006C9.11304 19.3006 6.45946 17.1894 5.50705 14.3002H1.5166V17.3912C3.55371 21.4434 7.7029 24 12.24 24Z"
                                fill="#34A853" />
                            <path
                                d="M5.50255 14.3003C5.25134 13.5518 5.11475 12.7533 5.11475 11.9999C5.11475 11.2465 5.25134 10.448 5.50255 9.69956V6.60858H1.5166C0.698944 8.23232 0.24 10.0631 0.24 11.9999C0.24 13.9367 0.698944 15.7675 1.5166 17.3912L5.50255 14.3003Z"
                                fill="#FBBC05" />
                            <path
                                d="M12.24 4.69966C14.0016 4.69966 15.5786 5.30456 16.8262 6.49377L20.2789 3.04107C18.2014 1.15858 15.472 0 12.24 0 C7.7029 0 3.55371 2.55657 1.5166 6.60878L5.50255 9.69976C6.45946 6.81061 9.11304 4.69966 12.24 4.69966Z"
                                fill="#EA4335" />
                        </svg>
                        <span>Lanjutkan dengan Google</span>
                    </a>

                    <!-- Register Prompt -->
                    <div class="auth-footer-prompt">
                        Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>
@endsection
