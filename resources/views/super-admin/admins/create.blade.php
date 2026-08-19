@extends('layouts.super-admin')

@section('title', 'Tambah Administrator Baru')
@section('page-title', 'Tambah Administrator Baru')
@section('page-description', 'Daftarkan akun staf administrator baru (misal: Admin 1, Admin 2) yang memiliki akses penuh mengelola operasional platform.')

@section('content')
    <x-content-card title='Formulir Registrasi Administrator Baru'>
        <form method='POST' action='{{ route('super-admin.admins.store') }}'>
            @csrf

            <!-- 1. Banner Info -->
            <div class="alert alert-light border rounded-4 p-3.5 mb-4 d-flex align-items-start gap-3 bg-light bg-opacity-50">
                <div class="superadmin-icon-box bg-primary-subtle text-primary mt-0.5" style="width: 36px; height: 36px; min-width: 36px; font-size: 15px;">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div class="fs-8 text-muted">
                    <strong class="text-dark d-block mb-0.5 fs-7">Akses Operasional Sama & Penuh:</strong>
                    Setiap staf admin yang didaftarkan secara otomatis memiliki akses penuh ke seluruh modul operasional (Moderasi Wisata, Penginapan, Kuliner, Event, Rental, Review KYC, Verifikasi Bank, dan Persetujuan Penarikan Dana).
                </div>
            </div>

            <!-- 2. Identitas Staf -->
            <h6 class='fw-bold text-dark mb-3 d-flex align-items-center gap-2'>
                <i class="fa-solid fa-id-card text-primary fs-7"></i>
                <span>Identitas Administrator</span>
            </h6>
            <div class='row g-3 mb-4'>
                <div class='col-md-6'>
                    <x-form-input name='name' label='Nama Lengkap / Label Staf' placeholder='Cth: Admin 1 (Budi Santoso)' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='email' label='Alamat Email Login' type='email' placeholder='admin1@jelajahtegal.com'
                        hint='Email aktif yang akan digunakan staf untuk login ke dashboard.' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='phone' label='Nomor WhatsApp / Kontak (Opsional)' placeholder='081234567890' />
                </div>
                <div class='col-md-6'>
                    <x-select name='role' label='Peran Akses (Role)' hint='Secara default memiliki akses penuh ke dashboard operasional platform.'>
                        <option value='admin' @selected(old('role', 'admin') === 'admin')>Administrator Platform (Default - Akses Operasional Penuh)</option>
                        <option value='dinas-supervisor' @selected(old('role') === 'dinas-supervisor')>Dinas Supervisor (Monitoring PAD & Wisata Pemda)</option>
                    </x-select>
                </div>
            </div>

            <hr class='my-4'>

            <!-- 3. Kredensial Keamanan -->
            <h6 class='fw-bold text-dark mb-3 d-flex align-items-center gap-2'>
                <i class="fa-solid fa-lock text-primary fs-7"></i>
                <span>Kredensial Keamanan Akun</span>
            </h6>
            <div class='row g-3 mb-4'>
                <div class='col-md-6'>
                    <x-form-input name='password' label='Password Awal' type='password' placeholder='Minimal 8 karakter'
                        hint='Berikan password awal ini kepada staf yang bersangkutan.' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='password_confirmation' label='Konfirmasi Password' type='password' placeholder='Ulangi password awal' required />
                </div>
            </div>

            <div class='alert alert-success d-flex align-items-center gap-2 mt-3 fs-8 py-2.5 px-3 border border-success-subtle rounded-3 bg-success-subtle text-success'>
                <i class='fa-solid fa-circle-check fs-6'></i>
                <span>Akun admin yang baru dibuat akan langsung berstatus <strong>Aktif</strong> dan <strong>Email Terverifikasi</strong> sehingga staf dapat langsung login tanpa kendala.</span>
            </div>

            <div class='d-flex align-items-center gap-2 mt-4'>
                <button type='submit' class='btn btn-lokantara fw-bold px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2'>
                    <i class='fa-solid fa-user-plus'></i>
                    <span>Buat Akun Administrator</span>
                </button>
                <a href='{{ route('super-admin.admins.index') }}' class='btn btn-outline-secondary rounded-pill px-3.5 py-2'>
                    Batal
                </a>
            </div>
        </form>
    </x-content-card>
@endsection
