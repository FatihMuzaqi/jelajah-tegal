@extends('layouts.admin')

@section('title', 'Edit Mitra: ' . $mitra->display_name)
@section('page-title', 'Edit Data Mitra')
@section('page-description', 'Perbarui informasi tenant, kategori operasional (Dinas / Non-Dinas), dan data kontak bisnis.')

@section('content')
    <x-content-card title='Formulir Pembaruan Data Mitra'>
        <form method='POST' action='{{ route('admin.mitras.update', $mitra) }}'>
            @csrf
            @method('PUT')

            <!-- 1. Kategori Mitra Selection -->
            <div class='mb-4'>
                <label class='form-label fw-bold text-dark fs-7 d-block mb-2'>
                    Kategori Mitra <span class='text-danger'>*</span>
                </label>
                <div class='row g-3'>
                    <div class='col-md-6'>
                        <label class='p-3 rounded-4 border bg-light d-flex align-items-start gap-3 cursor-pointer w-100 position-relative'
                               style='transition: all 0.2s ease;'>
                            <input type='radio' name='category' value='non_dinas' class='form-check-input mt-1'
                                   @checked(old('category', $mitra->category ?? 'non_dinas') === 'non_dinas') required>
                            <div>
                                <div class='d-flex align-items-center gap-2 mb-1'>
                                    <strong class='text-dark fs-6'> Non-Dinas (Swasta / Umum)</strong>
                                    <span class='badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 fs-8'>Default</span>
                                </div>
                                <p class='text-muted fs-8 mb-0'>
                                    Pelaku usaha swasta, pengelola hotel, restoran/kuliner, rental kendaraan, komunitas, atau kelompok sadar wisata (Pokdarwis).
                                </p>
                            </div>
                        </label>
                    </div>
                    <div class='col-md-6'>
                        <label class='p-3 rounded-4 border bg-light d-flex align-items-start gap-3 cursor-pointer w-100 position-relative'
                               style='transition: all 0.2s ease;'>
                            <input type='radio' name='category' value='dinas' class='form-check-input mt-1'
                                   @checked(old('category', $mitra->category) === 'dinas') required>
                            <div>
                                <div class='d-flex align-items-center gap-2 mb-1'>
                                    <strong class='text-primary fs-6'>️ Dinas (Pemerintah / Instansi)</strong>
                                    <span class='badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 fs-8'>Resmi</span>
                                </div>
                                <p class='text-muted fs-8 mb-0'>
                                    Organisasi Perangkat Daerah (OPD), Dinas Kepemudaan Olahraga dan Pariwisata (Disporapar), atau badan resmi pengelola aset daerah.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>
                @error('category')
                    <small class='text-danger d-block mt-1.5'>{{ $message }}</small>
                @enderror
            </div>

            <hr class='my-4'>

            <!-- 2. Data Legal & Bisnis -->
            <h6 class='fw-bold text-dark mb-3'>Identitas Bisnis & Publik</h6>
            <div class='row'>
                <div class='col-md-6'>
                    <x-form-input name='legal_name' label='Nama Legal / Badan Hukum' :value='old("legal_name", $mitra->legal_name)' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='display_name' label='Nama Tampil Publik' :value='old("display_name", $mitra->display_name)' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='slug' label='Slug URL' :value='old("slug", $mitra->slug)'
                        hint='Alamat URL unik mitra pada portal publik.' required />
                </div>
                <div class='col-md-6'>
                    <x-select name='region_id' label='Lokasi / Wilayah Kecamatan'>
                        <option value=''>Pilih Wilayah</option>
                        @foreach ($regions as $region)
                            <option value='{{ $region->id }}' @selected(old('region_id', $mitra->region_id) == $region->id)>{{ $region->name }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class='col-md-6'>
                    <x-form-input name='registration_number' label='Nomor Induk Berusaha (NIB) / SK Dinas' :value='old("registration_number", $mitra->registration_number)' />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='contact_email' label='Email Kontak Operasional' type='email' :value='old("contact_email", $mitra->contact_email)' />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='contact_phone' label='Nomor Telepon / WhatsApp' :value='old("contact_phone", $mitra->contact_phone)' />
                </div>
                <div class='col-12'>
                    <x-textarea name='description' label='Deskripsi Singkat Usaha / Instansi'>{{ old('description', $mitra->description) }}</x-textarea>
                </div>
                <div class='col-12'>
                    <x-textarea name='address' label='Alamat Lengkap Kantor / Lokasi Usaha'>{{ old('address', $mitra->address) }}</x-textarea>
                </div>
            </div>

            <div class='d-flex align-items-center gap-2 mt-4'>
                <button type='submit' class='btn btn-lokantara fw-bold px-4 py-2 rounded-pill'>
                    <i class='fa-solid fa-floppy-disk me-1.5'></i> Simpan Perubahan
                </button>
                <a href='{{ route('admin.mitras.index') }}' class='btn btn-outline-secondary rounded-pill px-3 py-2'>
                    Batal
                </a>
            </div>
        </form>
    </x-content-card>

    <!-- Reset Password Owner Card -->
    <x-content-card title='Keamanan & Reset Password Akun Owner' class='mt-4'>
        <div class='p-3 rounded-3 mb-3' style='background: #f8fafc; border: 1px solid #e2e8f0; font-size: 13px;'>
            <div class='d-flex align-items-center gap-2 mb-1'>
                <i class='fa-solid fa-user-shield text-primary'></i>
                <strong class='text-dark'>Akun Penanggung Jawab (Owner):</strong>
                <span>{{ $mitra->owner?->name ?? 'Belum ada akun owner' }}</span>
                <span class='text-muted'>·</span>
                <code>{{ $mitra->owner?->email ?? '-' }}</code>
            </div>
            <small class='text-muted'>
                Admin dapat menyetel ulang kata sandi akun pemilik tenant ini apabila pemilik lupa kata sandi atau mengalami kendala login.
            </small>
        </div>

        @if($mitra->owner)
            <form method='POST' action='{{ route('admin.mitras.reset-owner-password', $mitra) }}'>
                @csrf
                <div class='row g-3'>
                    <div class='col-md-6'>
                        <div class='d-flex justify-content-between align-items-center mb-1'>
                            <label class='form-label fw-bold text-dark mb-0' style='font-size: 13px;'>
                                Kata Sandi Baru <span class='text-danger'>*</span>
                            </label>
                            <button type='button' class='btn btn-link btn-sm p-0 text-decoration-none' style='font-size: 11px;'
                                    onclick="generateEditOwnerPwd('edit_owner_pwd', 'edit_owner_conf')">
                                <i class='fa-solid fa-wand-magic-sparkles me-1'></i> Buat Acak
                            </button>
                        </div>
                        <input type='text' name='password' id='edit_owner_pwd' class='form-control font-monospace' placeholder='Minimal 8 karakter' required>
                    </div>

                    <div class='col-md-6'>
                        <label class='form-label fw-bold text-dark mb-1' style='font-size: 13px;'>
                            Ulangi Kata Sandi Baru <span class='text-danger'>*</span>
                        </label>
                        <input type='text' name='password_confirmation' id='edit_owner_conf' class='form-control font-monospace' placeholder='Ulangi kata sandi baru' required>
                    </div>
                </div>

                <button type='submit' class='btn btn-outline-primary fw-bold rounded-pill px-4 py-2 mt-3'>
                    <i class='fa-solid fa-key me-1'></i> Reset Kata Sandi Owner
                </button>
            </form>
        @else
            <div class='alert alert-warning mb-0'>Mitra ini tidak memiliki akun owner terdaftar.</div>
        @endif
    </x-content-card>

    @push('scripts')
    <script>
        function generateEditOwnerPwd(newId, confId) {
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%';
            let pwd = '';
            for (let i = 0; i < 10; i++) {
                pwd += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById(newId).value = pwd;
            document.getElementById(confId).value = pwd;
        }
    </script>
    @endpush
@endsection
