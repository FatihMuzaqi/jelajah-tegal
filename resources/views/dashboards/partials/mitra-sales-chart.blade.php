@php($trends = $salesTrends ?? ['weekly' => ['labels' => [], 'revenue' => [], 'tickets' => []], 'monthly' => ['labels' => [], 'revenue' => [], 'tickets' => []], 'yearly' => ['labels' => [], 'revenue' => [], 'tickets' => []]])

<div class="content-card" style="min-width: 0;">
    <div class="card-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 20px;">
        <div>
            <h2 class="h6 fw-bold mb-1 text-dark d-flex align-items-center gap-2" id="mitraChartTitle" style="font-size: 15px; margin: 0;">
                <i class="fa-solid fa-chart-column text-primary"></i>
                <span>Tren Penjualan Tiket & Layanan</span>
            </h2>
            <p class="text-muted small mb-0" id="mitraChartSubtitle" style="font-size: 12px;">
                Statistik pendapatan dan volume penjualan tiket per bulan
            </p>
        </div>

        <!-- Periode Toggle Tabs -->
        <div class="d-flex align-items-center gap-1 p-1 bg-light rounded-pill border" style="font-size: 12px; width: fit-content;">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 mitra-period-btn text-muted" id="btnMitraWeekly" onclick="switchMitraPeriod('weekly')">
                Minggu
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 mitra-period-btn btn-primary text-white shadow-xs" id="btnMitraMonthly" onclick="switchMitraPeriod('monthly')">
                Bulan
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 mitra-period-btn text-muted" id="btnMitraYearly" onclick="switchMitraPeriod('yearly')">
                Tahun
            </button>
        </div>
    </div>
    <div class="card-body" style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="mitraSalesChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
let mitraChart = null;

const mitraTrendData = {
    weekly: {
        title: 'Tren Penjualan Tiket Mingguan (7 Hari Terakhir)',
        subtitle: 'Statistik pendapatan dan jumlah tiket 7 hari terakhir',
        labels: @json($trends['weekly']['labels'] ?? []),
        revenue: @json($trends['weekly']['revenue'] ?? []),
        tickets: @json($trends['weekly']['tickets'] ?? [])
    },
    monthly: {
        title: 'Tren Penjualan Tiket Bulanan (Tahun {{ now()->year }})',
        subtitle: 'Statistik pendapatan dan volume penjualan tiket Januari s.d. Desember',
        labels: @json($trends['monthly']['labels'] ?? []),
        revenue: @json($trends['monthly']['revenue'] ?? []),
        tickets: @json($trends['monthly']['tickets'] ?? [])
    },
    yearly: {
        title: 'Tren Penjualan Tiket Tahunan (5 Tahun Terakhir)',
        subtitle: 'Perbandingan akumulasi pendapatan dan tiket antar tahun',
        labels: @json($trends['yearly']['labels'] ?? []),
        revenue: @json($trends['yearly']['revenue'] ?? []),
        tickets: @json($trends['yearly']['tickets'] ?? [])
    }
};

function switchMitraPeriod(period) {
    if (!mitraChart || !mitraTrendData[period]) return;

    ['weekly', 'monthly', 'yearly'].forEach(p => {
        const btn = document.getElementById('btnMitra' + p.charAt(0).toUpperCase() + p.slice(1));
        if (btn) {
            if (p === period) {
                btn.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 mitra-period-btn btn-primary text-white shadow-xs';
            } else {
                btn.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 mitra-period-btn text-muted';
            }
        }
    });

    const data = mitraTrendData[period];
    const titleEl = document.getElementById('mitraChartTitle');
    const subEl = document.getElementById('mitraChartSubtitle');
    if (titleEl) titleEl.innerHTML = '<i class="fa-solid fa-chart-column text-primary"></i> <span>' + data.title + '</span>';
    if (subEl) subEl.textContent = data.subtitle;

    mitraChart.data.labels = data.labels;
    mitraChart.data.datasets[0].data = data.revenue;
    mitraChart.data.datasets[1].data = data.tickets;
    mitraChart.update();
}

document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('mitraSalesChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    mitraChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: mitraTrendData.monthly.labels,
            datasets: [
                {
                    label: 'Pendapatan (Rp)',
                    data: mitraTrendData.monthly.revenue,
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderRadius: 6,
                    yAxisID: 'y',
                    maxBarThickness: 24,
                },
                {
                    label: 'Jumlah Tiket Terjual',
                    data: mitraTrendData.monthly.tickets,
                    backgroundColor: 'rgba(59, 130, 246, 0.85)',
                    borderRadius: 6,
                    yAxisID: 'y1',
                    maxBarThickness: 24,
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
                    labels: {
                        boxWidth: 12,
                        font: { size: 12, weight: 'bold' }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000).toLocaleString('id-ID') + ' Jt';
                            if (value >= 1000) return 'Rp ' + (value/1000).toLocaleString('id-ID') + ' Rb';
                            return 'Rp ' + value;
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('id-ID') + ' tkt';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
