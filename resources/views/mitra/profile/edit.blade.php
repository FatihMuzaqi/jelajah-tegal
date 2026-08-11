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
            <x-textarea name='description' label='Deskripsi Usaha'>{{ old('description', $mitra->description) }}</x-textarea>
            
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
                @foreach($regions as $region)
                    <option value='{{ $region->id }}' @selected(old('region_id', $mitra->region_id) == $region->id)>
                        {{ $region->name }}
                    </option>
                @endforeach
            </x-select>
            
            <x-textarea name='address' label='Alamat Lengkap Kantor / Lokasi'>{{ old('address', $mitra->address) }}</x-textarea>
            <button class='btn btn-lokantara fw-bold mt-2'>Simpan Profil</button>
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
                    <dt>Nama Legal</dt>
                    <dd>{{ $mitra->legal_name }}</dd>
                </div>
                <div>
                    <dt>Komisi Platform</dt>
                    <dd>{{ data_get($commission, 'rate') !== null ? data_get($commission, 'rate').'%' : 'Belum dikonfigurasi' }}</dd>
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
                <div class='d-flex align-items-center gap-3 mb-2 p-2 rounded' style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                    <div id='logo-preview-box' style='width: 64px; height: 64px; border-radius: 12px; overflow: hidden; background: #e2e8f0; display: grid; place-items: center; flex-shrink: 0;'>
                        @if($mitra->logoMedia)
                            <img id='logo-preview-img' src='{{ asset('storage/' . $mitra->logoMedia->object_key) }}' alt='Logo {{ $mitra->display_name }}' style='width: 100%; height: 100%; object-fit: cover;'>
                        @else
                            <span id='logo-preview-placeholder' style='font-size: 24px; font-weight: bold; color: #64748b;'>{{ str($mitra->display_name)->substr(0,1)->upper() }}</span>
                            <img id='logo-preview-img' src='' alt='' style='width: 100%; height: 100%; object-fit: cover; display: none;'>
                        @endif
                    </div>
                    <div>
                        <small class='text-muted d-block'>{{ $mitra->logoMedia ? 'Logo aktif terpasang' : 'Belum ada logo terpasang' }}</small>
                        <small class='text-muted' style='font-size: 11px;'>Format: JPG, PNG, WEBP (Maks 8MB)</small>
                    </div>
                </div>

                <form method='POST' action='{{ route('mitra.profile.media', 'logo') }}' enctype='multipart/form-data'>
                    @csrf
                    <input class='form-control form-control-sm mb-2' type='file' name='image' id='logo-file-input' accept='image/jpeg,image/png,image/webp' required onchange='previewLocalImage(this, "logo-preview-img", "logo-preview-placeholder")'>
                    <button class='btn btn-sm btn-lokantara fw-bold'>Unggah & Simpan Logo</button>
                </form>
            </div>

            <hr>

            <!-- 2. Banner Upload & Current Preview -->
            <div>
                <label class='form-label fw-bold mb-2'>Banner Header</label>
                <div class='mb-2 p-2 rounded' style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                    <div id='banner-preview-box' style='width: 100%; height: 100px; border-radius: 8px; overflow: hidden; background: #e2e8f0; display: grid; place-items: center;'>
                        @if($mitra->bannerMedia)
                            <img id='banner-preview-img' src='{{ asset('storage/' . $mitra->bannerMedia->object_key) }}' alt='Banner {{ $mitra->display_name }}' style='width: 100%; height: 100%; object-fit: cover;'>
                        @else
                            <span id='banner-preview-placeholder' style='font-size: 13px; color: #64748b;'>Belum ada banner terpasang</span>
                            <img id='banner-preview-img' src='' alt='' style='width: 100%; height: 100%; object-fit: cover; display: none;'>
                        @endif
                    </div>
                </div>

                <form method='POST' action='{{ route('mitra.profile.media', 'banner') }}' enctype='multipart/form-data'>
                    @csrf
                    <input class='form-control form-control-sm mb-2' type='file' name='image' id='banner-file-input' accept='image/jpeg,image/png,image/webp' required onchange='previewLocalImage(this, "banner-preview-img", "banner-preview-placeholder")'>
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
            @foreach($dayNames as $day => $name)
                @php($hour = $mitra->operatingHours->firstWhere('day_of_week', $day))
                <div class='hour-row'>
                    <input type='hidden' name='hours[{{ $day }}][day_of_week]' value='{{ $day }}'>
                    <strong>{{ $name }}</strong>
                    <label>
                        <input type='checkbox' name='hours[{{ $day }}][is_closed]' value='1' @checked(old('hours.'.$day.'.is_closed', $hour?->is_closed))> Tutup
                    </label>
                    <input class='form-control' type='time' name='hours[{{ $day }}][opens_at]' value='{{ old('hours.'.$day.'.opens_at', $hour?->opens_at) }}'>
                    <input class='form-control' type='time' name='hours[{{ $day }}][closes_at]' value='{{ old('hours.'.$day.'.closes_at', $hour?->closes_at) }}'>
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
</script>
@endsection
