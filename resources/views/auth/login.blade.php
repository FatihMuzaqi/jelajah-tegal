@extends('layouts.auth')

@section('title', 'Masuk Akun — Jelajah Tegal')
@section('meta-description', 'Masuk ke portal Jelajah Tegal untuk mengakses pesanan tiket, rencana liburan AI, atau dashboard operasional unit bisnis mitra.')

@section('content')
<style>
    /* Professional Clean Auth Layout */
    .auth-page-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(20px, 4vw, 48px) 16px;
        background: radial-gradient(circle at 10% 15%, rgba(5, 150, 105, 0.08) 0%, transparent 40%),
                    radial-gradient(circle at 90% 85%, rgba(14, 116, 144, 0.08) 0%, transparent 40%),
                    #f8fafc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .auth-container {
        width: 100%;
        max-width: 1020px;
        margin: 0 auto;
    }
    .auth-card-grid {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 20px 50px -12px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.8);
        overflow: hidden;
        min-height: 580px;
    }
    
    /* Left Visual Photo Hero Side */
    .auth-visual-side {
        position: relative;
        background: linear-gradient(180deg, rgba(6, 40, 28, 0.72) 0%, rgba(4, 28, 19, 0.92) 100%),
                    url('{{ asset('images/guci_hero.png') }}') center/cover no-repeat;
        color: #ffffff;
        padding: clamp(36px, 5vw, 56px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
    }
    .auth-visual-side::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(52, 211, 153, 0.15) 0%, transparent 50%);
        pointer-events: none;
    }
    .auth-brand-logo {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        text-decoration: none;
        color: #ffffff;
        position: relative;
        z-index: 2;
    }
    .auth-brand-logo img {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        object-fit: contain;
        background: #ffffff;
        padding: 3px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
    }
    .auth-brand-title {
        font-family: 'Outfit', sans-serif;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    .auth-brand-sub {
        font-size: 11.5px;
        color: #a7f3d0;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .auth-hero-center {
        position: relative;
        z-index: 2;
        margin: auto 0;
        padding: 24px 0;
    }
    .auth-hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(28px, 3.4vw, 38px);
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.025em;
        color: #ffffff;
        margin-bottom: 12px;
    }
    .auth-hero-title span {
        color: #34d399;
    }
    .auth-hero-desc {
        font-size: 15px;
        line-height: 1.6;
        color: #d1fae5;
        margin: 0;
        opacity: 0.92;
        max-width: 420px;
    }
    .auth-location-badge {
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 99px;
        backdrop-filter: blur(10px);
        font-size: 12.5px;
        color: #e2e8f0;
        font-weight: 600;
        width: fit-content;
    }

    /* Right Form Side */
    .auth-form-side {
        padding: clamp(36px, 5vw, 56px);
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #ffffff;
    }
    .auth-header {
        margin-bottom: 26px;
    }
    .auth-header h2 {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(24px, 2.6vw, 30px);
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        margin-bottom: 6px;
    }
    .auth-header p {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }
    .form-input-group {
        margin-bottom: 18px;
    }
    .form-input-group label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .form-input-icon {
        position: absolute;
        left: 16px;
        color: #94a3b8;
        font-size: 15px;
        pointer-events: none;
        transition: color 0.2s ease;
    }
    .form-control-custom {
        width: 100%;
        height: 48px;
        padding: 10px 16px 10px 46px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-size: 14px;
        color: #0f172a;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .form-control-custom:focus {
        outline: none;
        border-color: #059669;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.12);
    }
    .form-control-custom:focus + .form-input-icon,
    .form-input-wrapper:focus-within .form-input-icon {
        color: #059669;
    }
    .form-control-custom.is-invalid {
        border-color: #ef4444;
        background: #fff5f5;
    }
    .password-toggle-btn {
        position: absolute;
        right: 12px;
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 6px 8px;
        font-size: 15px;
        border-radius: 8px;
        transition: color 0.2s ease, background-color 0.2s ease;
    }
    .password-toggle-btn:hover {
        color: #334155;
        background: #e2e8f0;
    }
    .form-actions-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
        font-size: 13px;
    }
    .custom-checkbox-wrapper {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        color: #475569;
        font-weight: 500;
        user-select: none;
    }
    .custom-checkbox-wrapper input[type="checkbox"] {
        accent-color: #059669;
        width: 16px;
        height: 16px;
        cursor: pointer;
    }
    .forgot-password-link {
        color: #059669;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .forgot-password-link:hover {
        color: #047857;
        text-decoration: underline;
    }
    .btn-submit-primary {
        width: 100%;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        border: none;
        color: #ffffff;
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(4, 120, 87, 0.25);
        transition: all 0.25s ease;
    }
    .btn-submit-primary:hover {
        background: linear-gradient(135deg, #047857 0%, #064e3b 100%);
        box-shadow: 0 8px 24px rgba(4, 120, 87, 0.35);
        transform: translateY(-1.5px);
    }
    .auth-divider {
        display: flex;
        align-items: center;
        margin: 22px 0;
        color: #94a3b8;
        font-size: 12.5px;
        font-weight: 500;
    }
    .auth-divider::before, .auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }
    .auth-divider span {
        padding: 0 12px;
    }
    .btn-google-oauth {
        width: 100%;
        height: 48px;
        border-radius: 12px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        color: #1e293b;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .btn-google-oauth:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .auth-footer-prompt {
        text-align: center;
        margin-top: 24px;
        font-size: 13.5px;
        color: #64748b;
    }
    .auth-footer-prompt a {
        color: #059669;
        font-weight: 700;
        text-decoration: none;
    }
    .auth-footer-prompt a:hover {
        text-decoration: underline;
    }
    .alert-custom {
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
        line-height: 1.5;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .alert-custom-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    .alert-custom-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    @media (max-width: 991px) {
        .auth-card-grid {
            grid-template-columns: 1fr;
            max-width: 480px;
            margin: 0 auto;
        }
        .auth-visual-side {
            display: none;
        }
        .auth-form-side {
            padding: 36px 24px;
        }
    }
</style>

<div class="auth-page-wrapper">
    <div class="auth-container">
        <div class="auth-card-grid">
            
            <!-- Left Side: Scenic Photo & Brand Logo Showcase -->
            <div class="auth-visual-side">
                <!-- Brand Header -->
                <a href="{{ route('home') }}" class="auth-brand-logo" aria-label="Jelajah Tegal Beranda">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Jelajah Tegal">
                    <div>
                        <div class="auth-brand-title">Jelajah Tegal</div>
                        <div class="auth-brand-sub">Platform Pariwisata & Ekonomi Kreatif</div>
                    </div>
                </a>

                <!-- Hero Content (Compact & Visual) -->
                <div class="auth-hero-center">
                    <h1 class="auth-hero-title">
                        Jelajahi Pesona,<br><span>Temukan Ceritamu</span>
                    </h1>
                    <p class="auth-hero-desc">
                        Satu akun untuk kemudahan reservasi destinasi wisata, hotel, kuliner legendaris, dan event terbaik di Tegal.
                    </p>
                </div>

                <!-- Location Badge Indicator -->
                <div class="auth-location-badge">
                    <i class="fa-solid fa-location-dot text-emerald"></i>
                    <span>Pemandian Air Panas Guci &middot; Tegal, Jawa Tengah</span>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="auth-form-side">
                <!-- Mobile Top Brand Logo -->
                <div class="d-lg-none text-center mb-4">
                    <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Jelajah Tegal" style="height: 38px; width: 38px; border-radius: 10px;">
                        <span class="fw-extrabold text-dark fs-4" style="letter-spacing: -0.02em;">Jelajah Tegal</span>
                    </a>
                </div>

                <div class="auth-header">
                    <h2>Masuk ke Akun</h2>
                    <p>Silakan masukkan email & kata sandi Anda.</p>
                </div>

                <!-- Status & Error Notifications -->
                @if (session('status'))
                    <div class="alert-custom alert-custom-success" role="alert">
                        <i class="fa-solid fa-circle-check text-success fs-5 mt-0.5"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert-custom alert-custom-danger" role="alert">
                        <i class="fa-solid fa-triangle-exclamation text-danger fs-5 mt-0.5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-custom alert-custom-danger" role="alert">
                        <i class="fa-solid fa-circle-xmark text-danger fs-5 mt-0.5"></i>
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
                            <i class="fa-regular fa-envelope form-input-icon"></i>
                            <input id="email" name="email" type="email"
                                class="form-control-custom @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" placeholder="nama@email.com" required autofocus
                                autocomplete="email">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="form-input-group">
                        <label for="password">Kata Sandi</label>
                        <div class="form-input-wrapper">
                            <i class="fa-solid fa-lock form-input-icon"></i>
                            <input id="password" name="password" type="password"
                                class="form-control-custom @error('password') is-invalid @enderror"
                                placeholder="Masukkan kata sandi akun" required autocomplete="current-password">
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn"
                                onclick="togglePasswordVisibility()" aria-label="Tampilkan atau sembunyikan kata sandi">
                                <i class="fa-regular fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Options Row: Remember Me & Forgot Password -->
                    <div class="form-actions-row">
                        <label class="custom-checkbox-wrapper">
                            <input type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <span>Ingat saya di perangkat ini</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-password-link">Lupa kata sandi?</a>
                        @endif
                    </div>

                    <!-- Primary Submit Button -->
                    <button type="submit" class="btn-submit-primary" id="btnSubmit">
                        <span>Masuk ke Akun</span>
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </button>
                </form>

                <!-- OAuth Divider & Google Button -->
                <div class="auth-divider">
                    <span>atau lanjutkan dengan</span>
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
                    <span>Masuk dengan Google</span>
                </a>

                <!-- Register Prompt -->
                <div class="auth-footer-prompt">
                    Belum memiliki akun? <a href="{{ route('register') }}">Daftar Akun Baru</a>
                </div>
                <div class="auth-footer-prompt" style="margin-top: 10px;">
                    Punya usaha di Tegal? <a href="{{ route('mitra.register') }}" style="color: #059669;">Gabung Jadi Mitra</a>
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
            toggleIcon.className = 'fa-regular fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'fa-regular fa-eye';
        }
    }
</script>
@endsection
