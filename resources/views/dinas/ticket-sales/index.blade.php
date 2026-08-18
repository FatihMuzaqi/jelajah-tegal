@extends('layouts.dinas')

@section('title', 'Log Penjualan Tiket & Retribusi PAD')
@section('page-title', 'Log Penjualan Tiket & Retribusi PAD')
@section('page-description', 'Catatan transaksi seluruh tiket masuk dan layanan pariwisata yang dikelola instansi dinas Pemkab/Pemkot Tegal.')

@section('page-actions')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('dinas.ticket-sales.export', request()->query()) }}" class="btn btn-success rounded-pill px-4 py-2 fw-bold fs-7 shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-file-excel"></i>
            <span>Unduh Laporan (CSV / Excel)</span>
        </a>
        <a href="{{ route('dinas.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 fw-semibold fs-7 d-inline-flex align-items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>
@endsection

@section('content')
    <!-- 1. Quick Metrics Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Tiket -->
        <div class="col-12 col-md-4">
            <div class="dinas-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="dinas-badge bg-primary-subtle text-primary border border-primary-subtle">
                        <i class="fa-solid fa-ticket"></i> Total Tiket Filter
                    </span>
                    <div class="dinas-icon-box bg-primary-subtle text-primary border border-primary-subtle">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold text-dark mb-0 fs-3" style="letter-spacing: -0.5px;">
                    {{ number_format($totalTickets, 0, ',', '.') }} <span class="fs-7 fw-normal text-muted">Tiket</span>
                </h3>
                <div class="mt-3 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                    <small class="text-muted fs-8">Volume tiket terbit</small>
                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 fs-8">Terekam</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Check-In -->
        <div class="col-12 col-md-4">
            <div class="dinas-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="dinas-badge bg-success-subtle text-success border border-success-subtle">
                        <i class="fa-solid fa-qrcode"></i> Terealisasi Check-In
                    </span>
                    <div class="dinas-icon-box bg-success-subtle text-success border border-success-subtle">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold text-success mb-0 fs-3" style="letter-spacing: -0.5px;">
                    {{ number_format($usedTickets, 0, ',', '.') }} <span class="fs-7 fw-normal text-muted">Orang</span>
                </h3>
                <div class="mt-3 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                    <small class="text-muted fs-8">Pengunjung masuk lokasi</small>
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fs-8">Gate Valid</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Nilai PAD -->
        <div class="col-12 col-md-4">
            <div class="dinas-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="dinas-badge bg-info-subtle text-info-emphasis border border-info-subtle">
                        <i class="fa-solid fa-sack-dollar"></i> Akumulasi Nilai PAD
                    </span>
                    <div class="dinas-icon-box bg-info-subtle text-info-emphasis border border-info-subtle">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold text-primary mb-0 fs-3" style="letter-spacing: -0.5px;">
                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                </h3>
                <div class="mt-3 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                    <small class="text-muted fs-8">Nilai bruto retribusi</small>
                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2 py-0.5 fs-8">PAD Daerah</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Filter & Search Panel -->
    <div class="dinas-panel mb-4">
        <form method="GET" action="{{ route('dinas.ticket-sales.index') }}" class="row g-3 align-items-end">
            <!-- Filter Destinasi -->
            <div class="col-12 col-md-3">
                <label class="form-label text-dark fs-8 fw-bold mb-1.5 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-landmark text-primary"></i> Objek Wisata
                </label>
                <select name="mitra_id" class="form-select form-select-sm fs-7 rounded-3 border-light-subtle shadow-2xs">
                    <option value="">-- Semua Objek Wisata Dinas --</option>
                    @foreach($dinasMitras as $mitra)
                        <option value="{{ $mitra->id }}" @selected(request('mitra_id') === $mitra->id)>
                            🏛️ {{ $mitra->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="col-6 col-md-2">
                <label class="form-label text-dark fs-8 fw-bold mb-1.5 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-circle-notch text-primary"></i> Status Tiket
                </label>
                <select name="status" class="form-select form-select-sm fs-7 rounded-3 border-light-subtle shadow-2xs">
                    <option value="">-- Semua Status --</option>
                    <option value="unused" @selected(request('status') === 'unused')>Belum Digunakan</option>
                    <option value="used" @selected(request('status') === 'used')>Sudah Check-In</option>
                    <option value="expired" @selected(request('status') === 'expired')>Kedaluwarsa</option>
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div class="col-6 col-md-2">
                <label class="form-label text-dark fs-8 fw-bold mb-1.5 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-calendar-day text-primary"></i> Dari Tanggal
                </label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm fs-7 rounded-3 border-light-subtle shadow-2xs">
            </div>

            <!-- Tanggal Sampai -->
            <div class="col-6 col-md-2">
                <label class="form-label text-dark fs-8 fw-bold mb-1.5 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-calendar-check text-primary"></i> Sampai Tanggal
                </label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm fs-7 rounded-3 border-light-subtle shadow-2xs">
            </div>

            <!-- Search Query -->
            <div class="col-6 col-md-2">
                <label class="form-label text-dark fs-8 fw-bold mb-1.5 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass text-primary"></i> Cari Data
                </label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm fs-7 rounded-3 border-light-subtle shadow-2xs" placeholder="Kode tiket, no order...">
            </div>

            <!-- Action Buttons -->
            <div class="col-12 col-md-1 d-flex gap-1.5">
                <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold py-2 fs-7 rounded-3 shadow-sm d-inline-flex align-items-center justify-content-center" title="Terapkan Filter">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(request()->hasAny(['mitra_id', 'status', 'start_date', 'end_date', 'q']))
                    <a href="{{ route('dinas.ticket-sales.index') }}" class="btn btn-sm btn-light border rounded-3 px-2.5 py-2 text-muted shadow-2xs d-inline-flex align-items-center justify-content-center" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 3. Ticket Transactions Table Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="dinas-icon-box bg-primary-subtle text-primary" style="width: 36px; height: 36px; min-width: 36px; font-size: 15px;">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Rincian Transaksi Tiket Resmi ({{ $tickets->total() }})</h6>
                        <small class="text-muted fs-8">Halaman {{ $tickets->currentPage() }} dari {{ $tickets->lastPage() }}</small>
                    </div>
                </div>
                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fs-8">
                    Total Retribusi: <strong class="text-dark">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                </span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table dinas-table align-middle mb-0 fs-7">
                <thead>
                    <tr>
                        <th class="ps-3">No. Tiket & Order</th>
                        <th>Destinasi Objek Wisata</th>
                        <th>Wisatawan / Pemesan</th>
                        <th>Layanan / Tiket</th>
                        <th class="text-end">Tarif Retribusi</th>
                        <th class="text-center">Waktu Beli</th>
                        <th class="text-center">Status</th>
                        <th class="pe-3 text-center">Waktu Check-In</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td class="ps-3">
                                <span class="font-monospace fw-bold text-dark fs-8 d-block">{{ $ticket->ticket_code }}</span>
                                <small class="text-muted fs-8">{{ $ticket->orderItem?->order?->order_number ?? '-' }}</small>
                            </td>
                            <td>
                                <strong class="text-dark d-block fs-7">{{ $ticket->mitra?->display_name ?? '-' }}</strong>
                                <small class="text-muted fs-8">{{ $ticket->mitra?->legal_name }}</small>
                            </td>
                            <td>
                                <span class="text-dark fw-semibold fs-8 d-block">{{ $ticket->orderItem?->order?->user?->name ?? ($ticket->orderItem?->order?->user_snapshot['name'] ?? 'Wisatawan') }}</span>
                                <small class="text-muted fs-8">{{ $ticket->orderItem?->order?->user?->email ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="text-dark fw-medium fs-8">{{ $ticket->orderItem?->item_name ?? '-' }}</span>
                            </td>
                            <td class="text-end">
                                <strong class="text-success fs-7">
                                    Rp {{ number_format($ticket->orderItem?->unit_price ?? 0, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td class="text-center">
                                <span class="text-dark d-block fs-8">{{ $ticket->created_at?->translatedFormat('d M Y') }}</span>
                                <small class="text-muted fs-8">{{ $ticket->created_at?->translatedFormat('H:i') }} WIB</small>
                            </td>
                            <td class="text-center">
                                @if($ticket->status === 'used')
                                    <span class="dinas-badge bg-success-subtle text-success border border-success-subtle fs-8">
                                        <i class="fa-solid fa-check"></i> Check-in
                                    </span>
                                @elseif($ticket->status === 'unused')
                                    <span class="dinas-badge bg-primary-subtle text-primary border border-primary-subtle fs-8">
                                        <i class="fa-solid fa-ticket"></i> Aktif
                                    </span>
                                @else
                                    <span class="dinas-badge bg-secondary-subtle text-secondary fs-8">
                                        {{ strtoupper($ticket->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="pe-3 text-center">
                                @if($ticket->used_at)
                                    <span class="text-dark d-block fs-8">{{ $ticket->used_at->translatedFormat('d M Y') }}</span>
                                    <small class="text-muted fs-8">{{ $ticket->used_at->translatedFormat('H:i') }} WIB</small>
                                @else
                                    <span class="text-muted fs-8">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-5 text-center text-muted fs-8">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-2 text-muted" style="width: 48px; height: 48px; font-size: 20px;">
                                    <i class="fa-solid fa-ticket"></i>
                                </div>
                                <p class="mb-0 fw-semibold">Tidak ada transaksi tiket yang cocok dengan filter saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2 bg-light bg-opacity-50">
            <small class="text-muted fs-8">
                Menampilkan {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} dari total {{ $tickets->total() }} tiket resmi
            </small>
            <div>
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
@endsection
