@extends('layouts.consumer')

@section('title', 'Profil & Keamanan Akun')
@section('page-title', 'Profil & Keamanan')
@section('page-description', 'Kelola informasi kontak akun wisatawan dan perbarui kata sandi untuk keamanan transaksi Anda.')

@section('content')
    <div class="row g-4">
        <!-- Kolom Kiri: Informasi Data Diri -->
        <div class="col-lg-6">
            <x-content-card title="Data Diri Wisatawan">
                <form method="POST" action="{{ route('consumer.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4 text-center">
                        <div class="position-relative d-inline-block">
                            @if($user->profile?->avatar)
                                <img src="{{ asset('storage/' . $user->profile->avatar->object_key) }}" alt="Avatar" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="rounded-circle border bg-light d-flex align-items-center justify-content-center text-muted fw-bold" style="width: 100px; height: 100px; font-size: 36px;">
                                    {{ str($user->name)->substr(0, 1)->upper() }}
                                </div>
                            @endif
                            <label for="avatar_upload" class="position-absolute bottom-0 end-0 bg-white border rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 32px; height: 32px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <i class="fa-solid fa-camera fs-7"></i>
                            </label>
                            <input type="file" name="avatar" id="avatar_upload" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                        </div>
                        <div class="small text-muted mt-2">Format: JPG, PNG. Maksimal 3 MB.</div>
                        @error('avatar')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-user"></i></span>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap Anda">
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">
                            Alamat Email
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" class="form-control bg-light text-muted" value="{{ $user->email }}" readonly disabled>
                            @if($user->email_verified_at)
                                <span class="input-group-text bg-success-subtle text-success border-start-0" title="Email Terverifikasi">
                                    <i class="fa-solid fa-circle-check me-1"></i> <small class="fw-bold fs-8">Terverifikasi</small>
                                </span>
                            @endif
                        </div>
                        <small class="text-muted fs-8 d-block mt-1">
                            Email digunakan untuk identitas login, pengiriman e-tiket, dan bukti pembayaran QRIS.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">
                            Nomor Telepon / WhatsApp
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-phone"></i></span>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890">
                        </div>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-lokantara fw-bold rounded-pill px-4">
                            <i class="fa-solid fa-floppy-disk me-1.5"></i> Simpan Profil
                        </button>
                    </div>
                </form>
            </x-content-card>
        </div>

        <!-- Kolom Kanan: Keamanan & Ubah Password -->
        <div class="col-lg-6">
            <x-content-card title="Keamanan & Ubah Kata Sandi">
                <div class="alert alert-light border d-flex align-items-start gap-2 mb-3 py-2.5 px-3 rounded-3" style="font-size: 12px; background: #f8fafc;">
                    <i class="fa-solid fa-shield-halved text-primary mt-0.5 flex-shrink-0"></i>
                    <div>
                        Gunakan minimal 8 karakter dengan kombinasi huruf dan angka untuk memastikan keamanan akun dan tiket perjalanan Anda.
                    </div>
                </div>

                <form method="POST" action="{{ route('consumer.profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">
                            Kata Sandi Saat Ini <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="current_password" id="curr_consumer_pwd"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Masukkan kata sandi lama Anda" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleConsumerPwd('curr_consumer_pwd', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">
                            Kata Sandi Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-key"></i></span>
                            <input type="password" name="password" id="new_consumer_pwd"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimal 8 karakter" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleConsumerPwd('new_consumer_pwd', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">
                            Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-check-double"></i></span>
                            <input type="password" name="password_confirmation" id="conf_consumer_pwd"
                                   class="form-control" placeholder="Ulangi kata sandi baru" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleConsumerPwd('conf_consumer_pwd', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-lokantara fw-bold rounded-pill px-4">
                            <i class="fa-solid fa-key me-1.5"></i> Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </x-content-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleConsumerPwd(inputId, btn) {
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

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = input.closest('.position-relative');
                    let img = container.querySelector('img');
                    if (!img) {
                        const placeholder = container.querySelector('.bg-light');
                        if (placeholder) placeholder.remove();
                        img = document.createElement('img');
                        img.className = 'rounded-circle border';
                        img.style = 'width: 100px; height: 100px; object-fit: cover;';
                        container.insertBefore(img, container.querySelector('label'));
                    }
                    img.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
@endsection
