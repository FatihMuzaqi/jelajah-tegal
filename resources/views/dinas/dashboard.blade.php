@extends('layouts.dinas')

@section('title', 'Dashboard Eksekutif PAD Pariwisata')
@section('page-title', 'Dashboard Eksekutif PAD Pariwisata')
@section('page-description', 'Pusat pemantauan retribusi tiket, Pendapatan Asli Daerah (PAD), dan analitik arus kunjungan destinasi Pemkab/Pemkot Tegal.')

@section('page-actions')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('dinas.ticket-sales.export', request()->query()) }}" class="btn btn-sm rounded-3 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-size: 13px;">
            <i class="fa-solid fa-file-csv text-success"></i>
            <span>Ekspor PAD (CSV)</span>
        </a>
        <a href="{{ route('dinas.ticket-sales.index') }}" class="btn btn-sm rounded-3 px-3.5 py-2 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" style="background: #2563eb; color: #ffffff; border: none; font-size: 13px;">
            <i class="fa-solid fa-ticket"></i>
            <span>Log Penjualan Tiket</span>
        </a>
    </div>
@endsection

@section('content')
<style>
/* Clean Executive Dinas Dashboard Styling */
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
.dinas-select {
    font-size: 12.5px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    padding: 7px 12px;
    color: #0f172a;
    background-color: #ffffff;
    height: 38px;
}
.dinas-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}
.btn-terapkan {
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
.btn-terapkan:hover {
    background: #1d4ed8;
    color: #ffffff;
}

/* Stat Cards */
.dinas-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
@media (max-width: 1200px) {
    .dinas-stat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    .dinas-stat-grid {
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
.icon-blue {
    background: #eff6ff;
    color: #3b82f6;
}
.icon-yellow {
    background: #fefce8;
    color: #eab308;
}
.icon-indigo {
    background: #eff6ff;
    color: #2563eb;
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

/* Chart Cards */
.dinas-chart-card {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    height: 100%;
}
.chart-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}
.chart-card-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.chart-card-sub {
    font-size: 12px;
    color: #64748b;
}
.badge-pill-light {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 99px;
}

/* Table Cards */
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
    display: flex;
    justify-content: space-between;
    align-items: center;
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
</style>

<!-- 1. Executive Filter Panel (Clean Single Card) -->
<div class="dinas-filter-card">
    <form method="GET" action="{{ route('dinas.dashboard') }}" class="row g-3 align-items-end">
        <!-- Objek Wisata Pemda -->
        <div class="col-12 col-lg-4 col-md-4">
            <label class="dinas-filter-label">
                <span>🏛️</span> Objek Wisata Pemda
            </label>
            <select name="mitra_id" class="form-select dinas-select">
                <option value="">-- Seluruh Objek Wisata Pemda (Semua) --</option>
                @foreach($dinasMitras as $mitra)
                    <option value="{{ $mitra->id }}" @selected($selectedMitraId === $mitra->id)>
                        {{ $mitra->display_name }} ({{ $mitra->legal_name }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Periode Bulan -->
        <div class="col-12 col-lg-3 col-md-3">
            <label class="dinas-filter-label">
                <i class="fa-regular fa-calendar text-primary me-1"></i> Periode Bulan
            </label>
            <select name="month" class="form-select dinas-select">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($selectedMonth === $m)>
                        {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- Tahun Anggaran -->
        <div class="col-12 col-lg-3 col-md-3">
            <label class="dinas-filter-label">
                <i class="fa-regular fa-calendar text-primary me-1"></i> Tahun Anggaran
            </label>
            <select name="year" class="form-select dinas-select">
                @for($y = now()->year; $y >= 2024; $y--)
                    <option value="{{ $y }}" @selected($selectedYear === $y)>Tahun {{ $y }}</option>
                @endfor
            </select>
        </div>

        <!-- Tombol Terapkan -->
        <div class="col-12 col-lg-2 col-md-2 d-flex gap-2">
            <button type="submit" class="btn-terapkan w-100">
                <i class="fa-solid fa-bolt text-warning"></i>
                <span>Terapkan</span>
            </button>
            @if($selectedMitraId || $selectedMonth !== (int)now()->month || $selectedYear !== (int)now()->year)
                <a href="{{ route('dinas.dashboard') }}" class="btn btn-sm btn-light border rounded-3 px-3 d-inline-flex align-items-center justify-content-center text-muted" style="height: 38px;" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- 2. KPI Stat Cards (4 Columns) -->
<div class="dinas-stat-grid">
    <!-- Card 1: Retribusi Tahun Berjalan (Green Highlight Card) -->
    <div class="dinas-stat-card dinas-stat-card-green">
        <div class="stat-top-row">
            <span class="stat-title">RETRIBUSI TAHUN {{ $selectedYear }}</span>
            <div class="stat-icon-square icon-translucent">
                <i class="fa-solid fa-landmark"></i>
            </div>
        </div>
        <div class="stat-amount">
            Rp {{ number_format($yearRevenue, 0, ',', '.') }}
        </div>
        <div class="stat-bottom-row">
            <span>{{ number_format($yearTicketsCount, 0, ',', '.') }} tiket terjual</span>
            <span>Total TA {{ $selectedYear }}</span>
        </div>
    </div>

    <!-- Card 2: Bulan Terpilih -->
    <div class="dinas-stat-card">
        <div class="stat-top-row">
            <span class="stat-title">BULAN TERPILIH ({{ strtoupper(\Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('M Y')) }})</span>
            <div class="stat-icon-square icon-blue">
                <i class="fa-solid fa-chart-column"></i>
            </div>
        </div>
        <div class="stat-amount">
            Rp {{ number_format($monthRevenue, 0, ',', '.') }}
        </div>
        <div class="stat-bottom-row">
            <span>{{ number_format($monthTicketsCount, 0, ',', '.') }} tiket bulan ini</span>
            <span class="badge-sub badge-sub-blue">Bulan {{ $selectedMonth }}</span>
        </div>
    </div>

    <!-- Card 3: Realisasi Hari Ini -->
    <div class="dinas-stat-card">
        <div class="stat-top-row">
            <span class="stat-title">REALISASI HARI INI</span>
            <div class="stat-icon-square icon-yellow">
                <i class="fa-solid fa-bolt text-warning"></i>
            </div>
        </div>
        <div class="stat-amount">
            Rp {{ number_format($todayRevenue, 0, ',', '.') }}
        </div>
        <div class="stat-bottom-row">
            <span>{{ number_format($todayTicketsCount, 0, ',', '.') }} tiket dibeli hari ini</span>
            <span class="fw-bold" style="color: #16a34a; font-size: 11.5px;">
                <i class="fa-solid fa-circle" style="font-size: 7px; vertical-align: middle;"></i> Real-time
            </span>
        </div>
    </div>

    <!-- Card 4: Check-in Loket -->
    <div class="dinas-stat-card">
        <div class="stat-top-row">
            <span class="stat-title">CHECK-IN LOKET</span>
            <div class="stat-icon-square icon-indigo">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <div class="stat-amount">
            {{ number_format($checkedInVisitorsCount, 0, ',', '.') }} <span class="fs-6 fw-semibold text-muted">Orang</span>
        </div>
        <div class="stat-bottom-row">
            <span>Wisatawan check-in</span>
            <span class="badge-sub badge-sub-green">Terealisasi</span>
        </div>
    </div>
</div>

<!-- 3. Interactive Charts Row (2 Columns) -->
<div class="row g-3 mb-4">
    <!-- Chart 1: Tren Retribusi Bulanan -->
    <div class="col-12 col-lg-6">
        <div class="dinas-chart-card">
            <div class="chart-card-header">
                <div>
                    <h6 class="chart-card-title">
                        <span>📊</span> Tren Retribusi Bulanan (Tahun {{ $selectedYear }})
                    </h6>
                    <div class="chart-card-sub">Perbandingan realisasi PAD & volume tiket Januari s.d. Desember</div>
                </div>
                <span class="badge-pill-light">12 Bulan</span>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Arus Retribusi Harian -->
    <div class="col-12 col-lg-6">
        <div class="dinas-chart-card">
            <div class="chart-card-header">
                <div>
                    <h6 class="chart-card-title">
                        <span>📈</span> Arus Retribusi Harian
                    </h6>
                    <div class="chart-card-sub">Realisasi per hari {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }} (Tgl 1 - {{ count($dailyLabels) }})</div>
                </div>
                <span class="badge-pill-light">Harian</span>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 4. Tables: Rankings per Destinasi & Recent Transactions -->
<div class="row g-3">
    <!-- Destinasi Wisata Pemda Rankings -->
    <div class="col-12 col-lg-7">
        <div class="dinas-table-card">
            <div class="dinas-table-header">
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Peringkat Kontribusi PAD Destinasi ({{ $selectedYear }})</h6>
                    <small class="text-muted fs-8">Kontribusi retribusi dan arus pengunjung per objek wisata Pemda</small>
                </div>
                <a href="{{ route('dinas.destinations.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-8 fw-semibold">
                    Semua Destinasi &rarr;
                </a>
            </div>
            <div class="table-responsive">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th class="ps-3 text-center" style="width: 50px;">No.</th>
                            <th>Destinasi Wisata</th>
                            <th class="text-center">Tiket Terjual</th>
                            <th class="text-center">Pengunjung Masuk</th>
                            <th class="pe-3 text-end">Total PAD (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($destinationRankings as $idx => $item)
                            <tr>
                                <td class="ps-3 text-center">
                                    @if($idx === 0)
                                        <span class="badge rounded-circle p-1 bg-warning text-dark fs-8 fw-bold" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                    @elseif($idx === 1)
                                        <span class="badge rounded-circle p-1 bg-secondary text-white fs-8 fw-bold" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                    @elseif($idx === 2)
                                        <span class="badge rounded-circle p-1 bg-danger-subtle text-danger fs-8 fw-bold" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                    @else
                                        <span class="text-muted fw-semibold fs-8">{{ $idx + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div style="width: 34px; height: 34px; min-width: 34px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                            <i class="fa-solid fa-landmark"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark d-block fs-7">{{ $item['mitra']->display_name }}</strong>
                                            <small class="text-muted fs-8">{{ $item['mitra']->legal_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-dark">{{ number_format($item['tickets_count'], 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2.5 py-1 fs-8 fw-semibold">
                                        {{ number_format($item['visitors_count'], 0, ',', '.') }} org
                                    </span>
                                </td>
                                <td class="pe-3 text-end">
                                    <strong class="text-success fs-7">
                                        Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-muted fs-8">Belum ada data destinasi dinas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-12 col-lg-5">
        <div class="dinas-table-card">
            <div class="dinas-table-header">
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">5 Transaksi Tiket Terakhir</h6>
                    <small class="text-muted fs-8">Pembelian tiket resmi dinas yang baru diselesaikan</small>
                </div>
                <a href="{{ route('dinas.ticket-sales.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-8 fw-semibold">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="table-responsive">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th class="ps-3">Pesanan</th>
                            <th>Destinasi</th>
                            <th class="pe-3 text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="ps-3">
                                    <span class="font-monospace fw-bold text-dark fs-8 d-block">#{{ $order->order_number }}</span>
                                    <small class="text-muted fs-8">{{ $order->user_snapshot['name'] ?? ($order->user?->name ?? 'Wisatawan') }}</small>
                                </td>
                                <td>
                                    <span class="fs-8 text-dark fw-medium d-block">{{ $order->mitra?->display_name ?? '-' }}</span>
                                    <small class="text-muted fs-8">{{ $order->paid_at?->translatedFormat('d M H:i') }} WIB</small>
                                </td>
                                <td class="pe-3 text-end">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fs-8 fw-bold">
                                        +Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-muted fs-8">Belum ada transaksi tiket dinas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Monthly Revenue Chart
    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: @json($monthLabels),
            datasets: [
                {
                    label: 'Retribusi PAD (Rp)',
                    data: @json($monthlyRevenueData),
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    borderRadius: 6,
                    yAxisID: 'y',
                    maxBarThickness: 28,
                },
                {
                    label: 'Jumlah Tiket Terjual',
                    data: @json($monthlyTicketsData),
                    type: 'line',
                    borderColor: '#10b981',
                    backgroundColor: '#10b981',
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    tension: 0.35,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000) + ' Jt';
                            if (value >= 1000) return 'Rp ' + (value/1000) + ' Rb';
                            return 'Rp ' + value;
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // 2. Daily Revenue Chart
    const ctxDaily = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Retribusi Harian (Rp)',
                data: @json($dailyRevenueData),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                fill: true,
                tension: 0.35,
                borderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    }
                },
                y: {
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return (value/1000000) + 'M';
                            if (value >= 1000) return (value/1000) + 'k';
                            return value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
