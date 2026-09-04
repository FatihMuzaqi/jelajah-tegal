@extends('layouts.mitra')

@php
    $domainIcon = match($domain) {
        'culinary' => 'fa-solid fa-utensils',
        'event' => 'fa-solid fa-calendar-days',
        'rental' => 'fa-solid fa-car',
        default => 'fa-solid fa-layer-group',
    };
@endphp

@section('title', $title . ' — ' . $item->name)
@section('page-title', $item->name)
@section('page-description', 'Kelola data layanan ' . strtolower($title) . ', galeri foto, buku menu/tarif/tiket, dan status moderasi.')

@section('content')
    @if (session('status'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-check text-success fs-5"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                <strong class="text-danger">Pengajuan Moderasi Belum Dapat Diproses:</strong>
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Action Bar -->
    <div class='d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded-4'
        style='background: #ffffff; border: 1px solid var(--lokantara-border, #e2e8f0); box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);'>
        <div class='d-flex align-items-center gap-2'>
            <x-status-badge :status='$item->status' />
            <span class='text-muted'>|</span>
            <span class='badge text-bg-light border'>
                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                {{ $item->region?->name ?? 'Tegal' }}
            </span>
            <span class='badge bg-secondary-subtle text-secondary border'>
                <i class="{{ $domainIcon }} me-1"></i>
                {{ $item->category?->name ?? $title }}
            </span>
        </div>

        <div class='d-flex align-items-center gap-2'>
            <a class='btn btn-sm btn-outline-lokantara rounded-pill px-3 fw-bold' href='{{ route($routePrefix . '.edit', $item) }}'>
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Data
            </a>

            @if (in_array($item->status, ['draft', 'rejected']))
                <form method='POST' action='{{ route($routePrefix . '.submit', $item) }}'>
                    @csrf
                    <button class='btn btn-sm btn-lokantara rounded-pill px-3 fw-bold'>
                        <i class="fa-solid fa-paper-plane me-1"></i> Ajukan Moderasi
                    </button>
                </form>
            @endif

            @if (in_array($item->status, ['draft', 'rejected', 'published']))
                <form method='POST' action='{{ route($routePrefix . '.archive', $item) }}'>
                    @csrf
                    <button class='btn btn-sm btn-outline-danger rounded-pill px-3'
                        onclick="return confirm('Apakah Anda yakin ingin mengarsipkan layanan ini?')">
                        <i class="fa-solid fa-box-archive me-1"></i> Arsipkan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class='row g-4'>
        <!-- Left Column: Details (7 Cols) -->
        <div class='col-lg-7'>
            <x-content-card title='Informasi Layanan'>
                <h2 class='fs-4 fw-bold mb-2 text-dark'>{{ $item->name }}</h2>
                <p class='text-muted mb-3' style='font-size: 14px;'>
                    {{ $item->description ?: 'Deskripsi belum diisi.' }}
                </p>

                <div class='p-3 rounded-3 mb-3'
                    style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;'>
                    <div class='mb-2'>
                        <strong class="text-dark"><i class="fa-solid fa-map-pin text-primary me-1"></i> Alamat:</strong> {{ $item->address ?: 'Belum diisi' }}
                    </div>
                    @if ($item->location)
                        <div class='mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2'>
                            <div>
                                <strong class="text-dark"><i class="fa-solid fa-crosshairs text-success me-1"></i> Koordinat GPS:</strong> {{ $item->location->latitude }}, {{ $item->location->longitude }}
                            </div>
                            <a href="https://www.google.com/maps?q={{ $item->location->latitude }},{{ $item->location->longitude }}" target="_blank" rel="noopener noreferrer" class="badge text-bg-light border text-decoration-none py-1.5 px-2">
                                <i class="fa-solid fa-arrow-up-right-from-square text-success me-1"></i> Buka di Google Maps
                            </a>
                        </div>
                    @endif
                </div>

                @if ($domain === 'culinary' && $item->culinary)
                    <div class='d-flex align-items-center gap-2 mb-3'>
                        <span class='badge {{ $item->culinary->accepts_reservations ? 'bg-success-subtle text-success border border-success' : 'bg-secondary-subtle text-secondary border' }}'>
                            <i class="fa-solid {{ $item->culinary->accepts_reservations ? 'fa-check' : 'fa-xmark' }} me-1"></i>
                            {{ $item->culinary->accepts_reservations ? 'Menerima Reservasi Meja' : 'Tidak Menerima Reservasi' }}
                        </span>
                        @if ($item->culinary->price_range)
                            <span class='badge bg-light text-dark border'>
                                <i class="fa-solid fa-money-bill-wave text-success me-1"></i>
                                Kisaran: {{ $item->culinary->price_range }}
                            </span>
                        @endif
                    </div>
                @endif
            </x-content-card>

            <!-- Domain Specific Management -->
            @if ($domain === 'culinary' && $item->culinary)
                <!-- Culinary Menu Categories & Items -->
                <x-content-card title='Buku Menu Makanan & Minuman' class='mt-4'>
                    <form method='POST' action='{{ route($routePrefix . '.categories.store', $item) }}' class='mb-4'>
                        @csrf
                        <label class='form-label fw-semibold' style='font-size: 13px;'>Tambah Kategori Menu Baru</label>
                        <div class='input-group'>
                            <input class='form-control' name='name'
                                placeholder='Cth: Makanan Utama, Minuman Segar, Paket Hemat' required>
                            <button class='btn btn-lokantara fw-bold'>
                                <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
                            </button>
                        </div>
                    </form>

                    @forelse($item->culinary->menuCategories as $category)
                        <div class='p-3 rounded-3 mb-3'
                            style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                            <h5 class='fw-bold mb-3 d-flex align-items-center gap-2' style='color: var(--lokantara-primary); font-size: 14px;'>
                                <i class="fa-solid fa-folder-open"></i>
                                <span>Kategori: {{ $category->name }}</span>
                            </h5>

                            <form method='POST' action='{{ route($routePrefix . '.items.store', [$item, $category]) }}'
                                class='mb-3'>
                                @csrf
                                <div class='row g-2'>
                                    <div class='col-md-5'>
                                        <input class='form-control form-control-sm' name='name'
                                            placeholder='Nama Menu (cth: Sate Kambing 10 Tusuk)' required>
                                    </div>
                                    <div class='col-md-4'>
                                        <input class='form-control form-control-sm' name='price' type='number'
                                            min='0' placeholder='Harga (Rp)' required>
                                    </div>
                                    <div class='col-md-3'>
                                        <button class='btn btn-sm btn-outline-lokantara w-100 fw-bold'>
                                            <i class="fa-solid fa-plus me-1"></i> Menu
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if ($category->items->isNotEmpty())
                                <div class='table-responsive'>
                                    <table class='table table-sm table-bordered bg-white mb-0'>
                                        <thead class='table-light'>
                                            <tr>
                                                <th>Nama Menu</th>
                                                <th>Harga</th>
                                                <th>Status</th>
                                                <th class='text-end' style='width: 140px;'>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($category->items as $menuItem)
                                                <tr>
                                                    <td>
                                                        <strong class="text-dark">{{ $menuItem->name }}</strong>
                                                        @if ($menuItem->is_featured)
                                                            <span class='badge bg-warning text-dark ms-1'
                                                                style='font-size: 10px;'>
                                                                <i class="fa-solid fa-star me-0.5"></i> Unggulan
                                                            </span>
                                                        @endif
                                                        @if ($menuItem->description)
                                                            <small class='text-muted d-block'>{{ $menuItem->description }}</small>
                                                        @endif
                                                    </td>
                                                    <td class='fw-bold text-success'>Rp {{ number_format($menuItem->price, 0, ',', '.') }}</td>
                                                    <td><x-status-badge :status='$menuItem->status' /></td>
                                                    <td class='text-end'>
                                                        <button type='button' class='btn btn-xs btn-outline-primary py-1 px-2 rounded-2' data-bs-toggle='modal' data-bs-target='#editMenuItemModal{{ $menuItem->id }}' style='font-size: 11px;'>
                                                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                                        </button>
                                                        <form method='POST' action='{{ route('mitra.culinary.items.destroy', [$item, $menuItem]) }}' class='d-inline'>
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type='submit' class='btn btn-xs btn-outline-danger py-1 px-2 rounded-2' style='font-size: 11px;' onclick="return confirm('Hapus menu {{ $menuItem->name }}?')">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>

                                                <!-- Modal Edit Menu Item -->
                                                <div class='modal fade' id='editMenuItemModal{{ $menuItem->id }}' tabindex='-1' aria-labelledby='editMenuItemModalLabel{{ $menuItem->id }}' aria-hidden='true'>
                                                    <div class='modal-dialog modal-dialog-centered'>
                                                        <div class='modal-content border-0 shadow-lg rounded-4 text-start'>
                                                            <form method='POST' action='{{ route('mitra.culinary.items.update', [$item, $menuItem]) }}'>
                                                                @csrf
                                                                @method('PUT')
                                                                <div class='modal-header border-bottom py-3 px-4' style='background: #f8fafc;'>
                                                                    <h6 class='modal-title fw-bold text-dark' id='editMenuItemModalLabel{{ $menuItem->id }}'>
                                                                        <i class='fa-solid fa-utensils text-primary me-1'></i> Edit Menu Kuliner
                                                                    </h6>
                                                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                                </div>
                                                                <div class='modal-body p-4'>
                                                                    <div class='mb-3'>
                                                                        <label class='form-label fw-bold' style='font-size: 13px;'>Nama Menu Hidangan <span class='text-danger'>*</span></label>
                                                                        <input type='text' name='name' class='form-control' value='{{ old('name', $menuItem->name) }}' required>
                                                                    </div>
                                                                    <div class='mb-3'>
                                                                        <label class='form-label fw-bold' style='font-size: 13px;'>Harga per Porsi (Rp) <span class='text-danger'>*</span></label>
                                                                        <div class='input-group'>
                                                                            <span class='input-group-text bg-white fw-bold text-success'>Rp</span>
                                                                            <input type='number' min='0' name='price' class='form-control' value='{{ old('price', $menuItem->price) }}' required>
                                                                        </div>
                                                                    </div>
                                                                    <div class='mb-3'>
                                                                        <label class='form-label fw-bold' style='font-size: 13px;'>Deskripsi Singkat</label>
                                                                        <textarea name='description' rows='2' class='form-control' placeholder='Penjelasan porsi, rasa, atau racikan bumbu...'>{{ old('description', $menuItem->description) }}</textarea>
                                                                    </div>
                                                                    <div class='row g-2 mb-2'>
                                                                        <div class='col-6'>
                                                                            <label class='form-label fw-bold' style='font-size: 13px;'>Status Menu</label>
                                                                            <select class='form-select form-select-sm' name='status'>
                                                                                <option value='active' @selected($menuItem->status === 'active')> Aktif Tersedia</option>
                                                                                <option value='inactive' @selected($menuItem->status === 'inactive')> Non-Aktif (Habis)</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class='col-6 d-flex align-items-end'>
                                                                            <div class='form-check mb-2'>
                                                                                <input class='form-check-input' type='checkbox' name='is_featured' value='1' id='feat_{{ $menuItem->id }}' @checked($menuItem->is_featured)>
                                                                                <label class='form-check-label fw-semibold' for='feat_{{ $menuItem->id }}' style='font-size: 12px;'>
                                                                                    <i class="fa-solid fa-star text-warning me-0.5"></i> Menu Unggulan
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class='modal-footer border-top py-2.5 px-4' style='background: #f8fafc;'>
                                                                    <button type='button' class='btn btn-sm btn-secondary rounded-pill px-3' data-bs-dismiss='modal'>Batal</button>
                                                                    <button type='submit' class='btn btn-sm btn-lokantara rounded-pill px-4 fw-bold'>Simpan Perubahan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <small class='text-muted'>Belum ada menu di kategori ini.</small>
                            @endif
                        </div>
                    @empty
                        <x-empty-state title='Belum ada kategori menu'
                            description='Buat kategori menu pertama Anda di atas.' compact />
                    @endforelse
                </x-content-card>

                <!-- Kelola E-Voucher & Paket Menu Promo -->
                <x-content-card title='Pilihan E-Voucher & Paket Promo Kuliner' class='mt-4'>
                    <p class='text-muted small mb-3' style='font-size: 13px;'>
                        Tambahkan voucher potongan saldo bebas menu (digunakan di kasir) atau paket menu komplit hemat untuk menarik lebih banyak pelanggan online.
                    </p>

                    <!-- Form Tambah Voucher Baru -->
                    <div class='p-3.5 rounded-3 mb-4' style='background: #f8fafc; border: 1px dashed #cbd5e1;'>
                        <h6 class='fw-bold text-dark mb-3' style='font-size: 14px;'>
                            <i class='fa-solid fa-ticket text-danger me-1.5'></i> Buat E-Voucher Baru
                        </h6>
                        <form method='POST' action='{{ route('mitra.culinary.vouchers.store', $item) }}'>
                            @csrf
                            <div class='row g-3 mb-3'>
                                <div class='col-md-5'>
                                    <label class='form-label fw-bold' style='font-size: 12.5px;'>Tipe Voucher <span class='text-danger'>*</span></label>
                                    <select class='form-select form-select-sm' name='voucher_type' required>
                                        <option value='cash'> Voucher Saldo Bebas Menu (Potong Kasir)</option>
                                        <option value='package'> Paket Menu Komplit Promo</option>
                                    </select>
                                </div>
                                <div class='col-md-7'>
                                    <label class='form-label fw-bold' style='font-size: 12.5px;'>Nama Voucher / Paket <span class='text-danger'>*</span></label>
                                    <input class='form-control form-control-sm' name='name' placeholder='Cth: Voucher Bebas Menu Senilai Rp 50.000 atau Paket Kenyang' required>
                                </div>
                                <div class='col-md-4'>
                                    <label class='form-label fw-bold' style='font-size: 12.5px;'>Harga Jual Promo (Rp) <span class='text-danger'>*</span></label>
                                    <div class='input-group input-group-sm'>
                                        <span class='input-group-text bg-white fw-bold text-success'>Rp</span>
                                        <input type='number' min='1000' name='price' class='form-control' placeholder='45000' required>
                                    </div>
                                </div>
                                <div class='col-md-8'>
                                    <label class='form-label fw-bold' style='font-size: 12.5px;'>Deskripsi & Ketentuan</label>
                                    <input class='form-control form-control-sm' name='description' placeholder='Cth: Bebas pilih menu makanan apa saja. Tunjukkan QR saat bayar di kasir.'>
                                </div>
                            </div>
                            <button type='submit' class='btn btn-sm btn-lokantara fw-bold px-3.5 py-1.5 rounded-pill'>
                                <i class='fa-solid fa-plus me-1'></i> Tambah E-Voucher
                            </button>
                        </form>
                    </div>

                    <!-- Daftar Voucher yang Aktif -->
                    <h6 class='fw-bold text-dark mb-2.5' style='font-size: 13.5px;'>
                        Daftar E-Voucher Tersedia ({{ $item->offers->where('status', 'active')->count() }})
                    </h6>

                    @if($item->offers && $item->offers->isNotEmpty())
                        <div class='table-responsive'>
                            <table class='table table-sm table-bordered bg-white align-middle mb-0' style='font-size: 12.5px;'>
                                <thead class='table-light'>
                                    <tr>
                                        <th>Nama & Tipe Voucher</th>
                                        <th>Harga Promo</th>
                                        <th>Masa Berlaku</th>
                                        <th>Status</th>
                                        <th class='text-end' style='width: 130px;'>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->offers as $vch)
                                        @php
                                            $isCash = str_contains(strtolower($vch->name), 'bebas') || str_contains(strtolower($vch->sku), 'cash');
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class='d-flex align-items-center gap-1.5'>
                                                    <strong class='text-dark'>{{ $vch->name }}</strong>
                                                    @if($isCash)
                                                        <span class='badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2' style='font-size: 10px;'>
                                                            <i class='fa-solid fa-wallet me-0.5'></i> Bebas Menu
                                                        </span>
                                                    @else
                                                        <span class='badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2' style='font-size: 10px;'>
                                                            <i class='fa-solid fa-bowl-food me-0.5'></i> Paket Menu
                                                        </span>
                                                    @endif
                                                </div>
                                                <small class='text-muted d-block'>{{ $vch->description ?: 'Tanpa deskripsi tambahan' }}</small>
                                            </td>
                                            <td class='fw-extrabold text-success'>
                                                Rp {{ number_format($vch->price, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <span class='badge bg-light text-dark border'>
                                                    <i class='fa-regular fa-clock text-primary me-1'></i> 30 Hari
                                                </span>
                                            </td>
                                            <td>
                                                <x-status-badge :status='$vch->status' />
                                            </td>
                                            <td class='text-end'>
                                                <button type='button' class='btn btn-xs btn-outline-primary py-1 px-2 rounded-2' data-bs-toggle='modal' data-bs-target='#editVchModal{{ $vch->id }}' style='font-size: 11px;'>
                                                    <i class='fa-solid fa-pen-to-square me-1'></i> Edit
                                                </button>
                                                <form method='POST' action='{{ route('mitra.culinary.vouchers.destroy', [$item, $vch]) }}' class='d-inline'>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type='submit' class='btn btn-xs btn-outline-danger py-1 px-2 rounded-2' style='font-size: 11px;' onclick="return confirm('Hapus voucher {{ $vch->name }}?')">
                                                        <i class='fa-solid fa-trash-can'></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit Voucher -->
                                        <div class='modal fade' id='editVchModal{{ $vch->id }}' tabindex='-1' aria-labelledby='editVchModalLabel{{ $vch->id }}' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered'>
                                                <div class='modal-content border-0 shadow-lg rounded-4 text-start'>
                                                    <form method='POST' action='{{ route('mitra.culinary.vouchers.update', [$item, $vch]) }}'>
                                                        @csrf
                                                        @method('PUT')
                                                        <div class='modal-header border-bottom py-3 px-4' style='background: #f8fafc;'>
                                                            <h6 class='modal-title fw-bold text-dark' id='editVchModalLabel{{ $vch->id }}'>
                                                                <i class='fa-solid fa-ticket text-danger me-1'></i> Edit E-Voucher Kuliner
                                                            </h6>
                                                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                        </div>
                                                        <div class='modal-body p-4'>
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold' style='font-size: 13px;'>Nama Voucher / Paket <span class='text-danger'>*</span></label>
                                                                <input type='text' name='name' class='form-control' value='{{ old('name', $vch->name) }}' required>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold' style='font-size: 13px;'>Harga Jual Promo (Rp) <span class='text-danger'>*</span></label>
                                                                <div class='input-group'>
                                                                    <span class='input-group-text bg-white fw-bold text-success'>Rp</span>
                                                                    <input type='number' min='1000' name='price' class='form-control' value='{{ old('price', $vch->price) }}' required>
                                                                </div>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold' style='font-size: 13px;'>Deskripsi & Ketentuan</label>
                                                                <textarea name='description' rows='2' class='form-control' placeholder='Penjelasan penggunaan voucher...'>{{ old('description', $vch->description) }}</textarea>
                                                            </div>
                                                            <div class='mb-2'>
                                                                <label class='form-label fw-bold' style='font-size: 13px;'>Status Voucher</label>
                                                                <select class='form-select' name='status'>
                                                                    <option value='active' @selected($vch->status === 'active')> Aktif (Dapat Dibeli)</option>
                                                                    <option value='inactive' @selected($vch->status === 'inactive')> Non-Aktif (Dihentikan Sementara)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class='modal-footer border-top py-2.5 px-4' style='background: #f8fafc;'>
                                                            <button type='button' class='btn btn-sm btn-secondary rounded-pill px-3' data-bs-dismiss='modal'>Batal</button>
                                                            <button type='submit' class='btn btn-sm btn-lokantara rounded-pill px-4 fw-bold'>Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class='p-3 bg-light rounded-3 text-center text-muted small'>
                            Belum ada voucher kuliner yang ditambahkan untuk tempat ini. Gunakan formulir di atas untuk membuat voucher pertama Anda.
                        </div>
                    @endif
                </x-content-card>
            @endif

            @if ($domain === 'event' && $item->event)
                <x-content-card title='Tipe Tiket Event' class='mt-4'>
                    <form method='POST' action='{{ route($routePrefix . '.ticket-types.store', $item) }}' class='mb-3'>
                        @csrf
                        <div class='row g-2'>
                            <div class='col-md-5'>
                                <input class='form-control form-control-sm' name='name' required
                                    placeholder='Nama Tiket (cth: Presale / VIP)'>
                            </div>
                            <div class='col-md-4'>
                                <input class='form-control form-control-sm' name='price' type='number' min='0'
                                    placeholder='Harga (Rp)' required>
                            </div>
                            <div class='col-md-3'>
                                <input class='form-control form-control-sm' name='quota' type='number' min='1'
                                    placeholder='Kuota' required>
                            </div>
                        </div>
                        <button class='btn btn-sm btn-lokantara mt-2 fw-bold'>
                            <i class="fa-solid fa-plus me-1"></i> Tambah Tiket Event
                        </button>
                    </form>

                    @if ($item->event->ticketTypes->isNotEmpty())
                        <div class='table-responsive'>
                            <table class='table table-sm table-bordered bg-white'>
                                <thead class='table-light'>
                                    <tr>
                                        <th>Nama Tiket</th>
                                        <th>Harga / Tiket</th>
                                        <th>Kuota Tersedia</th>
                                        <th class='text-end' style='width: 140px;'>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->event->ticketTypes as $type)
                                        <tr>
                                            <td><strong class="text-dark">{{ $type->name }}</strong></td>
                                            <td class='fw-bold text-danger'>Rp {{ number_format($type->offer->price, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary border">
                                                    <i class="fa-solid fa-ticket me-1"></i> {{ $type->quota }} tiket
                                                </span>
                                            </td>
                                            <td class='text-end'>
                                                <button type='button' class='btn btn-xs btn-outline-primary py-1 px-2 rounded-2' data-bs-toggle='modal' data-bs-target='#editTicketModal{{ $type->id }}' style='font-size: 11px;'>
                                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                                </button>
                                                <form method='POST' action='{{ route('mitra.event.ticket-types.destroy', [$item, $type]) }}' class='d-inline'>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type='submit' class='btn btn-xs btn-outline-danger py-1 px-2 rounded-2' style='font-size: 11px;' onclick="return confirm('Hapus tiket {{ $type->name }}?')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit Tiket Event -->
                                        <div class='modal fade' id='editTicketModal{{ $type->id }}' tabindex='-1' aria-labelledby='editTicketModalLabel{{ $type->id }}' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered'>
                                                <div class='modal-content border-0 shadow-lg rounded-4 text-start'>
                                                    <form method='POST' action='{{ route('mitra.event.ticket-types.update', [$item, $type]) }}'>
                                                        @csrf
                                                        @method('PUT')
                                                        <div class='modal-header border-bottom py-3 px-4' style='background: #f8fafc;'>
                                                            <h6 class='modal-title fw-bold text-dark' id='editTicketModalLabel{{ $type->id }}'>
                                                                <i class='fa-solid fa-ticket text-primary me-1'></i> Edit Tiket Event
                                                            </h6>
                                                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                        </div>
                                                        <div class='modal-body p-4'>
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold' style='font-size: 13px;'>Nama Kategori Tiket <span class='text-danger'>*</span></label>
                                                                <input type='text' name='name' class='form-control' value='{{ old('name', $type->name) }}' required>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold' style='font-size: 13px;'>Harga Tiket Masuk (Rp) <span class='text-danger'>*</span></label>
                                                                <div class='input-group'>
                                                                    <span class='input-group-text bg-white fw-bold text-danger'>Rp</span>
                                                                    <input type='number' min='0' name='price' class='form-control' value='{{ old('price', $type->offer->price) }}' required>
                                                                </div>
                                                            </div>
                                                            <div class='mb-2'>
                                                                <label class='form-label fw-bold' style='font-size: 13px;'>Kuota Tiket <span class='text-danger'>*</span></label>
                                                                <div class='input-group'>
                                                                    <input type='number' min='1' name='quota' class='form-control' value='{{ old('quota', $type->quota) }}' required>
                                                                    <span class='input-group-text bg-white'>Tiket</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class='modal-footer border-top py-2.5 px-4' style='background: #f8fafc;'>
                                                            <button type='button' class='btn btn-sm btn-secondary rounded-pill px-3' data-bs-dismiss='modal'>Batal</button>
                                                            <button type='submit' class='btn btn-sm btn-lokantara rounded-pill px-4 fw-bold'>Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-content-card>
            @endif

            @if ($domain === 'rental' && $item->rentalVehicle)
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
                                <input class='form-control form-control-sm' name='price' type='number' min='0'
                                    placeholder='Tarif per Hari (Rp)' required>
                            </div>
                        </div>
                        <button class='btn btn-sm btn-lokantara mt-2 fw-bold'>
                            <i class="fa-solid fa-plus me-1"></i> Tambah Tarif Sewa
                        </button>
                    </form>

                    @if ($item->rentalVehicle->rates->isNotEmpty())
                        <div class='table-responsive'>
                            <table class='table table-sm table-bordered bg-white'>
                                <thead class='table-light'>
                                    <tr>
                                        <th>Mode Sewa</th>
                                        <th>Durasi</th>
                                        <th>Tarif Sewa</th>
                                        <th class='text-end' style='width: 140px;'>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->rentalVehicle->rates as $rate)
                                        <tr>
                                            <td>
                                                <strong class="text-dark">
                                                    <i class="fa-solid {{ $rate->drive_mode === 'self_drive' ? 'fa-key' : 'fa-user-tie' }} text-secondary me-1"></i>
                                                    {{ str($rate->drive_mode)->headline() }}
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge text-bg-light border">
                                                    {{ $rate->duration_value }} {{ str($rate->duration_unit)->headline() }}
                                                </span>
                                            </td>
                                            <td class='fw-bold text-primary'>Rp {{ number_format($rate->offer->price, 0, ',', '.') }}</td>
                                            <td class='text-end'>
                                                <button type='button' class='btn btn-xs btn-outline-primary py-1 px-2 rounded-2' data-bs-toggle='modal' data-bs-target='#editRateModal{{ $rate->id }}' style='font-size: 11px;'>
                                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                                </button>
                                                <form method='POST' action='{{ route('mitra.rental.rates.destroy', [$item, $rate]) }}' class='d-inline'>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type='submit' class='btn btn-xs btn-outline-danger py-1 px-2 rounded-2' style='font-size: 11px;' onclick="return confirm('Hapus tarif sewa ini?')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit Tarif Rental -->
                                        <div class='modal fade' id='editRateModal{{ $rate->id }}' tabindex='-1' aria-labelledby='editRateModalLabel{{ $rate->id }}' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered'>
                                                <div class='modal-content border-0 shadow-lg rounded-4 text-start'>
                                                    <form method='POST' action='{{ route('mitra.rental.rates.update', [$item, $rate]) }}'>
                                                        @csrf
                                                        @method('PUT')
                                                        <div class='modal-header border-bottom py-3 px-4' style='background: #f8fafc;'>
                                                            <h6 class='modal-title fw-bold text-dark' id='editRateModalLabel{{ $rate->id }}'>
                                                                <i class='fa-solid fa-car text-primary me-1'></i> Edit Tarif Sewa Rental
                                                            </h6>
                                                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                        </div>
                                                        <div class='modal-body p-4'>
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold' style='font-size: 13px;'>Mode Sewa <span class='text-danger'>*</span></label>
                                                                <select class='form-select' name='drive_mode' required>
                                                                    <option value='self_drive' @selected($rate->drive_mode === 'self_drive')>Lepas Kunci (Self Drive)</option>
                                                                    <option value='with_driver' @selected($rate->drive_mode === 'with_driver')>Dengan Sopir (With Driver)</option>
                                                                </select>
                                                            </div>
                                                            <div class='mb-2'>
                                                                <label class='form-label fw-bold' style='font-size: 13px;'>Tarif Sewa (Rp) <span class='text-danger'>*</span></label>
                                                                <div class='input-group'>
                                                                    <span class='input-group-text bg-white fw-bold text-primary'>Rp</span>
                                                                    <input type='number' min='0' name='price' class='form-control' value='{{ old('price', $rate->offer->price) }}' required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class='modal-footer border-top py-2.5 px-4' style='background: #f8fafc;'>
                                                            <button type='button' class='btn btn-sm btn-secondary rounded-pill px-3' data-bs-dismiss='modal'>Batal</button>
                                                            <button type='submit' class='btn btn-sm btn-lokantara rounded-pill px-4 fw-bold'>Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
                <form method='POST' enctype='multipart/form-data'
                    action='{{ route('mitra.catalog.media', ['domain' => $domain, 'entity' => $item]) }}' class='mb-3'>
                    @csrf
                    <div class='mb-2'>
                        <label class='form-label fw-semibold text-dark' style='font-size: 13px;'>Pilih File Foto</label>
                        <input class='form-control form-control-sm' type='file' name='image'
                            accept='image/jpeg,image/png,image/webp' required onchange='previewDomainMedia(this)'>
                    </div>

                    <!-- Live Preview Box -->
                    <div id='domain-live-preview-box' class='mb-2 p-2 rounded'
                        style='display: none; background: var(--lokantara-background); border: 1px dashed var(--lokantara-primary); text-align: center;'>
                        <small class='text-muted d-block mb-1'>Preview foto yang akan diunggah:</small>
                        <img id='domain-live-preview-img' src=''
                            style='max-height: 140px; max-width: 100%; border-radius: 6px; object-fit: cover;'>
                    </div>

                    <div class='row g-2 mb-2'>
                        <div class='col-6'>
                            <label class='form-label' style='font-size: 12px;'>Peran Foto</label>
                            <select class='form-select form-select-sm' name='role' required>
                                <option value='cover'>Foto Cover Utama</option>
                                <option value='gallery'>Galeri Foto</option>
                            </select>
                        </div>
                        <div class='col-6'>
                            <label class='form-label' style='font-size: 12px;'>Keterangan / Caption</label>
                            <input class='form-control form-control-sm' name='caption' placeholder='Opsional'>
                        </div>
                    </div>

                    <button class='btn btn-sm btn-lokantara w-100 fw-bold'>
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Unggah Foto
                    </button>
                </form>

                <hr class='my-3'>

                <!-- Saved Media Gallery Preview -->
                <div class='d-flex align-items-center justify-content-between mb-2'>
                    <h6 class='fw-bold mb-0' style='font-size: 13px;'>Foto Tersimpan ({{ $item->media->count() }})</h6>
                </div>

                @if ($item->media->isEmpty())
                    <div class='p-3 text-center rounded'
                        style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                        <i class="fa-solid fa-images fs-2 text-muted mb-1 d-block"></i>
                        <small class='text-muted'>Belum ada foto yang diunggah. Unggah minimal 1 Foto Cover agar dapat
                            diajukan ke publik.</small>
                    </div>
                @else
                    <div class='row g-2'>
                        @foreach ($item->media as $media)
                            <div class='col-6'>
                                <div class='card h-100 border' style='border-radius: 8px; overflow: hidden;'>
                                    <div style='height: 100px; background: #e2e8f0; position: relative;'>
                                        <img src='{{ asset('storage/' . $media->object_key) }}'
                                            alt='{{ $media->pivot->caption ?? $item->name }}'
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
