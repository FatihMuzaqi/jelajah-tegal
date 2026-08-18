@extends('layouts.mitra')

@section('title', 'Detail Pesanan: ' . $order->order_number)
@section('page-title', 'Detail Pesanan #' . $order->order_number)
@section('page-description', 'Informasi transaksi, data pembeli, rincian item layanan, dan bagi hasil mitra.')

@section('content')
    <!-- Action Bar -->
    <div class='d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded-4'
        style='background: #ffffff; border: 1px solid var(--lokantara-border, #e2e8f0); box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);'>
        <div class='d-flex align-items-center gap-2'>
            <a href="{{ route('mitra.orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <span class='text-muted'>|</span>
            <span class="fw-bold text-dark fs-6">Order #{{ $order->order_number }}</span>
            <x-status-badge :status="$order->status->value" />
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Order Items & Breakdown (7 Cols) -->
        <div class="col-lg-7">
            <x-content-card title="Rincian Item Layanan">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle bg-white mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item / Layanan</th>
                                <th class="text-center" style="width: 80px;">Qty</th>
                                <th class="text-end" style="width: 140px;">Harga Satuan</th>
                                <th class="text-end" style="width: 150px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->items as $item)
                                <tr>
                                    <td>
                                        <strong class="text-dark">{{ $item->item_name }}</strong>
                                        @if($item->scheduled_date)
                                            <small class="text-muted d-block">
                                                <i class="fa-regular fa-calendar text-primary me-1"></i>
                                                Tgl: {{ \Carbon\Carbon::parse($item->scheduled_date)->translatedFormat('d M Y') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada rincian item.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total Pembayaran:</td>
                                <td class="text-end fw-bold text-dark fs-6">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="table-success">
                                <td colspan="3" class="text-end fw-bold text-success">Pendapatan Bersih Mitra (Net):</td>
                                <td class="text-end fw-bold text-success fs-6">Rp {{ number_format($order->mitra_net_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-content-card>
        </div>

        <!-- Right Column: Buyer & Transaction Info (5 Cols) -->
        <div class="col-lg-5">
            <x-content-card title="Informasi Pembeli & Transaksi">
                <div class="p-3 rounded-3 mb-3" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;">
                    <div class="mb-2 pb-2 border-bottom">
                        <span class="text-muted d-block" style="font-size: 11px;">Nama Pembeli / Wisatawan:</span>
                        <strong class="text-dark fs-6">
                            <i class="fa-regular fa-user text-primary me-1"></i>
                            {{ data_get($order->user_snapshot, 'name', 'Pengguna Lokantara') }}
                        </strong>
                    </div>
                    <div class="mb-2 pb-2 border-bottom">
                        <span class="text-muted d-block" style="font-size: 11px;">Email Kontak:</span>
                        <span class="text-dark">
                            <i class="fa-regular fa-envelope text-secondary me-1"></i>
                            {{ data_get($order->user_snapshot, 'email', '-') }}
                        </span>
                    </div>
                    @if(data_get($order->user_snapshot, 'phone'))
                        <div class="mb-2 pb-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 11px;">Nomor Telepon:</span>
                            <span class="text-dark">
                                <i class="fa-solid fa-phone text-success me-1"></i>
                                {{ data_get($order->user_snapshot, 'phone') }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <span class="text-muted d-block" style="font-size: 11px;">Waktu Transaksi:</span>
                        <span class="text-dark">
                            <i class="fa-regular fa-clock text-warning me-1"></i>
                            {{ $order->created_at?->translatedFormat('d F Y, H:i') }} WIB
                        </span>
                    </div>
                </div>

                <div class="alert alert-light border rounded-3 d-flex align-items-center gap-2 mb-0 py-2 px-3" style="font-size: 12px;">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Dana tersimpan di saldo tersedia setelah layanan berhasil diverifikasi.</span>
                </div>
            </x-content-card>
        </div>
    </div>
@endsection
