@extends('layouts.consumer') 
@section('title', 'Pesanan & E-Tiket') 
@section('page-title', 'Pesanan & E-Tiket Saya') 
@section('page-description', 'Kelola paket tour rekomendasi AI dan riwayat pesanan tiket Anda.') 

@section('content')
<div class="mb-4">
    <!-- Section 1: Paket Rekomendasi Tour Assistant AI -->
    @if ($invoices->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-size: 18px; background: #047857 !important;">
                        <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bolder text-dark mb-0">Paket Liburan Tour Assistant AI</h5>
                        <p class="text-muted small mb-0">Paket perjalanan otomatis all-in-one yang terintegrasi multi-mitra.</p>
                    </div>
                </div>
                <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill fw-bold fs-7">
                    {{ $invoices->count() }} Paket Liburan
                </span>
            </div>

            <div class="table-responsive">
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
                            <tr>
                                <td class="ps-4 py-3">
                                    <span class="fw-bold font-mono text-primary fs-6">{{ $invoice->invoice_number }}</span>
                                    <div class="text-muted small">{{ $invoice->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark mb-1">
                                        <i class="fa-solid fa-suitcase-rolling text-primary me-1"></i> Paket Rekomendasi Tour AI
                                    </div>
                                    <div class="d-flex flex-wrap gap-1" style="max-width: 450px;">
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
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold fs-7">
                                            <i class="fa-solid fa-circle-check me-1"></i> Lunas / Terbit
                                        </span>
                                    @elseif($invoice->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 rounded-pill fw-bold fs-7">
                                            <i class="fa-solid fa-clock me-1"></i> Menunggu Bayar
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-1.5 rounded-pill fw-bold fs-7">
                                            {{ strtoupper($invoice->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <a href="{{ route('consumer.invoices.show', $invoice->invoice_number) }}" 
                                       class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-1"
                                       style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); border: none;">
                                        <i class="fa-solid fa-qrcode"></i>
                                        <span>Lihat E-Tiket & Barcode</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Section 2: Pesanan Mandiri / Lainnya -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bolder text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-ticket text-emerald"></i> Pesanan Tiket Satuan (Mandiri)
            </h5>
            <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill fs-7">
                {{ $standaloneOrders->count() }} Pesanan
            </span>
        </div>

        @if ($standaloneOrders->isEmpty() && $invoices->isEmpty())
            <div class="card-body p-5 text-center">
                <div class="rounded-circle bg-light text-secondary d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Belum Ada Riwayat Pesanan</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width: 420px;">
                    Anda belum memiliki tiket atau paket liburan aktif. Rencanakan liburan otomatis Anda bersama Tour Assistant AI sekarang!
                </p>
                <a href="{{ route('tour-assistant.index') }}" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none;">
                    <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Coba Tour Assistant AI Sekarang
                </a>
            </div>
        @elseif($standaloneOrders->isEmpty())
            <div class="card-body p-4 text-center text-muted small">
                <i class="fa-solid fa-circle-info me-1 text-primary"></i> Seluruh tiket Anda saat ini terangkum di dalam <strong>Paket Tour AI</strong> di atas.
            </div>
        @else
            <div class="table-responsive">
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
                            <tr>
                                <td class="ps-4 py-3">
                                    <span class="font-mono fw-bold text-dark">{{ $order->order_number }}</span>
                                </td>
                                <td class="py-3 fw-medium text-dark">
                                    {{ $order->items->first()?->item_name }}
                                </td>
                                <td class="py-3 fw-bold text-dark">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3">
                                    <x-status-badge :status="$order->status->value" />
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <a href="{{ route('consumer.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
