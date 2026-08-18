@extends('layouts.mitra')

@section('title', $accommodation->name)
@section('page-title', $accommodation->name)
@section('page-description', 'Kelola properti, foto media, tipe kamar, harga per malam, dan kalender ketersediaan.')

@section('content')
    <!-- Action Bar -->
    <div class='d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded-4'
        style='background: #ffffff; border: 1px solid var(--lokantara-border, #e2e8f0); box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);'>
        <div class='d-flex align-items-center gap-2'>
            <x-status-badge :status='$accommodation->status' />
            <span class='text-muted'>|</span>
            <span class='badge text-bg-light border'>
                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                {{ $accommodation->region?->name ?? 'Tegal' }}
            </span>
            <span class='badge bg-primary-subtle text-primary border'>
                <i class="fa-solid fa-hotel me-1"></i>
                {{ str($accommodation->accommodation?->property_type ?? 'Hotel')->headline() }}
            </span>
            @if($accommodation->accommodation?->star_rating)
                <span class="badge bg-warning-subtle text-warning-emphasis border">
                    <i class="fa-solid fa-star text-warning me-0.5"></i>
                    {{ $accommodation->accommodation->star_rating }} Bintang
                </span>
            @endif
        </div>

        <div class='d-flex align-items-center gap-2'>
            <a class='btn btn-sm btn-outline-lokantara rounded-pill px-3 fw-bold'
                href='{{ route('mitra.accommodation.edit', $accommodation) }}'>
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Properti
            </a>

            @if (in_array($accommodation->status, ['draft', 'rejected']))
                <form method='POST' action='{{ route('mitra.accommodation.submit', $accommodation) }}'>
                    @csrf
                    <button class='btn btn-sm btn-lokantara rounded-pill px-3 fw-bold'>
                        <i class="fa-solid fa-paper-plane me-1"></i> Ajukan Moderasi
                    </button>
                </form>
            @endif

            @if (in_array($accommodation->status, ['draft', 'rejected', 'published']))
                <form method='POST' action='{{ route('mitra.accommodation.archive', $accommodation) }}'>
                    @csrf
                    <button class='btn btn-sm btn-outline-danger rounded-pill px-3'
                        onclick="return confirm('Apakah Anda yakin ingin mengarsipkan properti ini?')">
                        <i class="fa-solid fa-box-archive me-1"></i> Arsipkan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class='row g-4'>
        <!-- Left Column: Property Preview (7 Cols) -->
        <div class='col-lg-7'>
            <x-content-card title='Preview Properti'>
                <h2 class='fs-4 fw-bold mb-2 text-dark'>{{ $accommodation->name }}</h2>
                <p class='text-muted mb-3' style='font-size: 14px;'>
                    {{ $accommodation->description ?: 'Deskripsi penginapan belum diisi.' }}
                </p>

                <div class='p-3 rounded-3 mb-3'
                    style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;'>
                    <div class='mb-2'>
                        <strong class="text-dark"><i class="fa-solid fa-map-pin text-primary me-1"></i> Alamat:</strong> {{ $accommodation->address ?: 'Belum diisi' }}
                    </div>
                    <div class='row g-2'>
                        <div class='col-6'>
                            <span class="text-muted"><i class="fa-regular fa-clock text-primary me-1"></i> Waktu Check-In:</span>
                            <strong class="text-dark ms-1">{{ $accommodation->accommodation?->check_in_time ? \Carbon\Carbon::parse($accommodation->accommodation->check_in_time)->format('H:i') : '14:00' }} WIB</strong>
                        </div>
                        <div class='col-6'>
                            <span class="text-muted"><i class="fa-regular fa-clock text-warning me-1"></i> Waktu Check-Out:</span>
                            <strong class="text-dark ms-1">{{ $accommodation->accommodation?->check_out_time ? \Carbon\Carbon::parse($accommodation->accommodation->check_out_time)->format('H:i') : '12:00' }} WIB</strong>
                        </div>
                    </div>
                    @if ($accommodation->location)
                        <div class='mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2'>
                            <div>
                                <strong class="text-dark"><i class="fa-solid fa-crosshairs text-success me-1"></i> Koordinat GPS:</strong> {{ $accommodation->location->latitude }}, {{ $accommodation->location->longitude }}
                            </div>
                            <a href="https://www.google.com/maps?q={{ $accommodation->location->latitude }},{{ $accommodation->location->longitude }}" target="_blank" rel="noopener noreferrer" class="badge text-bg-light border text-decoration-none py-1.5 px-2">
                                <i class="fa-solid fa-arrow-up-right-from-square text-success me-1"></i> Buka di Google Maps
                            </a>
                        </div>
                    @endif
                </div>

                <strong class='d-block mb-2 text-dark' style='font-size: 13px;'>Fasilitas Properti:</strong>
                <div class='d-flex gap-2 flex-wrap'>
                    @forelse($accommodation->facilities as $facility)
                        <span class='badge border'
                            style='background: #f8fafc; color: #334155; padding: 6px 12px; font-weight: 500;'>
                            <i class="fa-solid fa-check text-success me-1"></i> {{ $facility->name }}
                        </span>
                    @empty
                        <span class='text-muted' style='font-size: 13px;'>Belum ada fasilitas dipilih.</span>
                    @endforelse
                </div>
            </x-content-card>
        </div>

        <!-- Right Column: Media Upload & Gallery Preview (5 Cols) -->
        <div class='col-lg-5'>
            <x-content-card title='Media & Foto Properti'>
                <!-- Upload Form -->
                <form method='POST' enctype='multipart/form-data'
                    action='{{ route('mitra.accommodation.media', $accommodation) }}' class='mb-3'>
                    @csrf
                    <div class='mb-2'>
                        <label class='form-label fw-semibold text-dark' style='font-size: 13px;'>Pilih Foto Hotel / Properti</label>
                        <input class='form-control form-control-sm' type='file' name='image'
                            accept='image/jpeg,image/png,image/webp' required onchange='previewHotelMedia(this)'>
                    </div>

                    <!-- Live Preview Box -->
                    <div id='hotel-live-preview-box' class='mb-2 p-2 rounded'
                        style='display: none; background: var(--lokantara-background); border: 1px dashed var(--lokantara-primary); text-align: center;'>
                        <small class='text-muted d-block mb-1'>Preview foto yang akan diunggah:</small>
                        <img id='hotel-live-preview-img' src=''
                            style='max-height: 140px; max-width: 100%; border-radius: 6px; object-fit: cover;'>
                    </div>

                    <div class='row g-2 mb-2'>
                        <div class='col-6'>
                            <label class='form-label' style='font-size: 12px;'>Peran Foto</label>
                            <select class='form-select form-select-sm' name='role' required>
                                <option value='cover'>Foto Cover Utama</option>
                                <option value='gallery'>Galeri Properti</option>
                            </select>
                        </div>
                        <div class='col-6'>
                            <label class='form-label' style='font-size: 12px;'>Keterangan / Caption</label>
                            <input class='form-control form-control-sm' name='caption' placeholder='Opsional'>
                        </div>
                    </div>

                    <button class='btn btn-sm btn-lokantara w-100 fw-bold'>
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Unggah Foto Properti
                    </button>
                </form>

                <hr class='my-3'>

                <!-- Saved Media Gallery Preview -->
                <div class='d-flex align-items-center justify-content-between mb-2'>
                    <h6 class='fw-bold mb-0' style='font-size: 13px;'>Foto Tersimpan ({{ $accommodation->media->count() }})</h6>
                </div>

                @if ($accommodation->media->isEmpty())
                    <div class='p-3 text-center rounded'
                        style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                        <i class="fa-solid fa-images fs-2 text-muted mb-1 d-block"></i>
                        <small class='text-muted'>Belum ada foto yang diunggah. Unggah minimal 1 Foto Cover agar dapat
                            diajukan ke publik.</small>
                    </div>
                @else
                    <div class='row g-2'>
                        @foreach ($accommodation->media as $media)
                            <div class='col-6'>
                                <div class='card h-100 border' style='border-radius: 8px; overflow: hidden;'>
                                    <div style='height: 100px; background: #e2e8f0; position: relative;'>
                                        <img src='{{ asset('storage/' . $media->object_key) }}'
                                            alt='{{ $media->pivot->caption ?? $accommodation->name }}'
                                            style='width: 100%; height: 100%; object-fit: cover;'>
                                        <span
                                            class='badge {{ $media->pivot->role === 'cover' ? 'bg-primary' : 'bg-dark text-white' }}'
                                            style='position: absolute; top: 6px; left: 6px; font-size: 10px;'>
                                            {{ strtoupper($media->pivot->role) }}
                                        </span>
                                    </div>
                                    <div class='p-2' style='font-size: 11px;'>
                                        <span
                                            class='text-truncate d-block fw-semibold'>{{ $media->pivot->caption ?: $media->original_name }}</span>
                                        <small class='text-muted'>{{ number_format($media->size_bytes / 1024, 0) }}
                                            KB</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-content-card>
        </div>
    </div>

    <!-- Add Room Card -->
    <x-content-card title='Tambah Tipe Kamar Baru' class='mt-4'>
        <form method='POST' action='{{ route('mitra.accommodation.rooms.store', $accommodation) }}'>
            @csrf
            <div class='row g-3'>
                <div class='col-md-4'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Nama Kamar <span class='text-danger'>*</span></label>
                    <input type='text' name='name' class='form-control' placeholder='Contoh: Deluxe King Room' required>
                </div>
                <div class='col-md-4'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Tipe / Kategori Kamar <span class='text-danger'>*</span></label>
                    <input type='text' name='room_type' class='form-control' placeholder='Contoh: Deluxe / Standard / Suite' required>
                </div>
                <div class='col-md-4'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Jenis Unit <span class='text-danger'>*</span></label>
                    <select name='kind' class='form-select' required>
                        <option value='room'>Kamar Bangunan (Room)</option>
                        <option value='tent_plot'>Lahan Tenda / Glamping</option>
                    </select>
                </div>
            </div>

            <div class='row g-3 mt-1'>
                <div class='col-md-2'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Dewasa <span class='text-danger'>*</span></label>
                    <div class='input-group'>
                        <input type='number' min='1' max='100' name='capacity_adults' class='form-control' value='2' required>
                        <span class='input-group-text bg-white' style='font-size: 11px;'>Org</span>
                    </div>
                </div>
                <div class='col-md-2'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Anak <span class='text-danger'>*</span></label>
                    <div class='input-group'>
                        <input type='number' min='0' max='100' name='capacity_children' class='form-control' value='1' required>
                        <span class='input-group-text bg-white' style='font-size: 11px;'>Anak</span>
                    </div>
                </div>
                <div class='col-md-2'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Jumlah Unit <span class='text-danger'>*</span></label>
                    <div class='input-group'>
                        <input type='number' min='1' max='10000' name='total_units' class='form-control' value='1' required>
                        <span class='input-group-text bg-white' style='font-size: 11px;'>Unit</span>
                    </div>
                </div>
                <div class='col-md-3'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Tarif per Malam (Rp) <span class='text-danger'>*</span></label>
                    <div class='input-group'>
                        <span class='input-group-text bg-white fw-bold text-success'>Rp</span>
                        <input type='number' step='0.01' min='0' name='nightly_price' class='form-control' placeholder='Contoh: 350000' required>
                    </div>
                </div>
                <div class='col-md-3'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Status Publikasi <span class='text-danger'>*</span></label>
                    <select name='status' class='form-select' required>
                        <option value='active'>🟢 Aktif (Tersedia)</option>
                        <option value='draft'>🟡 Draft (Disembunyikan)</option>
                    </select>
                </div>
            </div>

            <button class='btn btn-lokantara mt-3 fw-bold rounded-pill px-4'>
                <i class="fa-solid fa-plus me-1"></i> Tambahkan Tipe Kamar
            </button>
        </form>
    </x-content-card>

    <!-- Room Table List -->
    <x-table-wrapper title='Daftar Tipe Kamar' class='mt-4'>
        @if ($accommodation->accommodation->rooms->isEmpty())
            <tbody>
                <tr>
                    <td colspan='6'>
                        <x-empty-state title='Belum ada kamar dibuat'
                            description='Tambahkan minimal satu kamar aktif sebelum mengajukan moderasi properti.'
                            compact />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Nama Kamar</th>
                    <th>Kapasitas Tamu</th>
                    <th>Harga / Malam</th>
                    <th>Total Unit</th>
                    <th>Status</th>
                    <th class='text-end'>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accommodation->accommodation->rooms as $room)
                    <tr>
                        <td>
                            <strong class='d-block text-dark'>{{ $room->name }}</strong>
                            <small class='text-muted'>{{ str($room->room_type)->headline() }}</small>
                        </td>
                        <td>
                            <span class="badge text-bg-light border">
                                <i class="fa-solid fa-users text-primary me-1"></i>
                                {{ $room->capacity_adults }} Dewasa, {{ $room->capacity_children }} Anak
                            </span>
                        </td>
                        <td class='fw-bold text-success'>
                            Rp {{ number_format($room->offer->price, 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border">
                                {{ $room->total_units }} Unit
                            </span>
                        </td>
                        <td><x-status-badge :status='$room->status' /></td>
                        <td class='text-end'>
                            <a class='btn btn-sm btn-outline-lokantara rounded-pill px-2.5 py-1 fw-semibold'
                                href='{{ route('mitra.accommodation.rooms.edit', [$accommodation, $room]) }}' style='font-size: 12px;'>
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </a>
                            <a class='btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold ms-1'
                                href='{{ route('mitra.accommodation.rooms.calendar', [$accommodation, $room]) }}' style='font-size: 12px;'>
                                <i class="fa-solid fa-calendar-days me-1"></i> Kalender
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
    </x-table-wrapper>

    <!-- Moderation History -->
    <x-content-card title='Riwayat Moderasi' class='mt-4'>
        @forelse($accommodation->moderationReports as $report)
            @foreach ($report->actions as $action)
                <div class='border-bottom py-2'>
                    <strong>{{ str($action->action_type)->headline() }}</strong> ·
                    {{ $action->created_at?->format('d M Y H:i') }}
                    <div>{{ $action->notes }}</div>
                </div>
            @endforeach
        @empty
            <x-empty-state title='Belum ada riwayat' description='Riwayat muncul setelah properti diajukan ke admin.'
                compact />
        @endforelse
    </x-content-card>

    <script>
        function previewHotelMedia(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewBox = document.getElementById('hotel-live-preview-box');
                    const previewImg = document.getElementById('hotel-live-preview-img');
                    if (previewBox && previewImg) {
                        previewImg.src = e.target.result;
                        previewBox.style.display = 'block';
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
