@extends('layouts.gatekeeper')

@section('title', 'Profil & Keamanan Petugas Loket')
@section('page-title', 'Profil & Keamanan Petugas')
@section('page-description', 'Informasi akun petugas lapangan dan pembaruan kata sandi untuk keamanan operasional scanning tiket.')

@section('content')
    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <!-- Informasi Akun -->
            <x-content-card title="Informasi Akun Petugas Loket">
                <div class="p-3 rounded-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 13px;">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <span class="text-muted d-block fs-8">Nama Petugas:</span>
                            <strong class="text-dark fs-7">{{ $user->name }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block fs-8">Email Akun:</span>
                            <code class="text-dark fs-7">{{ $user->email }}</code>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted d-block fs-8">Mitra Penugasan:</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                <i class="fa-solid fa-store me-1"></i> {{ $mitra->display_name ?? 'Mitra Usaha' }}
                            </span>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted d-block fs-8">Peran / Hak Akses:</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                <i class="fa-solid fa-qrcode me-1"></i> Gatekeeper (Petugas Scanner)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Form Ganti Password -->
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-key text-primary"></i>
                    <span>Ubah Kata Sandi Akun</span>
                </h6>

                <div class="alert alert-light border d-flex align-items-start gap-2 mb-3 py-2.5 px-3 rounded-3" style="font-size: 12px; background: #f8fafc;">
                    <i class="fa-solid fa-shield-halved text-success mt-0.5 flex-shrink-0"></i>
                    <div>
                        Gunakan minimal 8 karakter agar akun perangkat scanner loket Anda aman dari akses tidak sah.
                    </div>
                </div>

                <form method="POST" action="{{ route('gatekeeper.profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">
                            Kata Sandi Saat Ini <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="current_password" id="curr_gatekeeper_pwd"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Masukkan kata sandi lama Anda" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleGatekeeperPwd('curr_gatekeeper_pwd', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7">
                                Kata Sandi Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-key"></i></span>
                                <input type="password" name="password" id="new_gatekeeper_pwd"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Minimal 8 karakter" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleGatekeeperPwd('new_gatekeeper_pwd', this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7">
                                Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-check-double"></i></span>
                                <input type="password" name="password_confirmation" id="conf_gatekeeper_pwd"
                                       class="form-control" placeholder="Ulangi kata sandi baru" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleGatekeeperPwd('conf_gatekeeper_pwd', this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-lokantara fw-bold rounded-pill px-4 py-2">
                            <i class="fa-solid fa-floppy-disk me-1.5"></i> Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </x-content-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleGatekeeperPwd(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
    @endpush
@endsection
