@extends('layouts.dinas')

@section('title', 'Log Penjualan Tiket & Retribusi PAD')
@section('page-title', 'Log Penjualan Tiket & Retribusi PAD')
@section('page-description', 'Catatan transaksi seluruh tiket masuk dan layanan pariwisata yang dikelola instansi dinas Pemkab/Pemkot Tegal.')

@section('page-actions')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('dinas.dashboard') }}" class="btn btn-sm btn-light border rounded-3 px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs text-dark" style="background: #ffffff; font-size: 13px;">
            <i class="fa-solid fa-arrow-left text-secondary"></i>
            <span>Kembali ke Dashboard</span>
        </a>
        <a href="{{ route('dinas.ticket-sales.export', request()->query()) }}" class="btn btn-sm rounded-3 px-3.5 py-2 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" style="background: #15803d; color: #ffffff; border: none; font-size: 13px;">
            <i class="fa-solid fa-file-excel"></i>
            <span>Unduh Laporan (CSV / Excel)</span>
        </a>
    </div>
@endsection

@section('content')
<style>
/* Clean Executive Dinas Ticket Sales Styling */
.dinas-stat-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
@media (max-width: 992px) {
    .dinas-stat-grid-3 {
        grid-template-columns: 1fr;
    }
}

.dinas-stat-card {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.dinas-stat-card-green {
    background: #15803d;
    border: 1px solid #166534;
    color: #ffffff;
}
.stat-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.stat-title {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #64748b;
    text-transform: uppercase;
}
.dinas-stat-card-green .stat-title {
    color: rgba(255, 255, 255, 0.85);
}
.stat-icon-square {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}
.icon-translucent {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}
.icon-yellow {
    background: #fefce8;
    color: #eab308;
}
.icon-green {
    background: #f0fdf4;
    color: #22c55e;
}

.stat-amount {
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: 12px;
}
.dinas-stat-card-green .stat-amount {
    color: #ffffff;
}
.stat-bottom-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: #64748b;
}
.dinas-stat-card-green .stat-bottom-row {
    color: rgba(255, 255, 255, 0.8);
}
.badge-sub {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 6px;
}
.badge-sub-blue {
    background: #eff6ff;
    color: #3b82f6;
}
.badge-sub-green {
    background: #f0fdf4;
    color: #16a34a;
}

/* Filter Card */
.dinas-filter-card {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    padding: 16px 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    margin-bottom: 20px;
}
.dinas-filter-label {
    font-size: 11.5px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.dinas-input {
    font-size: 12.5px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    padding: 7px 12px;
    color: #0f172a;
    background-color: #ffffff;
    height: 38px;
}
.dinas-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}
.btn-filter {
    background: #2563eb;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    border-radius: 10px;
    padding: 8px 24px;
    border: none;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.15s ease;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
}
.btn-filter:hover {
    background: #1d4ed8;
    color: #ffffff;
}

/* Table Card */
.dinas-table-card {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}
.dinas-table-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
}
.table-card-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.table-card-sub {
    font-size: 12px;
    color: #64748b;
}
.clean-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.clean-table th {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #64748b;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    background: #f8fafc;
    white-space: nowrap;
}
.clean-table td {
    padding: 13px 16px;
    border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
    font-size: 12.5px;
}
.clean-table tr:hover td {
    background-color: #fafbfd;
}
.table-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
    gap: 12px;
}
.footer-info {
    font-size: 12px;
    color: #64748b;
}
</style>

<!-- 1. KPI Stat Cards (3 Columns) -->
<div class="dinas-stat-grid-3">
    <!-- Card 1: Total Tiket Filter -->
    <div class="dinas-stat-card">
        <div class="stat-top-row">
            <span class="stat-title">TOTAL TIKET FILTER</span>
            <div class="stat-icon-square icon-yellow">
                <i class="fa-solid fa-ticket text-warning"></i>
            </div>
        </div>
        <div class="stat-amount">
            {{ number_format($totalTickets, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">Tiket</span>
        </div>
        <div class="stat-bottom-row">
            <span>Volume tiket terbit</span>
            <span class="badge-sub badge-sub-blue">Terekam</span>
        </div>
    </div>

    <!-- Card 2: Terealisasi Check-In -->
    <div class="dinas-stat-card">
        <div class="stat-top-row">
            <span class="stat-title">TEREALISASI CHECK-IN</span>
            <div class="stat-icon-square icon-green">
                <i class="fa-solid fa-check text-success"></i>
            </div>
        </div>
        <div class="stat-amount">
            {{ number_format($usedTickets, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">Orang</span>
        </div>
        <div class="stat-bottom-row">
            <span>Pengunjung masuk lokasi</span>
            <span class="badge-sub badge-sub-green">Gate Valid</span>
        </div>
    </div>

    <!-- Card 3: Akumulasi Nilai PAD (Green Highlight Card) -->
    <div class="dinas-stat-card dinas-stat-card-green">
        <div class="stat-top-row">
            <span class="stat-title">AKUMULASI NILAI PAD</span>
            <div class="stat-icon-square icon-translucent">
                <i class="fa-solid fa-landmark"></i>
            </div>
        </div>
        <div class="stat-amount">
            Rp {{ number_format($totalAmount, 0, ',', '.') }}
        </div>
        <div class="stat-bottom-row">
            <span>Nilai bruto retribusi</span>
            <span>PAD Daerah</span>
        </div>
    </div>
</div>

<!-- 2. Filter & Search Panel -->
<div class="dinas-filter-card">
    <form method="GET" action="{{ route('dinas.ticket-sales.index') }}" class="row g-3 align-items-end">
        <!-- Objek Wisata -->
        <div class="col-12 col-lg-3 col-md-4">
            <label class="dinas-filter-label">
                <span>️</span> Objek Wisata
            </label>
            <select name="mitra_id" class="form-select dinas-input">
                <option value="">-- Semua Objek Wisata Dinas --</option>
                @foreach($dinasMitras as $mitra)
                    <option value="{{ $mitra->id }}" @selected(request('mitra_id') === $mitra->id)>
                        {{ $mitra->display_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Status Tiket -->
        <div class="col-6 col-lg-2 col-md-4">
            <label class="dinas-filter-label">
                <span class="text-primary"></span> Status Tiket
            </label>
            <select name="status" class="form-select dinas-input">
                <option value="">-- Semua Status --</option>
                <option value="unused" @selected(request('status') === 'unused')>Belum Digunakan</option>
                <option value="used" @selected(request('status') === 'used')>Sudah Check-In</option>
                <option value="expired" @selected(request('status') === 'expired')>Kedaluwarsa</option>
            </select>
        </div>

        <!-- Dari Tanggal -->
        <div class="col-6 col-lg-2 col-md-4">
            <label class="dinas-filter-label">
                <i class="fa-regular fa-calendar text-primary me-1"></i> Dari Tanggal
            </label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control dinas-input" placeholder="dd/mm/yyyy">
        </div>

        <!-- Sampai Tanggal -->
        <div class="col-6 col-lg-2 col-md-4">
            <label class="dinas-filter-label">
                <i class="fa-regular fa-calendar text-primary me-1"></i> Sampai Tanggal
            </label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control dinas-input" placeholder="dd/mm/yyyy">
        </div>

        <!-- Cari Data -->
        <div class="col-6 col-lg-2 col-md-4">
            <label class="dinas-filter-label">
                <span></span> Cari Data
            </label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control dinas-input" placeholder="Kode tiket, no order...">
        </div>

        <!-- Tombol Filter -->
        <div class="col-12 col-lg-1 col-md-4 d-flex gap-1.5">
            <button type="submit" class="btn-filter w-100">
                Filter
            </button>
            @if(request()->hasAny(['mitra_id', 'status', 'start_date', 'end_date', 'q']))
                <a href="{{ route('dinas.ticket-sales.index') }}" class="btn btn-sm btn-light border rounded-3 px-2.5 d-inline-flex align-items-center justify-content-center text-muted" style="height: 38px;" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- 3. Ticket Transactions Table Card -->
<div class="dinas-table-card">
    <div class="dinas-table-header">
        <h6 class="table-card-title">
            <span></span> Rincian Transaksi Tiket Resmi
        </h6>
        <div class="table-card-sub">
            Menampilkan {{ $tickets->count() }} data transaksi &bull; Total Retribusi: <strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
        </div>
    </div>

    <div class="table-responsive">
        <table class="clean-table">
            <thead>
                <tr>
                    <th class="ps-3">NO. TRANSAKSI & TIKET</th>
                    <th>OBJEK WISATA / LAYANAN</th>
                    <th>WAKTU PEMBELIAN</th>
                    <th class="text-end">NOMINAL PAD</th>
                    <th class="text-center">STATUS TIKET</th>
                    <th class="pe-3 text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="ps-3">
                            <span class="font-monospace fw-bold text-dark fs-7 d-block">#{{ $ticket->orderItem?->order?->order_number ?? '-' }}</span>
                            <span class="font-monospace text-muted fs-8">{{ $ticket->ticket_code }}</span>
                        </td>
                        <td>
                            <strong class="text-dark d-block fs-7">{{ $ticket->mitra?->display_name ?? '-' }}</strong>
                            <small class="text-muted fs-8">{{ $ticket->orderItem?->item_name ?? 'Tiket Masuk' }} &bull; {{ $ticket->orderItem?->order?->user?->name ?? ($ticket->orderItem?->order?->user_snapshot['name'] ?? 'Wisatawan') }}</small>
                        </td>
                        <td>
                            <span class="text-dark d-block fs-8">{{ $ticket->created_at?->translatedFormat('d M Y') }}</span>
                            <small class="text-muted fs-8">{{ $ticket->created_at?->translatedFormat('H:i') }} WIB</small>
                        </td>
                        <td class="text-end">
                            <strong class="text-success fs-7">
                                Rp {{ number_format($ticket->orderItem?->unit_price ?? 0, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td class="text-center">
                            @if($ticket->status === 'used')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                    <i class="fa-solid fa-check me-1"></i> Check-in
                                </span>
                            @elseif($ticket->status === 'unused')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                    <i class="fa-solid fa-ticket me-1"></i> Belum Digunakan
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 fs-8">
                                    {{ strtoupper($ticket->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="pe-3 text-center">
                            @if($ticket->orderItem?->order)
                                <a href="{{ route('consumer.orders.show', $ticket->orderItem->order) }}" target="_blank" class="btn btn-sm btn-light border rounded-3 px-2.5 py-1 text-muted fs-8" title="Lihat E-Tiket">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            @else
                                <span class="text-muted fs-8">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-5 text-center text-muted">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-light mb-2 text-warning" style="width: 48px; height: 48px; font-size: 24px;">
                                
                            </div>
                            <p class="mb-0 fs-7 fw-semibold text-secondary">Belum ada data transaksi tiket yang tercatat pada filter ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="table-footer">
        <div class="footer-info">
            Menampilkan {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} dari {{ $tickets->total() }} data transaksi
        </div>
        <div class="footer-pagination">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection
