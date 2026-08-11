@extends('layouts.mitra')

@section('title', $title . ' — ' . $item->name)
@section('page-title', $item->name)
@section('page-description', 'Kelola ' . strtolower($title) . ', foto media, menu/tarif, dan status moderasi.')

@section('content')
<!-- Action Bar -->
<div class='d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded' style='background: var(--lokantara-surface); border: 1px solid var(--lokantara-border);'>
    <div class='d-flex align-items-center gap-2'>
        <x-status-badge :status='$item->status' />
        <span class='text-muted'>|</span>
        <span class='fw-semibold'>📍 {{ $item->region?->name ?? 'Tegal' }}</span>
        <span class='text-muted'>·</span>
        <span class='badge text-bg-light'>{{ $item->category?->name ?? $title }}</span>
    </div>

    <div class='d-flex align-items-center gap-2'>
        <a class='btn btn-sm btn-outline-lokantara fw-bold' href='{{ route($routePrefix . '.edit', $item) }}'>
            ✏️ Edit Data
        </a>

        @if(in_array($item->status, ['draft', 'rejected']))
            <form method='POST' action='{{ route($routePrefix . '.submit', $item) }}'>
                @csrf
                <button class='btn btn-sm btn-lokantara fw-bold'>
                    🚀 Ajukan Moderasi
                </button>
            </form>
        @endif

        @if(in_array($item->status, ['draft', 'rejected', 'published']))
            <form method='POST' action='{{ route($routePrefix . '.archive', $item) }}'>
                @csrf
                <button class='btn btn-sm btn-outline-danger' onclick="return confirm('Apakah Anda yakin ingin mengarsipkan layanan ini?')">
                    Arsipkan
                </button>
            </form>
        @endif
    </div>
</div>

<div class='row g-4'>
    <!-- Left Column: Details (7 Cols) -->
    <div class='col-lg-7'>
        <x-content-card title='Informasi Layanan'>
            <h2 class='fs-4 fw-bold mb-2'>{{ $item->name }}</h2>
            <p class='text-muted mb-3' style='font-size: 14px;'>
                {{ $item->description ?: 'Deskripsi belum diisi.' }}
            </p>

            <div class='p-3 rounded mb-3' style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;'>
                <div class='mb-2'>
                    <strong>📍 Alamat:</strong> {{ $item->address ?: 'Belum diisi' }}
                </div>
                @if($item->location)
                    <div>
                        <strong>🌐 Koordinat GPS:</strong> {{ $item->location->latitude }}, {{ $item->location->longitude }}
                    </div>
                @endif
            </div>

            @if($domain === 'culinary' && $item->culinary)
                <div class='d-flex align-items-center gap-2 mb-3'>
                    <span class='badge {{ $item->culinary->accepts_reservations ? 'bg-success' : 'bg-secondary' }}'>
                        {{ $item->culinary->accepts_reservations ? '✔ Menerima Reservasi Meja' : '❌ Tidak Menerima Reservasi' }}
                    </span>
                    @if($item->culinary->price_range)
                        <span class='badge bg-light text-dark border'>Kisaran Harga: {{ $item->culinary->price_range }}</span>
                    @endif
                </div>
            @endif
        </x-content-card>

        <!-- Domain Specific Management -->
        @if($domain === 'culinary' && $item->culinary)
            <!-- Culinary Menu Categories & Items -->
            <x-content-card title='Buku Menu Makanan & Minuman' class='mt-4'>
                <form method='POST' action='{{ route($routePrefix . '.categories.store', $item) }}' class='mb-4'>
                    @csrf
                    <label class='form-label fw-semibold' style='font-size: 13px;'>Tambah Kategori Menu Baru</label>
                    <div class='input-group'>
                        <input class='form-control' name='name' placeholder='Cth: Makanan Utama, Minuman Segar, Paket Hemat' required>
                        <button class='btn btn-lokantara fw-bold'>+ Tambah Kategori</button>
                    </div>
                </form>

                @forelse($item->culinary->menuCategories as $category)
                    <div class='p-3 rounded mb-3' style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                        <h5 class='fw-bold mb-3' style='color: var(--lokantara-primary);'>📋 Kategori: {{ $category->name }}</h5>

                        <form method='POST' action='{{ route($routePrefix . '.items.store', [$item, $category]) }}' class='mb-3'>
                            @csrf
                            <div class='row g-2'>
                                <div class='col-md-5'>
                                    <input class='form-control form-control-sm' name='name' placeholder='Nama Menu (cth: Sate Kambing 10 Tusuk)' required>
                                </div>
                                <div class='col-md-4'>
                                    <input class='form-control form-control-sm' name='price' type='number' min='0' placeholder='Harga (Rp)' required>
                                </div>
                                <div class='col-md-3'>
                                    <button class='btn btn-sm btn-outline-lokantara w-100 fw-bold'>+ Menu</button>
                                </div>
                            </div>
                        </form>

                        @if($category->items->isNotEmpty())
                            <div class='table-responsive'>
                                <table class='table table-sm table-bordered bg-white mb-0'>
                                    <thead class='table-light'>
                                        <tr>
                                            <th>Nama Menu</th>
                                            <th>Harga</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->items as $menuItem)
                                            <tr>
                                                <td>
                                                    <strong>{{ $menuItem->name }}</strong>
                                                    @if($menuItem->is_featured)
                                                        <span class='badge bg-warning text-dark' style='font-size: 10px;'>Unggulan</span>
                                                    @endif
                                                </td>
                                                <td>Rp {{ number_format($menuItem->price, 0, ',', '.') }}</td>
                                                <td><x-status-badge :status='$menuItem->status' /></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <small class='text-muted'>Belum ada menu di kategori ini.</small>
                        @endif
                    </div>
                @empty
                    <x-empty-state title='Belum ada kategori menu' description='Buat kategori menu pertama Anda di atas.' compact />
                @endforelse
            </x-content-card>
        @endif

        @if($domain === 'event' && $item->event)
            <x-content-card title='Tipe Tiket Event' class='mt-4'>
                <form method='POST' action='{{ route($routePrefix . '.ticket-types.store', $item) }}' class='mb-3'>
                    @csrf
                    <div class='row g-2'>
                        <div class='col-md-5'>
                            <input class='form-control form-control-sm' name='name' required placeholder='Nama Tiket (cth: Presale / VIP)'>
                        </div>
                        <div class='col-md-4'>
                            <input class='form-control form-control-sm' name='price' type='number' min='0' placeholder='Harga (Rp)' required>
                        </div>
                        <div class='col-md-3'>
                            <input class='form-control form-control-sm' name='quota' type='number' min='1' placeholder='Kuota' required>
                        </div>
                    </div>
                    <button class='btn btn-sm btn-lokantara mt-2 fw-bold'>+ Tambah Tiket Event</button>
                </form>

                @if($item->event->ticketTypes->isNotEmpty())
                    <div class='table-responsive'>
                        <table class='table table-sm table-bordered bg-white'>
                            <thead class='table-light'>
                                <tr>
                                    <th>Nama Tiket</th>
                                    <th>Harga</th>
                                    <th>Kuota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->event->ticketTypes as $type)
                                    <tr>
                                        <td><strong>{{ $type->name }}</strong></td>
                                        <td>Rp {{ number_format($type->offer->price, 0, ',', '.') }}</td>
                                        <td>{{ $type->quota }} tiket</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-content-card>
        @endif

        @if($domain === 'rental' && $item->rentalVehicle)
            <x-content-card title='Tarif Sewa Armada' class='mt-4'>
                <form method='POST' action='{{ route($routePrefix . '.rates.store', $item) }}' class='mb-3'>
                    @csrf
                    <div class='row g-2'>
                        <div class='col-md-6'>
                            <select class='form-select form-select-sm' name='drive_mode'>
                                <option value='self_drive'>Lepas Kunci (Self Drive)</option>
                                <option value='with_driver'>Dengan Sopir (With Driver)</option>
                            </select>
                        </div>
                        <input type='hidden' name='duration_unit' value='day'>
                        <input type='hidden' name='duration_value' value='1'>
                        <div class='col-md-6'>
                            <input class='form-control form-control-sm' name='price' type='number' min='0' placeholder='Tarif per Hari (Rp)' required>
                        </div>
                    </div>
                    <button class='btn btn-sm btn-lokantara mt-2 fw-bold'>+ Tambah Tarif Sewa</button>
                </form>

                @if($item->rentalVehicle->rates->isNotEmpty())
                    <div class='table-responsive'>
                        <table class='table table-sm table-bordered bg-white'>
                            <thead class='table-light'>
                                <tr>
                                    <th>Mode Sewa</th>
                                    <th>Durasi</th>
                                    <th>Tarif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->rentalVehicle->rates as $rate)
                                    <tr>
                                        <td><strong>{{ str($rate->drive_mode)->headline() }}</strong></td>
                                        <td>{{ $rate->duration_value }} {{ str($rate->duration_unit)->headline() }}</td>
                                        <td class='fw-bold'>Rp {{ number_format($rate->offer->price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-content-card>
        @endif
    </div>

    <!-- Right Column: Media Upload & Gallery Preview (5 Cols) -->
    <div class='col-lg-5'>
        <x-content-card title='Media & Foto Layanan'>
            <!-- Upload Form -->
            <form method='POST' enctype='multipart/form-data' action='{{ route('mitra.catalog.media', ['domain' => $domain, 'entity' => $item]) }}' class='mb-3'>
                @csrf
                <div class='mb-2'>
                    <label class='form-label fw-semibold' style='font-size: 13px;'>Pilih File Foto</label>
                    <input class='form-control form-control-sm' type='file' name='image' accept='image/jpeg,image/png,image/webp' required onchange='previewDomainMedia(this)'>
                </div>

                <!-- Live Preview Box -->
                <div id='domain-live-preview-box' class='mb-2 p-2 rounded' style='display: none; background: var(--lokantara-background); border: 1px dashed var(--lokantara-primary); text-align: center;'>
                    <small class='text-muted d-block mb-1'>Preview foto yang akan diunggah:</small>
                    <img id='domain-live-preview-img' src='' style='max-height: 140px; max-width: 100%; border-radius: 6px; object-fit: cover;'>
                </div>

                <div class='row g-2 mb-2'>
                    <div class='col-6'>
                        <label class='form-label' style='font-size: 12px;'>Peran Foto</label>
                        <select class='form-select form-select-sm' name='role' required>
                            <option value='cover'>⭐ Foto Cover Utama</option>
                            <option value='gallery'>🖼️ Galeri Foto</option>
                        </select>
                    </div>
                    <div class='col-6'>
                        <label class='form-label' style='font-size: 12px;'>Keterangan / Caption</label>
                        <input class='form-control form-control-sm' name='caption' placeholder='Opsional'>
                    </div>
                </div>

                <button class='btn btn-sm btn-lokantara w-100 fw-bold'>
                    📤 Unggah Foto
                </button>
            </form>

            <hr class='my-3'>

            <!-- Saved Media Gallery Preview -->
            <div class='d-flex align-items-center justify-content-between mb-2'>
                <h6 class='fw-bold mb-0' style='font-size: 13px;'>Foto Tersimpan ({{ $item->media->count() }})</h6>
            </div>

            @if($item->media->isEmpty())
                <div class='p-3 text-center rounded' style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                    <span class='fs-3 d-block mb-1'>📷</span>
                    <small class='text-muted'>Belum ada foto yang diunggah. Unggah minimal 1 Foto Cover agar dapat diajukan ke publik.</small>
                </div>
            @else
                <div class='row g-2'>
                    @foreach($item->media as $media)
                        <div class='col-6'>
                            <div class='card h-100 border' style='border-radius: 8px; overflow: hidden;'>
                                <div style='height: 100px; background: #e2e8f0; position: relative;'>
                                    <img src='{{ asset('storage/' . $media->object_key) }}' alt='{{ $media->pivot->caption ?? $item->name }}' style='width: 100%; height: 100%; object-fit: cover;'>
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

<script>
function previewDomainMedia(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewBox = document.getElementById('domain-live-preview-box');
            const previewImg = document.getElementById('domain-live-preview-img');
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
