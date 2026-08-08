@extends('layouts.auth')

@section('title', 'Daftar Akun — Lokantara')
@section('meta-description', 'Buat akun Lokantara baru untuk menikmati pemesanan tiket wisata, penginapan, reservasi kuliner, dan event lokal.')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-container">
        <div class="auth-card-grid">
            <!-- Left Side: Branding & Platform Features Showcase -->
            <div class="auth-visual-side">
                <div>
                    <a href="{{ route('home') }}" class="auth-brand-logo" aria-label="Lokantara Beranda">
                        <span class="auth-brand-badge">L</span>
                        <span>Lokantara</span>
                    </a>
                    
                    <h1 class="auth-hero-title">Bergabung dengan Ekosistem Lokantara</h1>
                    <p class="auth-hero-desc">Dapatkan akses penuh ke layanan wisata unggulan, penginapan nyaman, tempat kuliner khas, dan acara menarik di daerah Anda.</p>
                    
                    <div class="auth-feature-list">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">✨</div>
                            <div class="auth-feature-text">
                                <h6>Pendaftaran Cepat & Bebas Biaya</h6>
                                <p>Buat akun Consumer dalam hitungan detik dan langsung jelajahi potensi lokal.</p>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">📱</div>
                            <div class="auth-feature-text">
                                <h6>Tiket Digital QR Code</h6>
                                <p>Tiket pesanan tersimpan rapi di akun Anda dengan pemindaian gatekeeper mudah.</p>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">⭐</div>
                            <div class="auth-feature-text">
                                <h6>Ulasan Terverifikasi & Favorit</h6>
                                <p>Simpan tempat impian Anda dan bagikan pengalaman jujur ke komunitas.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="auth-visual-footer">
                    <span>&copy; {{ now()->year }} Lokantara Monolith</span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1">Akun Terverifikasi</span>
                </div>
            </div>

            <!-- Right Side: Register Form Card -->
            <div class="auth-form-side">
                <div class="auth-header">
                    <h2>Buat Akun Baru</h2>
                    <p>Lengkapi formulir di bawah ini untuk membuat akun Lokantara.</p>
                </div>

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

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <!-- Full Name Input -->
                    <div class="form-input-group">
                        <label for="name">Nama Lengkap</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">👤</span>
                            <input 
                                id="name" 
                                name="name" 
                                type="text" 
                                class="form-control-custom @error('name') is-invalid @enderror" 
                                value="{{ old('name') }}" 
                                placeholder="Masukkan nama lengkap" 
                                required 
                                autofocus 
                                autocomplete="name"
                            >
                        </div>
                    </div>

                    <!-- Email Input -->
                    <div class="form-input-group">
                        <label for="email">Alamat Email</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">✉️</span>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="form-control-custom @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" 
                                placeholder="contoh: nama@domain.com" 
                                required 
                                autocomplete="email"
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="form-input-group">
                        <label for="password">Kata Sandi (Minimal 8 karakter)</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">🔒</span>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                class="form-control-custom @error('password') is-invalid @enderror" 
                                placeholder="Buat kata sandi aman" 
                                required 
                                autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <!-- Password Confirmation Input -->
                    <div class="form-input-group">
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">🔑</span>
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                class="form-control-custom" 
                                placeholder="Ketik ulang kata sandi" 
                                required 
                                autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <!-- Primary Submit Button -->
                    <button type="submit" class="btn-submit-primary mt-2">
                        <span>Daftar Akun Baru</span>
                    </button>
                </form>

                <!-- OAuth Divider & Google Button -->
                <div class="auth-divider">
                    <span>atau daftar dengan</span>
                </div>

                <a href="{{ route('google.redirect') }}" class="btn-google-oauth">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M23.766 12.2764C23.766 11.4607 23.6999 10.6406 23.5588 9.83807H12.24V14.4591H18.7217C18.4528 15.9494 17.5885 17.2678 16.323 18.1056V21.1039H20.19C22.4608 19.0139 23.766 15.9274 23.766 12.2764Z" fill="#4285F4"/>
                        <path d="M12.24 24C15.4765 24 18.2059 22.9282 20.1945 21.1039L16.3275 18.1056C15.2517 18.8375 13.8627 19.3006 12.2445 19.3006C9.11304 19.3006 6.45946 17.1894 5.50705 14.3002H1.5166V17.3912C3.55371 21.4434 7.7029 24 12.24 24Z" fill="#34A853"/>
                        <path d="M5.50255 14.3003C5.25134 13.5518 5.11475 12.7533 5.11475 11.9999C5.11475 11.2465 5.25134 10.448 5.50255 9.69956V6.60858H1.5166C0.698944 8.23232 0.24 10.0631 0.24 11.9999C0.24 13.9367 0.698944 15.7675 1.5166 17.3912L5.50255 14.3003Z" fill="#FBBC05"/>
                        <path d="M12.24 4.69966C14.0016 4.69966 15.5786 5.30456 16.8262 6.49377L20.2789 3.04107C18.2014 1.15858 15.472 0 12.24 0 C7.7029 0 3.55371 2.55657 1.5166 6.60878L5.50255 9.69976C6.45946 6.81061 9.11304 4.69966 12.24 4.69966Z" fill="#EA4335"/>
                    </svg>
                    <span>Daftar dengan Google</span>
                </a>

                <!-- Login Prompt -->
                <div class="auth-footer-prompt">
                    Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
