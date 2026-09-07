@extends('layouts.admin')

@section('title', 'Moderasi ' . $title . ': ' . $item->name)
@section('page-title', 'Moderasi ' . $title)
@section('page-description', 'Periksa kelengkapan informasi, izin mitra, spesifikasi, tarif, lokasi, fasilitas, dan media sebelum mengambil keputusan publikasi.')

@section('content')
<style>
    .mod-card {
        background: #ffffff;
        border: 1px solid var(--lokantara-border, #e2e8f0);
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 22px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    }
    .mod-card-title {
        font-size: 16px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .spec-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
    }
    .facility-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="row g-4">
    <!-- Left Main Column (8 Cols) -->
    <div class="col-lg-8">
        <!-- 1. IDENTITAS & DESKRIPSI -->
        <div class="mod-card">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                <div>
                    <span class="badge bg-light text-dark border px-3 py-1 rounded-pill fs-8 fw-bold mb-2">
                        <i class="fa-solid fa-layer-group me-1 text-primary"></i> {{ $title }} · {{ $item->category?->name ?? 'Kategori Umum' }}
                    </span>
                    <h2 class="fw-extrabold text-dark fs-4 mb-1">{{ $item->name }}</h2>
                    <div class="text-muted fs-7">
                        <i class="fa-solid fa-link text-secondary me-1"></i> Slug: <code class="text-primary">{{ $item->slug }}</code>
                    </div>
                </div>
                <div>
                    <x-status-badge :status="$item->status" />
                </div>
            </div>

            <div class="p-3.5 rounded-3 mb-3 bg-light border" style="font-size: 13.5px; line-height: 1.6; color: #334155;">
                <strong class="d-block text-dark fw-bold mb-1"><i class="fa-solid fa-align-left text-primary me-1"></i> Deskripsi Layanan:</strong>
                {{ $item->description ?: 'Belum ada deskripsi yang ditambahkan oleh mitra.' }}
            </div>

            <div class="row g-3 fs-7">
                <div class="col-sm-6">
                    <div class="spec-item">
                        <small class="text-muted d-block fs-8">Mitra Pengelola:</small>
                        <strong class="text-dark">{{ $item->mitra->display_name }}</strong>
                        <div class="text-muted fs-8">{{ $item->mitra->name }} ({{ $item->mitra->slug }})</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="spec-item">
                        <small class="text-muted d-block fs-8">Wilayah / Kecamatan:</small>
                        <strong class="text-dark"><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $item->region?->name ?? 'Kabupaten/Kota Tegal' }}</strong>
                    </div>
                </div>
                <div class="col-12">
                    <div class="spec-item">
                        <small class="text-muted d-block fs-8">Alamat Fisik Lengkap:</small>
                        <strong class="text-dark">{{ $item->address ?: 'Alamat tidak dicantumkan.' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. SPESIFIKASI KHUSUS DOMAIN -->
        @if ($domain === 'rental' && $item->rentalVehicle)
            @php($rv = $item->rentalVehicle)
            <div class="mod-card">
                <h3 class="mod-card-title"><i class="fa-solid fa-car text-primary"></i> Spesifikasi Armada Rental</h3>
                <div class="row g-3 fs-7 mb-3">
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Tipe & Merek:</small>
                            <strong class="text-dark">{{ str($rv->vehicle_type)->headline() }} — {{ $rv->brand }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Model & Varian:</small>
                            <strong class="text-dark">{{ $rv->model }} ({{ $rv->year ?? '-' }})</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Plat Nomor Kendaraan:</small>
                            <strong class="text-dark font-mono"><i class="fa-solid fa-id-card text-secondary me-1"></i> {{ $rv->plate_number }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Transmisi & Kursi:</small>
                            <strong class="text-dark">{{ str($rv->transmission)->headline() }} · {{ $rv->seats }} Kursi</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Uang Deposit Jaminan:</small>
                            <strong class="text-success">Rp {{ number_format($rv->deposit_amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Opsi Pengemudi:</small>
                            <div class="d-flex gap-1.5 mt-0.5">
                                <span class="badge {{ $rv->self_drive_available ? 'bg-success' : 'bg-secondary' }} fs-8">Lepas Kunci</span>
                                <span class="badge {{ $rv->driver_available ? 'bg-primary' : 'bg-secondary' }} fs-8">Dengan Sopir</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($rv->fuel_policy || $rv->pickup_instructions)
                    <div class="p-3 rounded-3 bg-light border fs-7 mb-3">
                        @if($rv->fuel_policy)
                            <div class="mb-1"><strong>Kebijakan BBM:</strong> {{ $rv->fuel_policy }}</div>
                        @endif
                        @if($rv->pickup_instructions)
                            <div><strong>Petunjuk Serah Terima:</strong> {{ $rv->pickup_instructions }}</div>
                        @endif
                    </div>
                @endif

                <!-- Tarif Rental List -->
                <h4 class="fs-6 fw-bold text-dark mt-3 mb-2"><i class="fa-solid fa-money-bill-wave text-success me-1"></i> Paket Tarif Sewa ({{ $rv->rates->count() }})</h4>
                @forelse($rv->rates as $rate)
                    <div class="p-3 rounded-3 mb-2 border d-flex align-items-center justify-content-between" style="background: #ffffff;">
                        <div>
                            <span class="badge {{ $rate->drive_mode === 'self_drive' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }} rounded-pill px-2 py-0.5 fs-8 fw-bold">
                                {{ $rate->drive_mode === 'self_drive' ? 'Lepas Kunci' : 'Dengan Sopir' }}
                            </span>
                            <strong class="text-dark ms-2">{{ $rate->duration_value }} {{ str($rate->duration_unit)->headline() }}</strong>
                        </div>
                        <div class="text-end">
                            <strong class="fs-6 text-success">Rp {{ number_format($rate->offer->price, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @empty
                    <div class="p-3 rounded bg-light text-muted small text-center">Belum ada paket tarif sewa yang ditambahkan.</div>
                @endforelse
            </div>
        @elseif ($domain === 'culinary' && $item->culinary)
            @php($cv = $item->culinary)
            <div class="mod-card">
                <h3 class="mod-card-title"><i class="fa-solid fa-utensils text-warning"></i> Operasional & Menu Kuliner</h3>
                <div class="row g-3 fs-7 mb-3">
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Jenis Tempat Kuliner:</small>
                            <strong class="text-dark">{{ str($cv->venue_type)->headline() }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Kontak / WhatsApp:</small>
                            <strong class="text-dark"><i class="fa-brands fa-whatsapp text-success me-1"></i> {{ $cv->phone ?: '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Layanan Reservasi Meja:</small>
                            <span class="badge {{ $cv->accepts_reservations ? 'bg-success' : 'bg-secondary' }} fs-8 mt-0.5">
                                {{ $cv->accepts_reservations ? 'Menerima Reservasi' : 'Dine-In Langsung' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($cv->reservation_notes)
                    <div class="p-3 rounded-3 bg-light border fs-7 mb-3">
                        <strong>Catatan Reservasi:</strong> {{ $cv->reservation_notes }}
                    </div>
                @endif

                <!-- Menu Items List -->
                <h4 class="fs-6 fw-bold text-dark mt-3 mb-2"><i class="fa-solid fa-bowl-food text-danger me-1"></i> Daftar Menu Makanan & Minuman</h4>
                @forelse($cv->menuCategories as $cat)
                    <div class="mb-3 p-3 rounded-3 border bg-light">
                        <strong class="text-dark fs-7 d-block mb-2"><i class="fa-solid fa-folder-open text-warning me-1"></i> {{ $cat->name }} ({{ $cat->items->count() }} item)</strong>
                        <div class="row g-2">
                            @foreach($cat->items as $menu)
                                <div class="col-md-6">
                                    <div class="p-2.5 rounded bg-white border d-flex align-items-center justify-content-between">
                                        <div>
                                            <strong class="text-dark fs-7">{{ $menu->name }}</strong>
                                            @if($menu->is_featured) <span class="badge bg-warning text-dark fs-8">Favorit</span> @endif
                                            <small class="text-muted d-block fs-8">{{ str($menu->description)->limit(45) }}</small>
                                        </div>
                                        <div class="fw-bold text-success fs-7">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-3 rounded bg-light text-muted small text-center mb-3">Belum ada daftar menu yang ditambahkan.</div>
                @endforelse

                <!-- E-Vouchers List -->
                @if($item->offers && $item->offers->isNotEmpty())
                    <h4 class="fs-6 fw-bold text-dark mt-3 mb-2"><i class="fa-solid fa-gift text-primary me-1"></i> E-Voucher Promo Kuliner ({{ $item->offers->count() }})</h4>
                    @foreach($item->offers as $vch)
                        <div class="p-2.5 rounded-3 mb-2 border d-flex align-items-center justify-content-between bg-white">
                            <div>
                                <strong class="text-dark fs-7">{{ $vch->name }}</strong>
                                <small class="text-muted d-block fs-8">{{ $vch->description }}</small>
                            </div>
                            <strong class="text-primary fs-7">Rp {{ number_format($vch->price, 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                @endif
            </div>
        @elseif ($domain === 'event' && $item->event)
            @php($ev = $item->event)
            <div class="mod-card">
                <h3 class="mod-card-title"><i class="fa-solid fa-calendar-check text-danger"></i> Jadwal & Tiket Acara / Event</h3>
                <div class="row g-3 fs-7 mb-3">
                    <div class="col-sm-6">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Kategori / Jenis Event:</small>
                            <strong class="text-dark">{{ $ev->event_type }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Nama Venue / Panggung:</small>
                            <strong class="text-dark">{{ $ev->venue_name ?: '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Waktu Mulai:</small>
                            <strong class="text-dark">{{ $ev->starts_at?->translatedFormat('d M Y, H:i') }} WIB</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Waktu Selesai:</small>
                            <strong class="text-dark">{{ $ev->ends_at?->translatedFormat('d M Y, H:i') }} WIB</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spec-item">
                            <small class="text-muted d-block fs-8">Batas Akhir Penjualan:</small>
                            <strong class="text-danger">{{ $ev->registration_deadline ? $ev->registration_deadline->translatedFormat('d M Y, H:i') . ' WIB' : 'Hingga Acara Dimulai' }}</strong>
                        </div>
                    </div>
                </div>

                @if($ev->know_before_you_go)
                    <div class="p-3 rounded-3 bg-light border fs-7 mb-3">
                        <strong class="d-block mb-1"><i class="fa-solid fa-circle-info text-primary me-1"></i> Informasi Penting Pengunjung:</strong>
                        {{ $ev->know_before_you_go }}
                    </div>
                @endif

                <!-- Tipe Tiket Event -->
                <h4 class="fs-6 fw-bold text-dark mt-3 mb-2"><i class="fa-solid fa-ticket text-danger me-1"></i> Tipe & Kuota Tiket Event ({{ $ev->ticketTypes->count() }})</h4>
                @forelse($ev->ticketTypes as $type)
                    <div class="p-3 rounded-3 mb-2 border d-flex align-items-center justify-content-between bg-white">
                        <div>
                            <strong class="text-dark fs-7">{{ $type->name }}</strong>
                            <small class="text-muted d-block fs-8">Kuota: {{ $type->quota }} tiket · Terbit: {{ $type->issued_quantity }} · Di-hold: {{ $type->reserved_quantity }}</small>
                        </div>
                        <div class="text-end">
                            <strong class="fs-6 text-danger">Rp {{ number_format($type->offer->price, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @empty
                    <div class="p-3 rounded bg-light text-muted small text-center">Belum ada tipe tiket yang dibuat.</div>
                @endforelse
            </div>
        @endif

        <!-- 3. FASILITAS & SARANA -->
        <div class="mod-card">
            <h3 class="mod-card-title"><i class="fa-solid fa-wand-magic-sparkles text-success"></i> Fasilitas & Sarana yang Disediakan</h3>
            @if ($item->facilities->isNotEmpty())
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($item->facilities as $fac)
                        <span class="facility-badge">
                            <i class="fa-solid fa-check"></i> {{ $fac->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-muted small mb-0">Mitra tidak mencantumkan fasilitas pendukung.</p>
            @endif
        </div>

        <!-- 4. TITIK LOKASI & KOORDINAT PETA -->
        <div class="mod-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="mod-card-title mb-0 border-0 p-0"><i class="fa-solid fa-map-location-dot text-danger"></i> Titik Koordinat Lokasi</h3>
                @if($item->location?->latitude && $item->location?->longitude)
                    <a href="https://www.google.com/maps?q={{ $item->location->latitude }},{{ $item->location->longitude }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger fw-bold rounded-pill">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Google Maps
                    </a>
                @endif
            </div>

            <div class="p-3 rounded-3 bg-light border fs-7">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-8">Latitude (Lintang):</small>
                        <code class="fw-bold fs-7">{{ $item->location?->latitude ?? 'Belum diatur' }}</code>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-8">Longitude (Bujur):</small>
                        <code class="fw-bold fs-7">{{ $item->location?->longitude ?? 'Belum diatur' }}</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. GALERI MEDIA / FOTO -->
        <div class="mod-card">
            <h3 class="mod-card-title"><i class="fa-solid fa-images text-secondary"></i> Galeri Foto & Media Asset ({{ $item->media->count() }})</h3>
            @if ($item->media->isNotEmpty())
                <div class="row g-2">
                    @foreach ($item->media as $med)
                        <div class="col-4 col-md-3">
                            <div class="rounded-3 border overflow-hidden position-relative" style="height: 110px; background: #f8fafc;">
                                <img src="{{ asset('storage/' . $med->object_key) }}" alt="Foto Media" style="width: 100%; height: 100%; object-fit: cover;">
                                @if($med->pivot->role === 'cover')
                                    <span class="badge bg-success position-absolute top-0 start-0 m-1 fs-8">Cover</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted small mb-0">Belum ada foto media yang diunggah untuk destinasi/layanan ini.</p>
            @endif
        </div>
    </div>

    <!-- Right Sidebar Column (4 Cols): Keputusan & Riwayat -->
    <div class="col-lg-4">
        <!-- Keputusan Moderasi Form -->
        <div class="mod-card" style="position: sticky; top: 90px;">
            <h3 class="mod-card-title"><i class="fa-solid fa-gavel text-warning"></i> Keputusan Moderasi</h3>
            
            <form method="POST" action="{{ route($routePrefix . '.update', $item) }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label fw-bold fs-7 text-dark">Keputusan Admin <span class="text-danger">*</span></label>
                    <select class="form-select rounded-3 @error('decision') is-invalid @enderror" name="decision" required>
                        <option value="approve">Setujui & Publikasikan</option>
                        <option value="reject">Tolak Pengajuan</option>
                        @if ($item->status === 'published')
                            <option value="takedown">Takedown (Turunkan Publikasi)</option>
                        @endif
                    </select>
                    @error('decision') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold fs-7 text-dark">Catatan / Alasan Keputusan</label>
                    <textarea class="form-control rounded-3 fs-7" name="reason" rows="3" placeholder="Wajib diisi jika menolak atau melakukan takedown..."></textarea>
                    <small class="text-muted fs-8 d-block mt-1">Catatan ini akan dikirimkan ke notifikasi mitra sebagai panduan revisi.</small>
                </div>

                <button type="submit" class="btn btn-lokantara w-100 fw-bold py-2.5 rounded-pill shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Keputusan
                </button>
            </form>

            <hr class="my-4">

            <!-- Riwayat Moderasi -->
            <h4 class="fs-6 fw-bold text-dark mb-3"><i class="fa-solid fa-timeline text-secondary me-1"></i> Riwayat Moderasi</h4>
            @forelse($item->moderationReports as $report)
                @foreach ($report->actions as $action)
                    <div class="p-2.5 rounded-3 bg-light border mb-2 fs-8">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <strong class="text-dark">{{ str($action->action_type)->headline() }}</strong>
                            <span class="text-muted">{{ $action->created_at?->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mb-0 text-secondary">{{ $action->notes ?: 'Tanpa catatan tambahan.' }}</p>
                    </div>
                @endforeach
            @empty
                <p class="text-muted small mb-0">Belum ada riwayat tindakan moderasi pada layanan ini.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
