@extends('layouts.mitra') @section('title', $title) @section('page-title', isset($item) ? 'Edit ' . $title : 'Tambah ' . $title)
@section('page-description', 'Simpan sebagai draft sebelum diajukan.')
@section('content')@php($detail = isset($item) ? $item->{$domain} : null)<form class="content-card" method="POST"
        action="{{ isset($item) ? route($routePrefix . '.update', $item) : route($routePrefix . '.store') }}">
        @csrf @if (isset($item))
            @method('PUT')
        @endif
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Nama</label><input class="form-control" name="name"
                    value="{{ old('name', $item->name ?? '') }}" required></div>
            <div class="col-md-4"><label class="form-label">Slug</label><input class="form-control" name="slug"
                    value="{{ old('slug', $item->slug ?? '') }}" required></div>
            <div class="col-12"><label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="description" required>{{ old('description', $item->description ?? '') }}</textarea>
            </div>
            <div class="col-12"><label class="form-label">Alamat</label><input class="form-control" name="address"
                    value="{{ old('address', $item->address ?? '') }}" required></div>
            <div class="col-md-12"><label class="form-label fw-bold">Wilayah / Kecamatan <span class="text-danger">*</span></label><select class="form-select" name="region_id"
                    required>
                    <option value="">Pilih Wilayah</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @selected(old('region_id', $item->region_id ?? null) == $region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- GPS Coordinates Section -->
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-3.5 mb-2 mt-1" style="background: #f8fafc; border: 1px solid #e2e8f0 !important;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div>
                            <h6 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: #0f172a; font-size: 15px;">
                                <i class="fa-solid fa-location-dot text-danger"></i> Titik Koordinat Lokasi (GPS)
                            </h6>
                            <small class="text-muted d-block" style="font-size: 12px;">
                                Tentukan koordinat Latitude & Longitude agar {{ strtolower($title) }} tampil akurat pada peta dan navigasi pengunjung.
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
                                    placeholder="Contoh: -6.8797000" value="{{ old('latitude', $item?->location?->latitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
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
                                    placeholder="Contoh: 109.1256000" value="{{ old('longitude', $item?->location?->longitude ?? '') }}" required oninput="updateGmapsPreviewLink()">
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
            </div>

            @if ($domain === 'culinary')
                <div class="col-md-4"><label class="form-label fw-bold">Jenis venue</label><select class="form-select" name="venue_type">
                        <option value="restaurant">Restaurant</option>
                        <option value="cafe">Cafe</option>
                        <option value="street_food">Street food</option>
                    </select></div>
                <div class="col-md-4"><label class="form-label fw-bold">Telepon</label><input class="form-control" name="phone"
                        value="{{ old('phone', $detail?->phone) }}"></div>
                <div class="col-md-4 form-check mt-4 pt-2"><input type="hidden" name="accepts_reservations"
                        value="0"><input class="form-check-input" type="checkbox" name="accepts_reservations"
                        value="1" @checked(old('accepts_reservations', $detail?->accepts_reservations))><label class="form-check-label fw-semibold">Menerima reservasi</label></div>
            @endif
            @if ($domain === 'event')
                <div class="col-md-4"><label class="form-label fw-bold">Jenis event</label><input class="form-control" name="event_type"
                        value="{{ old('event_type', $detail?->event_type) }}" required></div>
                <div class="col-md-4"><label class="form-label fw-bold">Mulai</label><input type="datetime-local" class="form-control" name="starts_at"
                        value="{{ old('starts_at', $detail?->starts_at?->format('Y-m-d\TH:i')) }}" required></div>
                <div class="col-md-4"><label class="form-label fw-bold">Selesai</label><input type="datetime-local" class="form-control" name="ends_at"
                        value="{{ old('ends_at', $detail?->ends_at?->format('Y-m-d\TH:i')) }}" required></div>
            @endif
            @if ($domain === 'rental')
                @foreach (['vehicle_type' => 'Jenis', 'brand' => 'Merek', 'model' => 'Model', 'plate_number' => 'Nomor polisi', 'seats' => 'Kursi', 'deposit_amount' => 'Deposit'] as $name => $label)
                    <div class="col-md-4"><label class="form-label fw-bold">{{ $label }}</label><input class="form-control"
                            name="{{ $name }}" value="{{ old($name, $detail?->{$name}) }}" required></div>
                @endforeach
                <input type="hidden" name="self_drive_available" value="1"><input type="hidden"
                    name="driver_available" value="0">
            @endif
        </div><button class="btn btn-lokantara mt-4">Simpan draft</button>
</form>

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
