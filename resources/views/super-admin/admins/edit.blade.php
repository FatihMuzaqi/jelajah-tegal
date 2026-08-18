@extends('layouts.super-admin')

@section('title', 'Edit Administrator: ' . $admin->name)
@section('page-title', 'Edit Data Administrator')
@section('page-description', 'Perbarui identitas staf, status operasional (Aktif/Suspended), atau reset password untuk akun ' . $admin->email . '.')

@section('content')
    <x-content-card title='Formulir Pembaruan Data Administrator'>
        <form method='POST' action='{{ route('super-admin.admins.update', $admin) }}'>
            @csrf
            @method('PUT')

            <!-- 1. Identitas Staf -->
            <h6 class='fw-bold text-dark mb-3 d-flex align-items-center gap-2'>
                <i class="fa-solid fa-user-pen text-primary fs-7"></i>
                <span>Identitas Administrator</span>
            </h6>
            <div class='row g-3 mb-4'>
                <div class='col-md-6'>
                    <x-form-input name='name' label='Nama Lengkap / Label Staf' :value='old("name", $admin->name)' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='email' label='Alamat Email Login' type='email' :value='old("email", $admin->email)' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='phone' label='Nomor WhatsApp / Kontak' :value='old("phone", $admin->phone)' />
                </div>
                <div class='col-md-6'>
                    <x-select name='status' label='Status Akun'>
                        <option value='active' @selected(old('status', $admin->status) === 'active')>🟢 Aktif (Bisa Login & Bertugas)</option>
                        <option value='suspended' @selected(old('status', $admin->status) === 'suspended')>🔴 Suspended (Akses Ditangguhkan Sementara)</option>
                    </x-select>
                </div>
                @if(!$admin->hasRole('super-admin'))
                    <div class='col-md-6'>
                        <x-select name='role' label='Peran Akses (Role)'>
                            <option value='admin' @selected(old('role', $admin->getRoleNames()->first()) === 'admin')>🛡️ Administrator Platform (Akses Operasional Penuh)</option>
                            <option value='dinas-supervisor' @selected(old('role', $admin->getRoleNames()->first()) === 'dinas-supervisor')>🏛️ Dinas Supervisor (Monitoring PAD & Wisata Pemda)</option>
                        </x-select>
                    </div>
                @endif
            </div>

            <hr class='my-4'>

            <!-- 2. Reset Password Opsional -->
            <div class='d-flex align-items-center justify-content-between mb-2'>
                <h6 class='fw-bold text-dark mb-0 d-flex align-items-center gap-2'>
                    <i class="fa-solid fa-key text-warning fs-7"></i>
                    <span>Reset Password Staf (Opsional)</span>
                </h6>
                <span class='badge bg-light text-muted border rounded-pill px-2.5 py-0.5 fs-8'>Kosongkan jika tidak ingin mengubah</span>
            </div>
            <p class='text-muted fs-8 mb-3'>Gunakan kolom di bawah ini hanya jika staf lupa password dan membutuhkan kata sandi baru.</p>
            <div class='row g-3 mb-4'>
                <div class='col-md-6'>
                    <x-form-input name='password' label='Password Baru' type='password' placeholder='Minimal 8 karakter' />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='password_confirmation' label='Konfirmasi Password Baru' type='password' placeholder='Ulangi password baru' />
                </div>
            </div>

            <div class='d-flex align-items-center gap-2 mt-4'>
                <button type='submit' class='btn btn-lokantara fw-bold px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2'>
                    <i class='fa-solid fa-floppy-disk'></i>
                    <span>Simpan Perubahan</span>
                </button>
                <a href='{{ route('super-admin.admins.index') }}' class='btn btn-outline-secondary rounded-pill px-3.5 py-2'>
                    Batal
                </a>
            </div>
        </form>
    </x-content-card>
@endsection
