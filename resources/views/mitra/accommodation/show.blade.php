@extends('layouts.mitra')

@section('title', $accommodation->name)
@section('page-title', $accommodation->name)
@section('page-description', 'Kelola properti, foto media, tipe kamar, harga per malam, dan kalender ketersediaan.')

@section('content')
<!-- Action Bar -->
<div class='d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded' style='background: var(--lokantara-surface); border: 1px solid var(--lokantara-border);'>
    <div class='d-flex align-items-center gap-2'>
        <x-status-badge :status='$accommodation->status' />
        <span class='text-muted'>|</span>
        <span class='fw-semibold'>📍 {{ $accommodation->region?->name ?? 'Tegal' }}</span>
        <span class='text-muted'>·</span>
        <span class='badge text-bg-light'>{{ str($accommodation->accommodation?->property_type ?? 'Hotel')->headline() }}</span>
    </div>

    <div class='d-flex align-items-center gap-2'>
        <a class='btn btn-sm btn-outline-lokantara fw-bold' href='{{ route('mitra.accommodation.edit', $accommodation) }}'>
            ✏️ Edit Properti
        </a>

        @if(in_array($accommodation->status, ['draft', 'rejected']))
            <form method='POST' action='{{ route('mitra.accommodation.submit', $accommodation) }}'>
                @csrf
                <button class='btn btn-sm btn-lokantara fw-bold'>
                    🚀 Ajukan Moderasi
                </button>
            </form>
        @endif

        @if(in_array($accommodation->status, ['draft', 'rejected', 'published']))
            <form method='POST' action='{{ route('mitra.accommodation.archive', $accommodation) }}'>
                @csrf
                <button class='btn btn-sm btn-outline-danger' onclick="return confirm('Apakah Anda yakin ingin mengarsipkan properti ini?')">
                    Arsipkan
                </button>
            </form>
        @endif
    </div>
</div>

<div class='row g-4'>
    <!-- Left Column: Property Preview (7 Cols) -->
    <div class='col-lg-7'>
        <x-content-card title='Preview Properti'>
            <h2 class='fs-4 fw-bold mb-2'>{{ $accommodation->name }}</h2>
            <p class='text-muted mb-3' style='font-size: 14px;'>
                {{ $accommodation->description ?: 'Deskripsi penginapan belum diisi.' }}
            </p>
            
            <div class='p-3 rounded mb-3' style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;'>
                <div class='mb-2'>
                    <strong>📍 Alamat:</strong> {{ $accommodation->address ?: 'Belum diisi' }}
                </div>
                <div class='row'>
                    <div class='col-6'>
                        <strong>🕒 Waktu Check-In:</strong> {{ $accommodation->accommodation?->check_in_time ?? '14:00' }} WIB
                    </div>
                    <div class='col-6'>
                        <strong>🕚 Waktu Check-Out:</strong> {{ $accommodation->accommodation?->check_out_time ?? '12:00' }} WIB
                    </div>
                </div>
            </div>

            <strong class='d-block mb-2' style='font-size: 13px;'>Fasilitas Properti:</strong>
            <div class='d-flex gap-2 flex-wrap'>
                @forelse($accommodation->facilities as $facility)
                    <span class='badge' style='background: #e2e8f0; color: #1e293b; padding: 6px 12px; font-weight: 500;'>
                        ✔ {{ $facility->name }}
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
            <form method='POST' enctype='multipart/form-data' action='{{ route('mitra.accommodation.media', $accommodation) }}' class='mb-3'>
                @csrf
                <div class='mb-2'>
                    <label class='form-label fw-semibold' style='font-size: 13px;'>Pilih Foto Hotel / Properti</label>
                    <input class='form-control form-control-sm' type='file' name='image' accept='image/jpeg,image/png,image/webp' required onchange='previewHotelMedia(this)'>
                </div>

                <!-- Live Preview Box -->
                <div id='hotel-live-preview-box' class='mb-2 p-2 rounded' style='display: none; background: var(--lokantara-background); border: 1px dashed var(--lokantara-primary); text-align: center;'>
                    <small class='text-muted d-block mb-1'>Preview foto yang akan diunggah:</small>
                    <img id='hotel-live-preview-img' src='' style='max-height: 140px; max-width: 100%; border-radius: 6px; object-fit: cover;'>
                </div>

                <div class='row g-2 mb-2'>
                    <div class='col-6'>
                        <label class='form-label' style='font-size: 12px;'>Peran Foto</label>
                        <select class='form-select form-select-sm' name='role' required>
                            <option value='cover'>⭐ Foto Cover Utama</option>
                            <option value='gallery'>🖼️ Galeri Properti</option>
                        </select>
                    </div>
                    <div class='col-6'>
                        <label class='form-label' style='font-size: 12px;'>Keterangan / Caption</label>
                        <input class='form-control form-control-sm' name='caption' placeholder='Opsional'>
                    </div>
                </div>

                <button class='btn btn-sm btn-lokantara w-100 fw-bold'>
                    📤 Unggah Foto Properti
                </button>
            </form>

            <hr class='my-3'>

            <!-- Saved Media Gallery Preview -->
            <div class='d-flex align-items-center justify-content-between mb-2'>
                <h6 class='fw-bold mb-0' style='font-size: 13px;'>Foto Tersimpan ({{ $accommodation->media->count() }})</h6>
            </div>

            @if($accommodation->media->isEmpty())
                <div class='p-3 text-center rounded' style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                    <span class='fs-3 d-block mb-1'>🏨</span>
                    <small class='text-muted'>Belum ada foto yang diunggah. Unggah minimal 1 Foto Cover agar dapat diajukan ke publik.</small>
                </div>
            @else
                <div class='row g-2'>
                    @foreach($accommodation->media as $media)
                        <div class='col-6'>
                            <div class='card h-100 border' style='border-radius: 8px; overflow: hidden;'>
                                <div style='height: 100px; background: #e2e8f0; position: relative;'>
                                    <img src='{{ asset('storage/' . $media->object_key) }}' alt='{{ $media->pivot->caption ?? $accommodation->name }}' style='width: 100%; height: 100%; object-fit: cover;'>
                                    <span class='badge {{ $media->pivot->role === 'cover' ? 'bg-warning text-dark' : 'bg-dark text-white' }}' style='position: absolute; top: 6px; left: 6px; font-size: 10px;'>
                                        {{ strtoupper($media->pivot->role) }}
                                    </span>
                                </div>
                                <div class='p-2' style='font-size: 11px;'>
                                    <span class='text-truncate d-block fw-semibold'>{{ $media->pivot->caption ?: $media->original_name }}</span>
                                    <small class='text-muted'>{{ number_format($media->size_bytes / 1024, 0) }} KB</small>
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
<x-content-card title='Tambah Tipe Kamar' class='mt-4'>
    <form method='POST' action='{{ route('mitra.accommodation.rooms.store', $accommodation) }}'>
        @csrf
        <div class='row g-3'>
            <div class='col-md-4'>
                <x-form-input name='name' label='Nama Kamar (cth: Deluxe King / Standard Room)' required />
            </div>
            <div class='col-md-4'>
                <x-form-input name='room_type' label='Tipe Kamar (cth: Deluxe, Suite, Standar)' required />
            </div>
            <div class='col-md-4'>
                <x-select name='kind' label='Jenis Unit' required>
                    <option value='room'>Kamar Tidur</option>
                    <option value='tent_plot'>Lahan Tenda / Glamping</option>
                </x-select>
            </div>
        </div>

        <div class='row g-3 mt-1'>
            <div class='col-md-2'>
                <x-form-input name='capacity_adults' type='number' label='Kapasitas Dewasa' value='2' required />
            </div>
            <div class='col-md-2'>
                <x-form-input name='capacity_children' type='number' label='Kapasitas Anak' value='1' required />
            </div>
            <div class='col-md-2'>
                <x-form-input name='total_units' type='number' label='Jumlah Kamar' value='1' required />
            </div>
            <div class='col-md-3'>
                <x-form-input name='nightly_price' type='number' label='Harga Sewa / Malam (Rp)' required />
            </div>
            <div class='col-md-3'>
                <x-select name='status' label='Status Kamar' required>
                    <option value='active'>Aktif</option>
                    <option value='draft'>Draft</option>
                </x-select>
            </div>
        </div>

        <button class='btn btn-lokantara mt-3 fw-bold'>+ Tambahkan Tipe Kamar</button>
    </form>
</x-content-card>

<!-- Room Table List -->
<x-table-wrapper title='Daftar Tipe Kamar' class='mt-4'>
    @if($accommodation->accommodation->rooms->isEmpty())
        <tbody>
            <tr>
                <td colspan='6'>
                    <x-empty-state title='Belum ada kamar dibuat' description='Tambahkan minimal satu kamar aktif sebelum mengajukan moderasi properti.' compact />
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
            @foreach($accommodation->accommodation->rooms as $room)
                <tr>
                    <td>
                        <strong class='d-block'>{{ $room->name }}</strong>
                        <small class='text-muted'>{{ str($room->room_type)->headline() }}</small>
                    </td>
                    <td>{{ $room->capacity_adults }} Dewasa, {{ $room->capacity_children }} Anak</td>
                    <td class='fw-bold' style='color: var(--lokantara-primary);'>
                        Rp {{ number_format($room->offer->price, 0, ',', '.') }}
                    </td>
                    <td>{{ $room->total_units }} Unit</td>
                    <td><x-status-badge :status='$room->status' /></td>
                    <td class='text-end'>
                        <a class='btn btn-sm btn-outline-lokantara' href='{{ route('mitra.accommodation.rooms.edit', [$accommodation, $room]) }}'>
                            ✏️ Edit
                        </a>
                        <a class='btn btn-sm btn-outline-lokantara' href='{{ route('mitra.accommodation.rooms.calendar', [$accommodation, $room]) }}'>
                            📅 Kalender
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
        @foreach($report->actions as $action)
            <div class='border-bottom py-2'>
                <strong>{{ str($action->action_type)->headline() }}</strong> · {{ $action->created_at?->format('d M Y H:i') }}
                <div>{{ $action->notes }}</div>
            </div>
        @endforeach
    @empty
        <x-empty-state title='Belum ada riwayat' description='Riwayat muncul setelah properti diajukan ke admin.' compact />
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
