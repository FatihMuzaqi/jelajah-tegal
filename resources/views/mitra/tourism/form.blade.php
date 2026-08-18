@extends('layouts.mitra')
@php($editing = isset($tourism))
@section('title', $editing ? 'Edit Wisata' : 'Tambah Wisata') @section('page-title', $editing ? 'Edit Wisata' : 'Tambah
Wisata') @section('page-description', 'Simpan sebagai draft sebelum diajukan ke Admin.')
@section('content')
    <x-content-card title='Informasi destinasi'>
        <form method='POST' action='{{ $editing ? route('mitra.tourism.update', $tourism) : route('mitra.tourism.store') }}'>
            @csrf @if ($editing)
                @method('PUT')
            @endif
            <div class='row'>
                <div class='col-md-8'><x-form-input name='name' label='Nama' :value='old('name', $tourism->name ?? '')' required /></div>
                <div class='col-md-4'><x-form-input name='slug' label='Slug' :value='old('slug', $tourism->slug ?? '')' required /></div>
            </div>
            <div class='row'>
                <div class='col-md-6'><x-select name='category_id' label='Kategori' required>
                        <option value=''>Pilih</option>
                        @foreach ($categories as $item)
                            <option value='{{ $item->id }}' @selected(old('category_id', $tourism->category_id ?? null) == $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class='col-md-6'><x-select name='region_id' label='Wilayah' required>
                        <option value=''>Pilih</option>
                        @foreach ($regions as $item)
                            <option value='{{ $item->id }}' @selected(old('region_id', $tourism->region_id ?? null) == $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>
            <x-textarea name='description'
                label='Deskripsi'>{{ old('description', $tourism->description ?? '') }}</x-textarea><x-textarea name='address'
                label='Alamat'>{{ old('address', $tourism->address ?? '') }}</x-textarea>
            <div class='row'>
                <div class='col-md-4'><x-select name='destination_type' label='Tipe' required>
                        @foreach (['nature' => 'Alam', 'culture' => 'Budaya', 'recreation' => 'Rekreasi', 'education' => 'Edukasi', 'religious' => 'Religi', 'other' => 'Lainnya'] as $key => $label)
                            <option value='{{ $key }}' @selected(old('destination_type', $tourism->tourism->destination_type ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class='col-md-4'><x-form-input name='visit_duration_minutes' type='number'
                        label='Durasi kunjungan (menit)' :value='old('visit_duration_minutes', $tourism->tourism->visit_duration_minutes ?? '')' /></div>
                <div class='col-md-4'><x-form-input name='badge' label='Badge' :value='old('badge', $tourism->tourism->badge ?? '')' /></div>
            </div>
            <!-- GPS Coordinates Section -->
            <div class="card border-0 shadow-sm rounded-4 p-3.5 mb-4 mt-2" style="background: #f8fafc; border: 1px solid #e2e8f0 !important;">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: #0f172a; font-size: 15px;">
                            <i class="fa-solid fa-location-dot text-danger"></i> Titik Koordinat Lokasi (GPS)
                        </h6>
                        <small class="text-muted d-block" style="font-size: 12px;">
                            Tentukan koordinat Latitude & Longitude agar destinasi tampil akurat pada peta dan navigasi pengunjung.
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success rounded-3 fw-bold d-inline-flex align-items-center gap-1" onclick="detectGPSLocation(this)">
                            <i class="fa-solid fa-crosshairs"></i> Gunakan Lokasi GPS Saya
                        </button>
                        <a id="gmaps-preview-btn" href="#" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3 fw-bold d-none align-items-center gap-1" rel="noopener noreferrer">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Cek di Google Maps
                        </a>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 13px;">
                            Latitude (Garis Lintang) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-arrows-up-down"></i></span>
                            <input type="number" step="any" name="latitude" id="coord_latitude" class="form-control border-start-0 @error('latitude') is-invalid @enderror"
                                placeholder="Contoh: -6.8797000" value="{{ old('latitude', $tourism->location->latitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
                        </div>
                        @error('latitude')
                            <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Wilayah Tegal & sekitarnya berkisar antara -6.8 s/d -7.2</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 13px;">
                            Longitude (Garis Bujur) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-arrows-left-right"></i></span>
                            <input type="number" step="any" name="longitude" id="coord_longitude" class="form-control border-start-0 @error('longitude') is-invalid @enderror"
                                placeholder="Contoh: 109.1256000" value="{{ old('longitude', $tourism->location->longitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
                        </div>
                        @error('longitude')
                            <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Wilayah Tegal & sekitarnya berkisar antara 109.0 s/d 109.3</small>
                    </div>
                </div>

                <div class="alert alert-light border d-flex align-items-start gap-2 mt-3 mb-0 py-2 px-3 rounded-3" style="font-size: 12px; background: #ffffff;">
                    <i class="fa-solid fa-circle-info text-primary mt-1 flex-shrink-0"></i>
                    <div>
                        <strong>Tips Koordinat Google Maps:</strong> Buka Google Maps di browser, klik kanan pada titik lokasi Anda, lalu klik angka koordinat di menu teratas untuk otomatis menyalin Latitude & Longitude.
                    </div>
                </div>
            </div>

            <div class='mb-3'><strong>Fasilitas</strong>
                <div class='d-flex flex-wrap gap-3 mt-2'>
                    @foreach ($facilities as $item)
                        <label><input type='checkbox' name='facilities[]' value='{{ $item->id }}'
                                @checked(in_array($item->id, old('facilities', $editing ? $tourism->facilities->pluck('id')->all() : [])))> {{ $item->name }}</label>
                    @endforeach
                </div>
            </div>
            <label class='me-3'><input type='checkbox' name='is_hidden_gem' value='1' @checked(old('is_hidden_gem', $tourism->tourism->is_hidden_gem ?? false))>
                Hidden gem</label><label><input type='checkbox' name='is_featured' value='1'
                    @checked(old('is_featured', $tourism->is_featured ?? false))> Featured</label>
            <div class='mt-3'><button class='btn btn-lokantara'>Simpan draft</button></div>
        </form>
    </x-content-card>

    @push('scripts')
    <script>
        function updateGmapsPreviewLink() {
            const lat = document.getElementById('coord_latitude')?.value?.trim();
            const lng = document.getElementById('coord_longitude')?.value?.trim();
            const btn = document.getElementById('gmaps-preview-btn');
            if (btn) {
                if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                    btn.href = `https://www.google.com/maps?q=${lat},${lng}`;
                    btn.classList.remove('d-none');
                    btn.classList.add('d-inline-flex');
                } else {
                    btn.classList.add('d-none');
                    btn.classList.remove('d-inline-flex');
                }
            }
        }

        function detectGPSLocation(btn) {
            if (!navigator.geolocation) {
                alert('Fitur Geolocation tidak didukung oleh browser Anda.');
                return;
            }
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mendeteksi...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('coord_latitude').value = position.coords.latitude.toFixed(7);
                    document.getElementById('coord_longitude').value = position.coords.longitude.toFixed(7);
                    updateGmapsPreviewLink();
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-check text-success"></i> Lokasi Terdeteksi!';
                    setTimeout(() => { btn.innerHTML = origHtml; }, 3000);
                },
                function(error) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                    let msg = 'Gagal mendeteksi lokasi GPS.';
                    if (error.code === error.PERMISSION_DENIED) {
                        msg = 'Izin akses lokasi ditolak oleh browser. Silakan izinkan akses lokasi atau masukkan koordinat secara manual.';
                    }
                    alert(msg);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }

        document.addEventListener('DOMContentLoaded', updateGmapsPreviewLink);
    </script>
    @endpush
@endsection
