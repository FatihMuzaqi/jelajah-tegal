@extends('layouts.mitra')
@section('title', 'Profil Mitra')
@section('page-title', 'Profil Mitra')
@section('page-description', 'Identitas bisnis, lokasi, media, dan jam operasional tenant aktif.')

@section('content')
    <div class='dashboard-grid'>
        <!-- Left Column: Business Info -->
        <x-content-card title='Informasi Bisnis'>
            <form method='POST' action='{{ route('mitra.profile.update') }}'>
                @csrf
                @method('PUT')
                <x-form-input name='display_name' label='Nama Bisnis / Brand' :value='$mitra->display_name' required />
                <x-textarea name='description'
                    label='Deskripsi Usaha'>{{ old('description', $mitra->description) }}</x-textarea>

                <div class='row'>
                    <div class='col-md-6'>
                        <x-form-input name='contact_email' label='Email Kontak' type='email' :value='$mitra->contact_email' />
                    </div>
                    <div class='col-md-6'>
                        <x-form-input name='contact_phone' label='Telepon / WhatsApp' :value='$mitra->contact_phone' />
                    </div>
                </div>

                <x-select name='region_id' label='Wilayah / Kecamatan'>
                    <option value=''>Pilih Wilayah</option>
                    @foreach ($regions as $region)
                        <option value='{{ $region->id }}' @selected(old('region_id', $mitra->region_id) == $region->id)>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </x-select>

                <x-textarea name='address'
                    label='Alamat Lengkap Kantor / Lokasi'>{{ old('address', $mitra->address) }}</x-textarea>
                <button class='btn btn-lokantara fw-bold mt-2 rounded-pill px-4'>
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Profil
                </button>
            </form>
        </x-content-card>

        <!-- Left Column: Security & Change Password Card -->
        <x-content-card title='Keamanan & Ubah Kata Sandi' class='mt-3'>
            <div class="alert alert-light border d-flex align-items-start gap-2 mb-3 py-2.5 px-3 rounded-3" style="font-size: 12px; background: #f8fafc;">
                <i class="fa-solid fa-shield-halved text-success mt-0.5 flex-shrink-0"></i>
                <div>
                    Gunakan minimal 8 karakter dengan kombinasi huruf dan angka untuk memastikan keamanan akun Mitra Anda.
                </div>
            </div>

            <form method='POST' action='{{ route('mitra.profile.password.update') }}'>
                @csrf
                @method('PUT')

                <div class='mb-3'>
                    <label class='form-label fw-bold text-dark' style='font-size: 13px;'>
                        Kata Sandi Saat Ini <span class='text-danger'>*</span>
                    </label>
                    <div class='input-group'>
                        <span class='input-group-text bg-white text-muted'><i class='fa-solid fa-lock'></i></span>
                        <input type='password' name='current_password' id='input_curr_pwd' class='form-control @error('current_password') is-invalid @enderror' placeholder='Masukkan kata sandi lama...' required>
                        <button class='btn btn-outline-secondary' type='button' onclick='togglePwdVisibility("input_curr_pwd", this)'>
                            <i class='fa-regular fa-eye'></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class='text-danger small mt-1'>{{ $message }}</div>
                    @enderror
                </div>

                <div class='row g-3 mb-3'>
                    <div class='col-md-6'>
                        <label class='form-label fw-bold text-dark' style='font-size: 13px;'>
                            Kata Sandi Baru <span class='text-danger'>*</span>
                        </label>
                        <div class='input-group'>
                            <span class='input-group-text bg-white text-muted'><i class='fa-solid fa-key'></i></span>
                            <input type='password' name='password' id='input_new_pwd' class='form-control @error('password') is-invalid @enderror' placeholder='Minimal 8 karakter' required>
                            <button class='btn btn-outline-secondary' type='button' onclick='togglePwdVisibility("input_new_pwd", this)'>
                                <i class='fa-regular fa-eye'></i>
                            </button>
                        </div>
                        @error('password')
                            <div class='text-danger small mt-1'>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class='col-md-6'>
                        <label class='form-label fw-bold text-dark' style='font-size: 13px;'>
                            Ulangi Kata Sandi Baru <span class='text-danger'>*</span>
                        </label>
                        <div class='input-group'>
                            <span class='input-group-text bg-white text-muted'><i class='fa-solid fa-check-double'></i></span>
                            <input type='password' name='password_confirmation' id='input_conf_pwd' class='form-control' placeholder='Ulangi kata sandi baru...' required>
                            <button class='btn btn-outline-secondary' type='button' onclick='togglePwdVisibility("input_conf_pwd", this)'>
                                <i class='fa-regular fa-eye'></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button class='btn btn-lokantara fw-bold rounded-pill px-4'>
                    <i class='fa-solid fa-key me-1'></i> Perbarui Kata Sandi
                </button>
            </form>
        </x-content-card>

        <!-- Right Column: Status & Brand Media -->
        <div>
            <x-content-card title='Status Bisnis'>
                <dl class='profile-summary'>
                    <div>
                        <dt>Status</dt>
                        <dd><x-status-badge :status='$mitra->status' /></dd>
                    </div>
                    <div>
                        <dt>Kategori Tenant</dt>
                        <dd>
                            @if($mitra->category === 'dinas')
                                <span class='badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 fs-8 fw-bold'>
                                    <i class='fa-solid fa-building-columns me-1'></i> Dinas (Pemerintah)
                                </span>
                            @else
                                <span class='badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-0.5 fs-8 fw-bold'>
                                    <i class='fa-solid fa-store me-1'></i> Non-Dinas (Swasta/Umum)
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt>Nama Legal</dt>
                        <dd>{{ $mitra->legal_name }}</dd>
                    </div>
                    <div>
                        <dt>Komisi Platform</dt>
                        <dd>{{ data_get($commission, 'rate') !== null ? data_get($commission, 'rate') . '%' : 'Belum dikonfigurasi' }}
                        </dd>
                    </div>
                    <div>
                        <dt>Fitur Layanan Aktif</dt>
                        <dd>{{ $mitra->features->where('status', 'enabled')->count() }} Kategori</dd>
                    </div>
                </dl>
                <a href='{{ route('mitra.kyc.index') }}' class='btn btn-sm btn-outline-lokantara w-100 mt-2'>
                    Kelola Dokumen Legal (KYC)
                </a>
            </x-content-card>

            <!-- Brand Media with Live Preview -->
            <x-content-card title='Logo & Banner Bisnis' class='mt-3'>
                <!-- 1. Logo Upload & Current Preview -->
                <div class='mb-4'>
                    <label class='form-label fw-bold mb-2'>Logo Mitra</label>
                    <div class='d-flex align-items-center gap-3 mb-2 p-2 rounded'
                        style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                        <div id='logo-preview-box'
                            style='width: 64px; height: 64px; border-radius: 12px; overflow: hidden; background: #e2e8f0; display: grid; place-items: center; flex-shrink: 0;'>
                            @if ($mitra->logoMedia)
                                <img id='logo-preview-img' src='{{ asset('storage/' . $mitra->logoMedia->object_key) }}'
                                    alt='Logo {{ $mitra->display_name }}'
                                    style='width: 100%; height: 100%; object-fit: cover;'>
                            @else
                                <span id='logo-preview-placeholder'
                                    style='font-size: 24px; font-weight: bold; color: #64748b;'>{{ str($mitra->display_name)->substr(0, 1)->upper() }}</span>
                                <img id='logo-preview-img' src='' alt=''
                                    style='width: 100%; height: 100%; object-fit: cover; display: none;'>
                            @endif
                        </div>
                        <div>
                            <small
                                class='text-muted d-block'>{{ $mitra->logoMedia ? 'Logo aktif terpasang' : 'Belum ada logo terpasang' }}</small>
                            <small class='text-muted' style='font-size: 11px;'>Format: JPG, PNG, WEBP (Maks 8MB)</small>
                        </div>
                    </div>

                    <form method='POST' action='{{ route('mitra.profile.media', 'logo') }}' enctype='multipart/form-data'>
                        @csrf
                        <input class='form-control form-control-sm mb-2' type='file' name='image' id='logo-file-input'
                            accept='image/jpeg,image/png,image/webp' required
                            onchange='previewLocalImage(this, "logo-preview-img", "logo-preview-placeholder")'>
                        <button class='btn btn-sm btn-lokantara fw-bold'>Unggah & Simpan Logo</button>
                    </form>
                </div>

                <hr>

                <!-- 2. Banner Upload & Current Preview -->
                <div>
                    <label class='form-label fw-bold mb-2'>Banner Header</label>
                    <div class='mb-2 p-2 rounded'
                        style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                        <div id='banner-preview-box'
                            style='width: 100%; height: 100px; border-radius: 8px; overflow: hidden; background: #e2e8f0; display: grid; place-items: center;'>
                            @if ($mitra->bannerMedia)
                                <img id='banner-preview-img'
                                    src='{{ asset('storage/' . $mitra->bannerMedia->object_key) }}'
                                    alt='Banner {{ $mitra->display_name }}'
                                    style='width: 100%; height: 100%; object-fit: cover;'>
                            @else
                                <span id='banner-preview-placeholder' style='font-size: 13px; color: #64748b;'>Belum ada
                                    banner terpasang</span>
                                <img id='banner-preview-img' src='' alt=''
                                    style='width: 100%; height: 100%; object-fit: cover; display: none;'>
                            @endif
                        </div>
                    </div>

                    <form method='POST' action='{{ route('mitra.profile.media', 'banner') }}'
                        enctype='multipart/form-data'>
                        @csrf
                        <input class='form-control form-control-sm mb-2' type='file' name='image'
                            id='banner-file-input' accept='image/jpeg,image/png,image/webp' required
                            onchange='previewLocalImage(this, "banner-preview-img", "banner-preview-placeholder")'>
                        <button class='btn btn-sm btn-lokantara fw-bold'>Unggah & Simpan Banner</button>
                    </form>
                </div>
            </x-content-card>
        </div>
    </div>

    <!-- Operating Hours Card -->
    <x-content-card title='Jam Operasional Bisnis' class='mt-3'>
        <form method='POST' action='{{ route('mitra.profile.hours') }}'>
            @csrf
            @method('PUT')
            @php($dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])
            <div class='hours-grid'>
                @foreach ($dayNames as $day => $name)
                    @php($hour = $mitra->operatingHours->firstWhere('day_of_week', $day))
                    <div class='hour-row'>
                        <input type='hidden' name='hours[{{ $day }}][day_of_week]' value='{{ $day }}'>
                        <strong>{{ $name }}</strong>
                        <label>
                            <input type='checkbox' name='hours[{{ $day }}][is_closed]' value='1'
                                @checked(old('hours.' . $day . '.is_closed', $hour?->is_closed))> Tutup
                        </label>
                        <input class='form-control' type='time' name='hours[{{ $day }}][opens_at]'
                            value='{{ old('hours.' . $day . '.opens_at', $hour?->opens_at) }}'>
                        <input class='form-control' type='time' name='hours[{{ $day }}][closes_at]'
                            value='{{ old('hours.' . $day . '.closes_at', $hour?->closes_at) }}'>
                    </div>
                @endforeach
            </div>
            <button class='btn btn-lokantara mt-3 fw-bold'>Simpan Jam Operasional</button>
        </form>
    </x-content-card>

    <script>
        function previewLocalImage(input, imgId, placeholderId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(imgId);
                    if (img) {
                        img.src = e.target.result;
                        img.style.display = 'block';
                    }
                    const placeholder = document.getElementById(placeholderId);
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function togglePwdVisibility(inputId, btn) {
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
@endsection
