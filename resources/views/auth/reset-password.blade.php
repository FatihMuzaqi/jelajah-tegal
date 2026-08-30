@extends('layouts.auth')

@section('title', 'Setel Ulang Kata Sandi — Lokantara')
@section('meta-description', 'Setel ulang kata sandi baru untuk akun Lokantara Anda.')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-container">
        <div class="auth-card-grid">
            <!-- Left Side: Branding -->
            <div class="auth-visual-side">
                <div>
                    <a href="{{ route('home') }}" class="auth-brand-logo" aria-label="Lokantara Beranda">
                        <span class="auth-brand-badge">L</span>
                        <span>Lokantara</span>
                    </a>
                    
                    <h1 class="auth-hero-title">Setel Kata Sandi Baru</h1>
                    <p class="auth-hero-desc">Buat kata sandi baru yang kuat untuk mengamankan akun Anda.</p>
                </div>
                
                <div class="auth-visual-footer">
                    <span>&copy; {{ now()->year }} Lokantara Monolith</span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1">Aman & Terenkripsi</span>
                </div>
            </div>

            <!-- Right Side: Form Card -->
            <div class="auth-form-side">
                <div class="auth-header">
                    <h2>Buat Kata Sandi Baru</h2>
                    <p>Masukkan kata sandi baru Anda di bawah ini.</p>
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

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Input -->
                    <div class="form-input-group">
                        <label for="email">Alamat Email</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">️</span>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="form-control-custom @error('email') is-invalid @enderror" 
                                value="{{ old('email', $email) }}" 
                                required 
                                readonly
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="form-input-group">
                        <label for="password">Kata Sandi Baru</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon"></span>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                class="form-control-custom @error('password') is-invalid @enderror" 
                                placeholder="Minimal 8 karakter" 
                                required 
                                autofocus 
                                autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <!-- Password Confirmation Input -->
                    <div class="form-input-group">
                        <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon"></span>
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                class="form-control-custom" 
                                placeholder="Ketik ulang kata sandi baru" 
                                required 
                                autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <!-- Primary Submit Button -->
                    <button type="submit" class="btn-submit-primary mt-3">
                        <span>Simpan Kata Sandi Baru</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
