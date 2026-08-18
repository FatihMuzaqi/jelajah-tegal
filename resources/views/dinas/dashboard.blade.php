@extends('layouts.dinas')

@section('title', 'Dashboard Eksekutif Monitoring PAD Dinas')
@section('page-title', 'Dashboard Eksekutif PAD Pariwisata')
@section('page-description', 'Pusat pemantauan retribusi tiket, Pendapatan Asli Daerah (PAD), dan analitik arus kunjungan destinasi Pemkab/Pemkot Tegal.')

@section('page-actions')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('dinas.ticket-sales.export', request()->query()) }}" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 fw-semibold fs-7 d-inline-flex align-items-center gap-2 shadow-2xs">
            <i class="fa-solid fa-file-csv text-success"></i>
            <span>Ekspor PAD (CSV)</span>
        </a>
        <a href="{{ route('dinas.ticket-sales.index') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold fs-7 shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-ticket"></i>
            <span>Log Penjualan Tiket</span>
        </a>
    </div>
@endsection

@section('content')
    <!-- 1. Executive Filter Panel -->
    <div class="dinas-panel mb-4">
        <form method="GET" action="{{ route('dinas.dashboard') }}" class="row g-3 align-items-end">
            <!-- Filter Destinasi -->
            <div class="col-12 col-md-4">
                <label class="form-label text-dark fs-8 fw-bold mb-1.5 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-landmark text-primary"></i> Objek Wisata Pemda
                </label>
                <select name="mitra_id" class="form-select form-select-sm fs-7 rounded-3 border-light-subtle shadow-2xs">
                    <option value="">-- Seluruh Objek Wisata Pemda ({{ $dinasMitras->count() }}) --</option>
                    @foreach($dinasMitras as $mitra)
                        <option value="{{ $mitra->id }}" @selected($selectedMitraId === $mitra->id)>
                            🏛️ {{ $mitra->display_name }} ({{ $mitra->legal_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Bulan -->
            <div class="col-6 col-md-3">
                <label class="form-label text-dark fs-8 fw-bold mb-1.5 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-calendar text-primary"></i> Periode Bulan
                </label>
                <select name="month" class="form-select form-select-sm fs-7 rounded-3 border-light-subtle shadow-2xs">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected($selectedMonth === $m)>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-6 col-md-3">
                <label class="form-label text-dark fs-8 fw-bold mb-1.5 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i> Tahun Anggaran
                </label>
                <select name="year" class="form-select form-select-sm fs-7 rounded-3 border-light-subtle shadow-2xs">
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" @selected($selectedYear === $y)>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold py-2 fs-7 rounded-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan</span>
                </button>
                @if($selectedMitraId || $selectedMonth !== (int)now()->month || $selectedYear !== (int)now()->year)
                    <a href="{{ route('dinas.dashboard') }}" class="btn btn-sm btn-light border rounded-3 px-3 py-2 text-muted shadow-2xs d-inline-flex align-items-center justify-content-center" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 2. KPI Executive Stat Cards -->
    <div class="dinas-grid">
        <!-- Hero Card: Total PAD Tahun Ini -->
        <div class="dinas-card dinas-hero-card">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="dinas-badge text-white" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px);">
                        <i class="fa-solid fa-building-columns"></i> Retribusi Tahun {{ $selectedYear }}
                    </span>
                    <div class="dinas-icon-box" style="background: #ffffff; color: #1e3a8a;">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold text-white mb-0" style="font-size: clamp(20px, 2vw, 25px); letter-spacing: -0.5px;">
                    Rp {{ number_format($yearRevenue, 0, ',', '.') }}
                </h2>
            </div>
            <div class="mt-3 pt-2.5 border-top border-white border-opacity-20 d-flex justify-content-between align-items-center">
                <small class="text-white-50 fs-8">{{ number_format($yearTicketsCount, 0, ',', '.') }} tiket terjual</small>
                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2 py-0.5 fs-8">Tahun {{ $selectedYear }}</span>
            </div>
        </div>

        <!-- PAD Bulan Ini -->
        <div class="dinas-card">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="dinas-badge bg-primary-subtle text-primary border border-primary-subtle">
                        <i class="fa-solid fa-calendar-days"></i> Bulan {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('M Y') }}
                    </span>
                    <div class="dinas-icon-box bg-primary-subtle text-primary border border-primary-subtle">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold text-dark mb-0" style="font-size: clamp(19px, 1.8vw, 24px); letter-spacing: -0.5px;">
                    Rp {{ number_format($monthRevenue, 0, ',', '.') }}
                </h3>
            </div>
            <div class="mt-3 pt-2.5 border-top border-light d-flex justify-content-between align-items-center">
                <small class="text-muted fs-8">{{ number_format($monthTicketsCount, 0, ',', '.') }} tiket bulan ini</small>
                <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 fs-8">Bulan {{ $selectedMonth }}</span>
            </div>
        </div>

        <!-- PAD Hari Ini -->
        <div class="dinas-card">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="dinas-badge bg-success-subtle text-success border border-success-subtle">
                        <i class="fa-solid fa-bolt"></i> Realisasi Hari Ini
                    </span>
                    <div class="dinas-icon-box bg-success-subtle text-success border border-success-subtle">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold text-success mb-0" style="font-size: clamp(19px, 1.8vw, 24px); letter-spacing: -0.5px;">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </h3>
            </div>
            <div class="mt-3 pt-2.5 border-top border-light d-flex justify-content-between align-items-center">
                <small class="text-muted fs-8">{{ number_format($todayTicketsCount, 0, ',', '.') }} tiket dibeli hari ini</small>
                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fs-8">Real-time</span>
            </div>
        </div>

        <!-- Pengunjung Check-In Gatekeeper -->
        <div class="dinas-card">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="dinas-badge bg-info-subtle text-info-emphasis border border-info-subtle">
                        <i class="fa-solid fa-qrcode"></i> Check-in Loket
                    </span>
                    <div class="dinas-icon-box bg-info-subtle text-info-emphasis border border-info-subtle">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold text-dark mb-0" style="font-size: clamp(19px, 1.8vw, 24px); letter-spacing: -0.5px;">
                    {{ number_format($checkedInVisitorsCount, 0, ',', '.') }} <span class="fs-7 fw-normal text-muted">Orang</span>
                </h3>
            </div>
            <div class="mt-3 pt-2.5 border-top border-light d-flex justify-content-between align-items-center">
                <small class="text-muted fs-8">Wisatawan check-in bulan ini</small>
                <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2 py-0.5 fs-8">Terealisasi</span>
            </div>
        </div>
    </div>

    <!-- 3. Interactive Charts (Chart.js) -->
    <div class="row g-4 mb-4">
        <!-- Monthly Revenue & Volume Chart (12 Months) -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="dinas-icon-box bg-primary-subtle text-primary" style="width: 36px; height: 36px; min-width: 36px; font-size: 15px;">
                            <i class="fa-solid fa-chart-column"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Tren Retribusi Bulanan (Tahun {{ $selectedYear }})</h6>
                            <small class="text-muted fs-8">Perbandingan realisasi PAD & volume tiket Januari s.d. Desember</small>
                        </div>
                    </div>
                    <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 fs-8">12 Bulan</span>
                </div>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Trend Chart (Selected Month) -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="dinas-icon-box bg-success-subtle text-success" style="width: 36px; height: 36px; min-width: 36px; font-size: 15px;">
                            <i class="fa-solid fa-wave-square"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Arus Retribusi Harian</h6>
                            <small class="text-muted fs-8">{{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }} (Tgl 1 - {{ count($dailyLabels) }})</small>
                        </div>
                    </div>
                    <span class="badge bg-light text-success border rounded-pill px-2.5 py-1 fs-8">Harian</span>
                </div>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Tables: Rankings per Destinasi & Recent Transactions -->
    <div class="row g-4">
        <!-- Destinasi Wisata Pemda Rankings -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
                <div class="card-header bg-white border-bottom p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="dinas-icon-box bg-primary-subtle text-primary" style="width: 36px; height: 36px; min-width: 36px; font-size: 15px;">
                                <i class="fa-solid fa-ranking-star"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-6">Peringkat Kontribusi PAD Destinasi ({{ $selectedYear }})</h6>
                                <small class="text-muted fs-8">Kontribusi retribusi dan arus pengunjung per objek wisata Pemda</small>
                            </div>
                        </div>
                        <a href="{{ route('dinas.destinations.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-8 fw-semibold">
                            Semua Destinasi &rarr;
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table dinas-table align-middle mb-0 fs-7">
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
                                            <span class="badge rounded-circle p-1.5 bg-warning text-dark fs-8 fw-bold" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                        @elseif($idx === 1)
                                            <span class="badge rounded-circle p-1.5 bg-secondary text-white fs-8 fw-bold" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                        @elseif($idx === 2)
                                            <span class="badge rounded-circle p-1.5 bg-danger-subtle text-danger fs-8 fw-bold" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                        @else
                                            <span class="text-muted fw-semibold fs-8">{{ $idx + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="dinas-icon-box bg-primary-subtle text-primary border border-primary-subtle rounded-3" style="width: 36px; height: 36px; min-width: 36px; font-size: 14px;">
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
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
                <div class="card-header bg-white border-bottom p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="dinas-icon-box bg-success-subtle text-success" style="width: 36px; height: 36px; min-width: 36px; font-size: 15px;">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-6">5 Transaksi Tiket Terakhir</h6>
                                <small class="text-muted fs-8">Pembelian tiket resmi dinas yang baru diselesaikan</small>
                            </div>
                        </div>
                        <a href="{{ route('dinas.ticket-sales.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-8 fw-semibold">
                            Lihat Semua &rarr;
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table dinas-table align-middle mb-0 fs-7">
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
                                        <span class="font-monospace fw-bold text-dark fs-8 d-block">{{ $order->order_number }}</span>
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
                            maxBarThickness: 32,
                        },
                        {
                            label: 'Jumlah Tiket Terjual',
                            data: @json($monthlyTicketsData),
                            type: 'line',
                            borderColor: '#10b981',
                            backgroundColor: '#10b981',
                            borderWidth: 2.5,
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
                        borderWidth: 2.5,
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
