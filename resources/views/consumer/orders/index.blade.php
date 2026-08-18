@extends('layouts.consumer') 
@section('title', 'Pesanan & E-Tiket') 
@section('page-title', 'Pesanan & E-Tiket Saya') 
@section('page-description', 'Kelola paket tour rekomendasi AI dan riwayat pesanan tiket Anda.') 

@section('content')
<style>
    /* Filter Pills */
    .filter-scroll-container {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        margin-bottom: 20px;
        -webkit-overflow-scrolling: touch;
    }
    .filter-scroll-container::-webkit-scrollbar {
        height: 4px;
    }
    .filter-scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .order-filter-btn {
        white-space: nowrap;
        border-radius: 9999px;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 600;
        border: 1.5px solid var(--lokantara-border, #e2e8f0);
        background: #ffffff;
        color: #64748b;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .order-filter-btn:hover {
        border-color: #4338ca;
        color: #4338ca;
    }
    .order-filter-btn.active {
        background: #4338ca;
        border-color: #4338ca;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(67, 56, 202, 0.25);
    }

    /* Modern Order Card for Mobile (< 768px) */
    @media (max-width: 767.98px) {
        .desktop-order-table {
            display: none !important;
        }
        .mobile-order-cards-list {
            display: flex !important;
            flex-direction: column;
            gap: 14px;
            padding: 14px;
        }
        .mobile-order-card {
            background: #ffffff;
            border: 1.5px solid var(--lokantara-border, #e2e8f0);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .mobile-order-card:active {
            transform: scale(0.99);
        }
    }
    @media (min-width: 768px) {
        .mobile-order-cards-list {
            display: none !important;
        }
        .desktop-order-table {
            display: table !important;
        }
    }

    /* Action Buttons in Table */
    .btn-action-view {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: #ffffff;
        border: none;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: transform 0.15s ease;
    }
    .btn-action-view:hover {
        transform: translateY(-1px);
        color: #ffffff;
    }
</style>

<div class="mb-4">
    <!-- QUICK FILTER TABS -->
    <div class="filter-scroll-container">
        <button type="button" class="order-filter-btn active" onclick="filterOrderType('all', this)">
            <i class="fa-solid fa-layer-group me-1"></i> Semua ({{ $invoices->count() + $standaloneOrders->count() }})
        </button>
        @if ($invoices->isNotEmpty())
            <button type="button" class="order-filter-btn" onclick="filterOrderType('ai-package', this)">
                <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Paket AI Tour ({{ $invoices->count() }})
            </button>
        @endif
        @if ($standaloneOrders->isNotEmpty())
            <button type="button" class="order-filter-btn" onclick="filterOrderType('standalone', this)">
                <i class="fa-solid fa-ticket text-emerald me-1"></i> Tiket Mandiri ({{ $standaloneOrders->count() }})
            </button>
        @endif
        <button type="button" class="order-filter-btn" onclick="filterOrderStatus('paid', this)">
            <i class="fa-solid fa-circle-check text-success me-1"></i> Lunas
        </button>
        <button type="button" class="order-filter-btn" onclick="filterOrderStatus('pending', this)">
            <i class="fa-solid fa-clock text-warning me-1"></i> Menunggu Bayar
        </button>
    </div>

    <!-- 3. SECTION 1: PAKET REKOMENDASI TOUR ASSISTANT AI -->
    @if ($invoices->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 order-section-block" data-section="ai-package">
            <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 42px; height: 42px; font-size: 18px; background: #047857; color: #ffffff;">
                        <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bolder text-dark mb-0 fs-6 fs-md-5">Paket Liburan Tour Assistant AI</h5>
                        <p class="text-muted small mb-0" style="font-size: 11px;">Paket perjalanan otomatis all-in-one yang terintegrasi multi-mitra.</p>
                    </div>
                </div>
                <span class="badge bg-primary-subtle text-primary border px-3 py-1.5 rounded-pill fw-bold" style="font-size: 11px;">
                    {{ $invoices->count() }} Paket Liburan
                </span>
            </div>

            <!-- A. Desktop Table View (>= 768px) -->
            <div class="table-responsive desktop-order-table">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-secondary small fw-bold text-uppercase">
                            <th class="ps-4 py-3">Nomor Invoice</th>
                            <th class="py-3">Rincian Paket Terpadu</th>
                            <th class="py-3 text-center">Jumlah Layanan</th>
                            <th class="py-3">Total Biaya</th>
                            <th class="py-3">Status</th>
                            <th class="pe-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr class="order-row-item" data-status="{{ $invoice->status }}">
                                <td class="ps-4 py-3">
                                    <span class="fw-bold font-mono text-primary fs-6">{{ $invoice->invoice_number }}</span>
                                    <div class="text-muted small">{{ $invoice->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark mb-1" style="font-size: 13px;">
                                        <i class="fa-solid fa-suitcase-rolling text-primary me-1"></i> Paket Rekomendasi Tour AI
                                    </div>
                                    <div class="d-flex flex-wrap gap-1" style="max-width: 420px;">
                                        @foreach($invoice->orders as $ord)
                                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 11px;">
                                                {{ $ord->items->first()?->item_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge bg-secondary-subtle text-dark px-2.5 py-1 rounded-pill fw-bold">
                                        {{ $invoice->orders->count() }} Item
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="fw-bolder text-dark fs-6">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @if($invoice->status === 'paid')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold">
                                            <i class="fa-solid fa-circle-check me-1"></i> Lunas / Terbit
                                        </span>
                                    @elseif($invoice->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 rounded-pill fw-bold">
                                            <i class="fa-solid fa-clock me-1"></i> Menunggu Bayar
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-1.5 rounded-pill fw-bold">
                                            {{ strtoupper($invoice->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <a href="{{ route('consumer.invoices.show', $invoice->invoice_number) }}" 
                                       class="btn btn-sm btn-action-view rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1">
                                        <i class="fa-solid fa-qrcode"></i>
                                        <span>Lihat E-Tiket</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- B. Mobile Cards View (< 768px) -->
            <div class="mobile-order-cards-list">
                @foreach ($invoices as $invoice)
                    <div class="mobile-order-card order-row-item" data-status="{{ $invoice->status }}">
                        <!-- Top: Invoice No + Status -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="fw-bold font-mono text-primary" style="font-size: 13px;">{{ $invoice->invoice_number }}</span>
                                <small class="text-muted d-block" style="font-size: 11px;">{{ $invoice->created_at->translatedFormat('d M Y, H:i') }}</small>
                            </div>
                            <div>
                                @if($invoice->status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                                        <i class="fa-solid fa-check me-1"></i> Lunas
                                    </span>
                                @elseif($invoice->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                                        <i class="fa-solid fa-clock me-1"></i> Menunggu
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                                        {{ strtoupper($invoice->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Mid: Items List -->
                        <div class="mb-3 pt-2 border-top">
                            <strong class="d-block text-dark mb-1" style="font-size: 13px;">
                                <i class="fa-solid fa-suitcase-rolling text-primary me-1"></i> Paket Rekomendasi Tour AI
                            </strong>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @foreach($invoice->orders as $ord)
                                    <span class="badge bg-light text-dark border fw-normal" style="font-size: 11px;">
                                        {{ $ord->items->first()?->item_name }}
                                    </span>
                                @endforeach
                            </div>
                            <small class="text-muted" style="font-size: 11px;">
                                <i class="fa-solid fa-layer-group me-1"></i> Total {{ $invoice->orders->count() }} layanan terpadu
                            </small>
                        </div>

                        <!-- Bottom: Total & Full Button -->
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <div>
                                <small class="text-muted d-block" style="font-size: 10px;">Total Pembayaran</small>
                                <strong class="text-dark fs-6">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong>
                            </div>
                            <a href="{{ route('consumer.invoices.show', $invoice->invoice_number) }}" 
                               class="btn btn-sm btn-action-view rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 12px;">
                                <i class="fa-solid fa-qrcode"></i>
                                <span>Lihat E-Tiket</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. SECTION 2: PESANAN TIKET SATUAN (MANDIRI) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden order-section-block" data-section="standalone">
        <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bolder text-dark mb-0 d-flex align-items-center gap-2 fs-6 fs-md-5">
                <i class="fa-solid fa-ticket text-emerald"></i> Pesanan Tiket Satuan (Mandiri)
            </h5>
            <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                {{ $standaloneOrders->count() }} Pesanan
            </span>
        </div>

        @if ($standaloneOrders->isEmpty() && $invoices->isEmpty())
            <div class="card-body p-4 p-md-5 text-center">
                <div class="rounded-circle bg-light text-secondary d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1 fs-6 fs-md-5">Belum Ada Riwayat Pesanan</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width: 420px; font-size: 12px;">
                    Anda belum memiliki tiket atau paket liburan aktif. Rencanakan liburan otomatis Anda bersama Tour Assistant AI sekarang!
                </p>
                <a href="{{ route('tour-assistant.index') }}" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none;">
                    <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Coba Tour Assistant AI Sekarang
                </a>
            </div>
        @elseif($standaloneOrders->isEmpty())
            <div class="card-body p-4 text-center text-muted small" style="font-size: 12px;">
                <i class="fa-solid fa-circle-info me-1 text-primary"></i> Seluruh tiket Anda saat ini terangkum di dalam <strong>Paket Tour AI</strong> di atas.
            </div>
        @else
            <!-- A. Desktop Standalone Table (>= 768px) -->
            <div class="table-responsive desktop-order-table">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-secondary small fw-bold text-uppercase">
                            <th class="ps-4 py-3">Nomor Pesanan</th>
                            <th class="py-3">Item Layanan</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Status</th>
                            <th class="pe-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($standaloneOrders as $order)
                            @php($isPaid = in_array($order->payment_status?->value ?? $order->status?->value, ['paid', 'settlement', 'capture', 'completed']))
                            <tr class="order-row-item" data-status="{{ $isPaid ? 'paid' : 'pending' }}">
                                <td class="ps-4 py-3">
                                    <span class="font-mono fw-bold text-dark">{{ $order->order_number }}</span>
                                    <small class="text-muted d-block" style="font-size: 11px;">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</small>
                                </td>
                                <td class="py-3 fw-medium text-dark">
                                    <div style="font-size: 13px;">{{ $order->items->first()?->item_name }}</div>
                                    <small class="text-muted" style="font-size: 11px;">{{ $order->mitra?->display_name ?? 'Mitra Jelajah Tegal' }}</small>
                                </td>
                                <td class="py-3 fw-bold text-dark">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3">
                                    <x-status-badge :status="$order->status->value" />
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <a href="{{ route('consumer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" style="font-size: 12px;">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- B. Mobile Standalone Cards (< 768px) -->
            <div class="mobile-order-cards-list">
                @foreach ($standaloneOrders as $order)
                    @php($isPaid = in_array($order->payment_status?->value ?? $order->status?->value, ['paid', 'settlement', 'capture', 'completed']))
                    <div class="mobile-order-card order-row-item" data-status="{{ $isPaid ? 'paid' : 'pending' }}">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="fw-bold font-mono text-dark" style="font-size: 13px;">{{ $order->order_number }}</span>
                                <small class="text-muted d-block" style="font-size: 11px;">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</small>
                            </div>
                            <div>
                                <x-status-badge :status="$order->status->value" />
                            </div>
                        </div>

                        <div class="mb-3 pt-2 border-top">
                            <strong class="d-block text-dark mb-0" style="font-size: 13px;">
                                {{ $order->items->first()?->item_name }}
                            </strong>
                            <small class="text-muted" style="font-size: 11px;">{{ $order->mitra?->display_name ?? 'Mitra Jelajah Tegal' }}</small>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <div>
                                <small class="text-muted d-block" style="font-size: 10px;">Total</small>
                                <strong class="text-dark fs-6">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                            </div>
                            <a href="{{ route('consumer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 12px;">
                                Detail &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Client-side filter script -->
<script>
function filterOrderType(type, btn) {
    document.querySelectorAll('.order-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const sections = document.querySelectorAll('.order-section-block');
    sections.forEach(sec => {
        if (type === 'all' || sec.dataset.section === type) {
            sec.style.display = 'block';
        } else {
            sec.style.display = 'none';
        }
    });

    // Reset item status visibility
    document.querySelectorAll('.order-row-item').forEach(item => item.style.display = '');
}

function filterOrderStatus(status, btn) {
    document.querySelectorAll('.order-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Show all sections
    document.querySelectorAll('.order-section-block').forEach(sec => sec.style.display = 'block');

    // Filter individual items
    document.querySelectorAll('.order-row-item').forEach(item => {
        const itemStatus = item.dataset.status;
        if (status === 'all' || itemStatus === status) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endsection
