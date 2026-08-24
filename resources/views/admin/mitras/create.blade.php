@extends('layouts.admin')

@section('title', 'Buat Mitra')
@section('page-title', 'Buat Mitra Baru')
@section('page-description', 'Daftarkan tenant baru (Dinas atau Non-Dinas) dan kirimkan undangan aktivasi akun kepada pemilik/penanggung jawab.')

@section('content')
    <x-content-card title='Formulir Registrasi Mitra & Owner'>
        <form method='POST' action='{{ route('admin.mitras.store') }}'>
            @csrf

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
                                   @checked(old('category', 'non_dinas') === 'non_dinas') required>
                            <div>
                                <div class='d-flex align-items-center gap-2 mb-1'>
                                    <strong class='text-dark fs-6'>🏢 Non-Dinas (Swasta / Umum)</strong>
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
                                   @checked(old('category') === 'dinas') required>
                            <div>
                                <div class='d-flex align-items-center gap-2 mb-1'>
                                    <strong class='text-primary fs-6'>🏛️ Dinas (Pemerintah / Instansi)</strong>
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
            <h6 class='fw-bold text-dark mb-3'>Identitas Bisnis / Tenant</h6>
            <div class='row'>
                <div class='col-md-6'>
                    <x-form-input name='legal_name' label='Nama Legal / Badan Hukum' placeholder='Cth: PT Pesona Bahari / Disporapar Tegal' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='display_name' label='Nama Tampil Publik' placeholder='Cth: Wisata Bahari Tegal' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='slug' label='Slug URL' placeholder='cth: wisata-bahari-tegal'
                        hint='Otomatis terisi dari Nama Tampil Publik (bisa diedit manual).' required />
                </div>
                <div class='col-md-6'>
                    <x-select name='region_id' label='Lokasi / Wilayah Kecamatan'>
                        <option value=''>Pilih Wilayah</option>
                        @foreach ($regions as $region)
                            <option value='{{ $region->id }}' @selected(old('region_id') == $region->id)>{{ $region->name }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <hr class='my-4'>

            <!-- 3. Penanggung Jawab / Owner -->
            <h6 class='fw-bold text-dark mb-3'>Akun Penanggung Jawab (Owner)</h6>
            <div class='row'>
                <div class='col-md-6'>
                    <x-form-input name='owner_name' label='Nama Lengkap Owner / Kepala Instansi' placeholder='Cth: Budi Susanto / Dr. Ahmad' required />
                </div>
                <div class='col-md-6'>
                    <x-form-input name='owner_email' label='Alamat Email Owner' type='email' placeholder='owner@bisnis.com'
                        hint='Tautan aktivasi tenant akan dikirim ke alamat email ini.' required />
                </div>
            </div>

            <div class='d-flex align-items-center gap-2 mt-4'>
                <button type='submit' class='btn btn-lokantara fw-bold px-4 py-2'>
                    <i class='fa-solid fa-paper-plane me-1.5'></i> Buat Mitra & Kirim Undangan
                </button>
                <a href='{{ route('admin.mitras.index') }}' class='btn btn-outline-secondary rounded-pill px-3 py-2'>
                    Batal
                </a>
            </div>
        </form>
    </x-content-card>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const legalNameInput = document.getElementById('legal_name');
    const displayNameInput = document.getElementById('display_name');
    const slugInput = document.getElementById('slug');

    if (!slugInput) return;

    let isManualSlug = slugInput.value.trim() !== '';

    function slugify(text) {
        return text.toString().toLowerCase()
            .trim()
            .replace(/\s+/g, '-')           // Ganti spasi dengan tanda hubung (-)
            .replace(/[^\w\-]+/g, '')       // Hapus karakter non-alphanumeric kecuali tanda hubung
            .replace(/\-\-+/g, '-')         // Ganti tanda hubung ganda dengan tanda hubung tunggal
            .replace(/^-+/, '')             // Hapus tanda hubung di awal
            .replace(/-+$/, '');            // Hapus tanda hubung di akhir
    }

    if (displayNameInput) {
        displayNameInput.addEventListener('input', function() {
            if (!isManualSlug) {
                slugInput.value = slugify(this.value);
            }
        });
    }

    if (legalNameInput) {
        legalNameInput.addEventListener('input', function() {
            // Jika display_name masih kosong dan slug belum diubah manual, isi dari legal_name
            if (displayNameInput && !displayNameInput.value.trim() && !isManualSlug) {
                slugInput.value = slugify(this.value);
            }
        });
    }

    slugInput.addEventListener('input', function() {
        if (this.value.trim() === '') {
            isManualSlug = false;
            if (displayNameInput && displayNameInput.value.trim() !== '') {
                this.value = slugify(displayNameInput.value);
            } else if (legalNameInput && legalNameInput.value.trim() !== '') {
                this.value = slugify(legalNameInput.value);
            }
        } else {
            isManualSlug = true;
        }
    });

    // Auto-generate pada initial load jika slug kosong namun display_name sudah ada (misal browser auto-fill)
    if (!slugInput.value.trim() && displayNameInput && displayNameInput.value.trim()) {
        slugInput.value = slugify(displayNameInput.value);
    }
});
</script>
@endpush
@endsection
