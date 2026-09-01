@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi — Jelajah Tegal')
@section('meta-description', 'Kirim tautan pemulihan kata sandi ke email Anda.')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-container">
        <div class="auth-card-grid">
            <!-- Left Side: Branding & Info -->
            <div class="auth-visual-side">
                <div>
                    <a href="{{ route('home') }}" class="auth-brand-logo" aria-label="Jelajah Tegal Beranda">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Jelajah Tegal" width="36" height="36" style="border-radius: 8px; object-fit: contain;">
                        <span>Jelajah Tegal</span>
                    </a>
                    
                    <h1 class="auth-hero-title">Pemulihan Akses Akun</h1>
                    <p class="auth-hero-desc">Jangan khawatir, kami akan mengirimkan instruksi dan tautan aman untuk menyetel ulang kata sandi Anda.</p>
                </div>
                
                <div class="auth-visual-footer">
                    <span>&copy; {{ now()->year }} Jelajah Tegal</span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1">Sistem Keamanan</span>
                </div>
            </div>

            <!-- Right Side: Form Card -->
            <div class="auth-form-side">
                <!-- Mobile Brand Header -->
                <div class="d-lg-none text-center mb-3">
                    <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Jelajah Tegal" width="36" height="36" style="border-radius: 8px; object-fit: contain;">
                        <span class="fw-bold text-dark fs-6">Jelajah Tegal</span>
                    </a>
                </div>

                <div class="auth-header">
                    <h2>Lupa Kata Sandi?</h2>
                    <p>Masukkan alamat email akun Anda yang terdaftar.</p>
                </div>

                @if (session('status'))
                    <div class="alert-custom alert-custom-success" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-custom alert-custom-danger" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Input -->
                    <div class="form-input-group">
                        <label for="email">Alamat Email Terdaftar</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">️</span>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="form-control-custom @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" 
                                placeholder="contoh: nama@domain.com" 
                                required 
                                autofocus 
                                autocomplete="email"
                            >
                        </div>
                    </div>

                    <!-- Primary Submit Button -->
                    <button type="submit" class="btn-submit-primary mt-3">
                        <span>Kirim Tautan Reset Password</span>
                    </button>
                </form>

                <!-- Back to Login Prompt -->
                <div class="auth-footer-prompt mt-4">
                    Ingat kata sandi Anda? <a href="{{ route('login') }}">Kembali ke halaman masuk</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
