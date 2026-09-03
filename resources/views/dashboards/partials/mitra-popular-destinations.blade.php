@php($destData = $popularDestinations ?? ['labels' => [], 'tickets' => [], 'revenue' => [], 'colors' => [], 'items' => []])
@php($totalTickets = array_sum($destData['tickets'] ?? []))
@php($totalRevenue = array_sum($destData['revenue'] ?? []))
@php($totalDestinations = count($destData['items'] ?? []))

<div class="content-card mb-4" style="min-width: 0; width: 100%;">
    <div class="card-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 20px;">
        <div>
            <h2 class="h6 fw-bold mb-1 text-dark d-flex align-items-center gap-2" style="font-size: 15px; margin: 0;">
                <i class="fa-solid fa-fire text-danger"></i>
                <span>Destinasi Wisata Terpopuler Mitra</span>
            </h2>
            <p class="text-muted small mb-0" style="font-size: 12px;">
                Peringkat performa dan kontribusi penjualan tiket per objek wisata Anda
            </p>
        </div>

        <div class="d-flex align-items-center gap-1 p-1 bg-light rounded-pill border" style="font-size: 12px;">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 pop-toggle-btn btn-primary text-white shadow-xs" id="btnPopTickets" onclick="switchPopMetric('tickets')">
                Berdasarkan Tiket
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 pop-toggle-btn text-muted" id="btnPopRevenue" onclick="switchPopMetric('revenue')">
                Berdasarkan Omset (Rp)
            </button>
        </div>
    </div>
    
    <div class="card-body" style="padding: 24px 20px;">
        @if(empty($destData['items']))
            <x-empty-state title="Belum ada data destinasi" description="Data destinasi terpopuler akan tampil setelah ada transaksi penjualan tiket." compact />
        @else
            <div class="row g-4 align-items-center">
                <!-- Sisi Kiri: Doughnut Chart dengan Dynamic Center Badge -->
                <div class="col-12 col-lg-5 col-md-6 text-center">
                    <div style="position: relative; height: 280px; width: 100%; max-width: 320px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                        <canvas id="popularDestinationsChart"></canvas>

                        <!-- Center Dynamic Overlay -->
                        <div id="doughnutCenterInfo" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none; width: 155px; z-index: 5; transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);">
                            <div id="centerIcon" style="font-size: 13px; margin-bottom: 2px;">
                                <i class="fa-solid fa-trophy text-warning"></i>
                            </div>
                            <span id="centerSubtitle" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; display: block; line-height: 1.2;">
                                TOTAL PENJUALAN
                            </span>
                            <div id="centerValue" style="font-size: 17px; font-weight: 800; color: #0f172a; line-height: 1.2; margin-top: 2px; word-break: break-word;">
                                {{ number_format($totalTickets, 0, ',', '.') }} Tiket
                            </div>
                            <div id="centerDetail" style="margin-top: 3px;">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 10px;">
                                    {{ $totalDestinations }} Destinasi Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan: List Peringkat Destinasi -->
                <div class="col-12 col-lg-7 col-md-6">
                    <div class="d-flex flex-column gap-2.5">
                        @foreach($destData['items'] as $index => $item)
                            @php($color = $destData['colors'][$index] ?? '#10b981')
                            <div class="p-3 rounded-3 border d-flex flex-wrap align-items-center justify-content-between gap-2 dest-rank-card" 
                                 id="destCard{{ $index }}"
                                 onmouseenter="highlightDoughnutSlice({{ $index }})" 
                                 onmouseleave="resetDoughnutSlice()"
                                 style="background: #fafbfd; transition: all 0.2s ease; cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle fw-bold text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 12px; background: {{ $color }}; flex-shrink: 0; box-shadow: 0 2px 6px {{ $color }}40;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark fs-7 mb-0.5">{{ $item['name'] }}</strong>
                                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 11.5px;">
                                            <span><i class="fa-solid fa-tag me-1"></i> {{ $item['category'] }}</span>
                                            <span>&middot;</span>
                                            <span><i class="fa-solid fa-location-dot me-1"></i> {{ $item['region'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold fs-8 d-block mb-1">
                                        {{ number_format($item['tickets_count'], 0, ',', '.') }} Tiket
                                    </span>
                                    <strong class="text-success fs-8">
                                        Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                                    </strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
let popularChart = null;

const popChartData = {
    labels: @json($destData['labels'] ?? []),
    tickets: @json($destData['tickets'] ?? []),
    revenue: @json($destData['revenue'] ?? []),
    colors: @json($destData['colors'] ?? []),
    totalTickets: {{ $totalTickets }},
    totalRevenue: {{ $totalRevenue }},
    totalDestinations: {{ $totalDestinations }}
};

let currentPopMetric = 'tickets';

function updateCenterDefault() {
    const centerIcon = document.getElementById('centerIcon');
    const centerSub = document.getElementById('centerSubtitle');
    const centerVal = document.getElementById('centerValue');
    const centerDetail = document.getElementById('centerDetail');
    const centerBox = document.getElementById('doughnutCenterInfo');

    if (!centerBox) return;

    centerBox.style.transform = 'translate(-50%, -50%) scale(1)';
    centerIcon.innerHTML = '<i class="fa-solid fa-trophy text-warning"></i>';

    if (currentPopMetric === 'tickets') {
        centerSub.textContent = 'TOTAL PENJUALAN';
        centerVal.textContent = popChartData.totalTickets.toLocaleString('id-ID') + ' Tiket';
        centerDetail.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 10px;">' + popChartData.totalDestinations + ' Destinasi Aktif</span>';
    } else {
        centerSub.textContent = 'TOTAL OMSET';
        centerVal.textContent = 'Rp ' + (popChartData.totalRevenue >= 1000000 ? (popChartData.totalRevenue / 1000000).toFixed(1) + ' Jt' : popChartData.totalRevenue.toLocaleString('id-ID'));
        centerDetail.innerHTML = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 10px;">' + popChartData.totalDestinations + ' Destinasi Aktif</span>';
    }
}

function updateCenterHover(index) {
    if (index === null || index === undefined || !popChartData.labels[index]) {
        updateCenterDefault();
        return;
    }

    const name = popChartData.labels[index];
    const tickets = popChartData.tickets[index] || 0;
    const revenue = popChartData.revenue[index] || 0;
    const color = popChartData.colors[index] || '#10b981';

    const centerIcon = document.getElementById('centerIcon');
    const centerSub = document.getElementById('centerSubtitle');
    const centerVal = document.getElementById('centerValue');
    const centerDetail = document.getElementById('centerDetail');
    const centerBox = document.getElementById('doughnutCenterInfo');

    if (!centerBox) return;

    centerBox.style.transform = 'translate(-50%, -50%) scale(1.06)';
    centerIcon.innerHTML = '<i class="fa-solid fa-circle" style="color: ' + color + '; font-size: 10px;"></i>';

    // Short name if too long
    const shortName = name.length > 20 ? name.substring(0, 18) + '...' : name;
    centerSub.textContent = shortName;

    if (currentPopMetric === 'tickets') {
        const percent = popChartData.totalTickets > 0 ? Math.round((tickets / popChartData.totalTickets) * 100) : 0;
        centerVal.textContent = tickets.toLocaleString('id-ID') + ' Tiket';
        centerDetail.innerHTML = '<span class="badge text-white rounded-pill px-2 py-0.5 fw-bold" style="background: ' + color + '; font-size: 10px;">Porsi ' + percent + '%</span>';
    } else {
        const percent = popChartData.totalRevenue > 0 ? Math.round((revenue / popChartData.totalRevenue) * 100) : 0;
        centerVal.textContent = 'Rp ' + revenue.toLocaleString('id-ID');
        centerDetail.innerHTML = '<span class="badge text-white rounded-pill px-2 py-0.5 fw-bold" style="background: ' + color + '; font-size: 10px;">Porsi ' + percent + '%</span>';
    }
}

function highlightDoughnutSlice(index) {
    if (!popularChart) return;
    popularChart.setActiveElements([{ datasetIndex: 0, index: index }]);
    popularChart.tooltip.setActiveElements([{ datasetIndex: 0, index: index }], { x: 0, y: 0 });
    popularChart.update();
    updateCenterHover(index);

    // Highlight card
    const card = document.getElementById('destCard' + index);
    if (card) {
        card.style.background = '#ffffff';
        card.style.borderColor = popChartData.colors[index];
        card.style.boxShadow = '0 4px 12px rgba(0,0,0,0.06)';
    }
}

function resetDoughnutSlice() {
    if (!popularChart) return;
    popularChart.setActiveElements([]);
    popularChart.tooltip.setActiveElements([], { x: 0, y: 0 });
    popularChart.update();
    updateCenterDefault();

    // Reset cards
    popChartData.labels.forEach((_, i) => {
        const card = document.getElementById('destCard' + i);
        if (card) {
            card.style.background = '#fafbfd';
            card.style.borderColor = '#dee2e6';
            card.style.boxShadow = 'none';
        }
    });
}

function switchPopMetric(metric) {
    currentPopMetric = metric;
    if (!popularChart) return;

    const btnTickets = document.getElementById('btnPopTickets');
    const btnRevenue = document.getElementById('btnPopRevenue');

    if (metric === 'tickets') {
        btnTickets.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 pop-toggle-btn btn-primary text-white shadow-xs';
        btnRevenue.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 pop-toggle-btn text-muted';
        popularChart.data.datasets[0].data = popChartData.tickets;
    } else {
        btnRevenue.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 pop-toggle-btn btn-primary text-white shadow-xs';
        btnTickets.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold fs-8 border-0 pop-toggle-btn text-muted';
        popularChart.data.datasets[0].data = popChartData.revenue;
    }
    popularChart.update();
    updateCenterDefault();
}

document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('popularDestinationsChart');
    if (!canvas || !popChartData.labels.length) return;

    const ctx = canvas.getContext('2d');
    popularChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: popChartData.labels,
            datasets: [{
                data: popChartData.tickets,
                backgroundColor: popChartData.colors,
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 14,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 800,
                easing: 'easeOutQuart'
            },
            onHover: (event, elements) => {
                if (elements.length > 0) {
                    const idx = elements[0].index;
                    updateCenterHover(idx);

                    // Sync right-side cards
                    popChartData.labels.forEach((_, i) => {
                        const card = document.getElementById('destCard' + i);
                        if (card) {
                            if (i === idx) {
                                card.style.background = '#ffffff';
                                card.style.borderColor = popChartData.colors[i];
                                card.style.boxShadow = '0 4px 12px rgba(0,0,0,0.06)';
                            } else {
                                card.style.background = '#fafbfd';
                                card.style.borderColor = '#dee2e6';
                                card.style.boxShadow = 'none';
                            }
                        }
                    });
                } else {
                    updateCenterDefault();
                    popChartData.labels.forEach((_, i) => {
                        const card = document.getElementById('destCard' + i);
                        if (card) {
                            card.style.background = '#fafbfd';
                            card.style.borderColor = '#dee2e6';
                            card.style.boxShadow = 'none';
                        }
                    });
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            const val = context.raw;
                            if (currentPopMetric === 'revenue') {
                                return ' Omset: Rp ' + val.toLocaleString('id-ID');
                            }
                            return ' Tiket: ' + val.toLocaleString('id-ID') + ' Tiket';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    updateCenterDefault();
});
</script>
@endpush
