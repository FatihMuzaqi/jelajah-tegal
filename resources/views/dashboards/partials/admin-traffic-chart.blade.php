@php
    $traffic = $visitorTraffic ?? [
        'weekly' => ['labels' => [], 'visitors' => [], 'pageviews' => []],
        'monthly' => ['labels' => [], 'visitors' => [], 'pageviews' => []],
        'yearly' => ['labels' => [], 'visitors' => [], 'pageviews' => []],
        'metrics' => [
            'today_visitors' => 0,
            'month_visitors' => 0,
            'month_pageviews' => 0,
            'avg_duration' => '3m 42s',
            'engagement_rate' => '78.5%',
        ]
    ];
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
    <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1 fs-6 d-flex align-items-center gap-2" id="adminTrafficChartTitle">
                <i class="fa-solid fa-chart-line text-primary"></i>
                <span>Trafik Pengunjung &amp; Kunjungan Website</span>
            </h5>
            <p class="text-muted fs-8 mb-0" id="adminTrafficChartSubtitle">
                Statistik pengunjung unik dan total page views per bulan (Tahun {{ now()->year }})
            </p>
        </div>

        <!-- Periode Switcher Tabs -->
        <div class="d-flex align-items-center gap-1 p-1 bg-light rounded-pill border" style="font-size: 12px; width: fit-content;">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 admin-traffic-btn text-muted" id="btnAdminWeekly" onclick="switchAdminTrafficPeriod('weekly')">
                Minggu
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 admin-traffic-btn btn-primary text-white shadow-xs" id="btnAdminMonthly" onclick="switchAdminTrafficPeriod('monthly')">
                Bulan
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 admin-traffic-btn text-muted" id="btnAdminYearly" onclick="switchAdminTrafficPeriod('yearly')">
                Tahun
            </button>
        </div>
    </div>

    <!-- Mini KPI Highlights -->
    <div class="bg-light px-4 py-3 border-bottom">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-users fs-7"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fs-9 fw-semibold text-uppercase">Pengunjung Hari Ini</small>
                        <strong class="text-dark fs-7" id="statTodayVisitors">{{ number_format($traffic['metrics']['today_visitors'] ?? 0, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-user-check fs-7"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fs-9 fw-semibold text-uppercase">Pengunjung Bulan Ini</small>
                        <strong class="text-dark fs-7" id="statMonthVisitors">{{ number_format($traffic['metrics']['month_visitors'] ?? 0, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 bg-info-subtle text-info p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-eye fs-7"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fs-9 fw-semibold text-uppercase">Total Page Views</small>
                        <strong class="text-dark fs-7" id="statMonthViews">{{ number_format($traffic['metrics']['month_pageviews'] ?? 0, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-stopwatch fs-7"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fs-9 fw-semibold text-uppercase">Durasi Sesi &amp; Retensi</small>
                        <strong class="text-dark fs-7">{{ $traffic['metrics']['avg_duration'] ?? '3m 42s' }} ({{ $traffic['metrics']['engagement_rate'] ?? '78%' }})</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Canvas Container -->
    <div class="card-body p-4 bg-white">
        <div style="position: relative; height: 320px; width: 100%;">
            <canvas id="adminTrafficChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
let adminTrafficChartInstance = null;

const adminTrafficDataset = {
    weekly: {
        title: 'Trafik Pengunjung Mingguan (7 Hari Terakhir)',
        subtitle: 'Statistik pengunjung unik dan page views harian dalam 7 hari terakhir',
        labels: @json($traffic['weekly']['labels'] ?? []),
        visitors: @json($traffic['weekly']['visitors'] ?? []),
        pageviews: @json($traffic['weekly']['pageviews'] ?? [])
    },
    monthly: {
        title: 'Trafik Pengunjung Bulanan (Tahun {{ now()->year }})',
        subtitle: 'Statistik pengunjung unik dan total page views per bulan (Januari s.d. Desember)',
        labels: @json($traffic['monthly']['labels'] ?? []),
        visitors: @json($traffic['monthly']['visitors'] ?? []),
        pageviews: @json($traffic['monthly']['pageviews'] ?? [])
    },
    yearly: {
        title: 'Trafik Pengunjung Tahunan (5 Tahun Terakhir)',
        subtitle: 'Perbandingan pertumbuhan trafik pengunjung website antar tahun',
        labels: @json($traffic['yearly']['labels'] ?? []),
        visitors: @json($traffic['yearly']['visitors'] ?? []),
        pageviews: @json($traffic['yearly']['pageviews'] ?? [])
    }
};

function renderAdminTrafficChart(period) {
    const data = adminTrafficDataset[period];
    if (!data) return;

    const ctx = document.getElementById('adminTrafficChart');
    if (!ctx) return;

    if (adminTrafficChartInstance) {
        adminTrafficChartInstance.destroy();
    }

    const ctx2d = ctx.getContext('2d');

    // Create soft gradients
    const gradVisitors = ctx2d.createLinearGradient(0, 0, 0, 300);
    gradVisitors.addColorStop(0, 'rgba(31, 122, 92, 0.35)');
    gradVisitors.addColorStop(1, 'rgba(31, 122, 92, 0.0)');

    const gradViews = ctx2d.createLinearGradient(0, 0, 0, 300);
    gradViews.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
    gradViews.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    adminTrafficChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Pengunjung Unik (Visitors)',
                    data: data.visitors,
                    borderColor: '#1f7a5c',
                    backgroundColor: gradVisitors,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#1f7a5c',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    yAxisID: 'y'
                },
                {
                    label: 'Kunjungan Halaman (Page Views)',
                    data: data.pageviews,
                    borderColor: '#3b82f6',
                    backgroundColor: gradViews,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5.5,
                    yAxisID: 'y1'
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
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 12,
                        boxHeight: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 12, weight: '600' },
                        color: '#334155'
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#ffffff',
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    boxPadding: 6,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.parsed.y || 0;
                            return ' ' + label + ': ' + new Intl.NumberFormat('id-ID').format(value) + (context.datasetIndex === 0 ? ' Pengunjung' : ' Views');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#64748b' }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    grid: { color: 'rgba(226, 232, 240, 0.6)' },
                    ticks: {
                        font: { size: 11 },
                        color: '#1f7a5c',
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    ticks: {
                        font: { size: 11 },
                        color: '#3b82f6',
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });

    document.getElementById('adminTrafficChartTitle').innerHTML = '<i class="fa-solid fa-chart-line text-primary"></i> <span>' + data.title + '</span>';
    document.getElementById('adminTrafficChartSubtitle').innerText = data.subtitle;
}

function switchAdminTrafficPeriod(period) {
    const buttons = document.querySelectorAll('.admin-traffic-btn');
    buttons.forEach(btn => {
        btn.classList.remove('btn-primary', 'text-white', 'shadow-xs');
        btn.classList.add('text-muted');
    });

    const activeBtnId = period === 'weekly' ? 'btnAdminWeekly' : (period === 'yearly' ? 'btnAdminYearly' : 'btnAdminMonthly');
    const activeBtn = document.getElementById(activeBtnId);
    if (activeBtn) {
        activeBtn.classList.remove('text-muted');
        activeBtn.classList.add('btn-primary', 'text-white', 'shadow-xs');
    }

    renderAdminTrafficChart(period);
}

function initAdminTrafficChart() {
    if (typeof Chart !== 'undefined') {
        renderAdminTrafficChart('monthly');
    } else {
        let attempts = 0;
        const checkChart = setInterval(() => {
            attempts++;
            if (typeof Chart !== 'undefined') {
                clearInterval(checkChart);
                renderAdminTrafficChart('monthly');
            } else if (attempts >= 50) {
                clearInterval(checkChart);
            }
        }, 100);
    }
}

document.addEventListener('DOMContentLoaded', initAdminTrafficChart);
document.addEventListener('livewire:navigated', initAdminTrafficChart);
</script>
@endpush
