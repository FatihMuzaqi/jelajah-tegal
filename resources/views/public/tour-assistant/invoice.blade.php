@extends('layouts.consumer')

@section('title', 'Rincian Paket & E-Tiket #' . $invoice->invoice_number)
@section('page-title', 'Rincian Paket & E-Tiket')
@section('page-description', 'Invoice #' . $invoice->invoice_number . ' · Paket Liburan Terintegrasi Multi-Mitra.')

@section('content')
<div class="mb-4">
    <!-- 1. Header / Hero Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-4 p-md-5 border-bottom" style="background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%);">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge text-white rounded-pill px-3 py-1 fw-bold fs-7" style="background: #047857;">
                            <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Paket Tour Assistant AI
                        </span>
                        <span class="text-muted small">| Terintegrasi Multi-Mitra</span>
                    </div>
                    <h2 class="fw-extrabold text-dark mb-1 font-mono fs-3">{{ $invoice->invoice_number }}</h2>
                    <p class="text-muted small mb-0">Diterbitkan pada {{ $invoice->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>
                <div class="text-start text-md-end">
                    <div class="mb-2">
                        @if($invoice->status === 'paid')
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-6 px-3 py-2 rounded-pill fw-bold">
                                <i class="fa-solid fa-circle-check me-1"></i> LUNAS / TIKET AKTIF
                            </span>
                        @elseif($invoice->status === 'pending')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fs-6 px-3 py-2 rounded-pill fw-bold">
                                <i class="fa-solid fa-clock me-1"></i> MENUNGGU PEMBAYARAN
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold">
                                {{ strtoupper($invoice->status) }}
                            </span>
                        @endif
                    </div>
                    <div class="text-muted small">Total Tagihan Paket:</div>
                    <span class="fs-2 fw-bolder text-primary">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            @if(session('status'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
                    <i class="fa-solid fa-circle-check fs-4 me-3 text-success"></i>
                    <div>
                        <strong>Sukses!</strong> {{ session('status') }}
                    </div>
                </div>
            @endif

            @if($invoice->status === 'pending')
                <!-- Box Pembayaran Saat Status Pending -->
                <div class="p-4 p-md-5 bg-light rounded-4 border text-center mb-5">
                    <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 24px;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Selesaikan Pembayaran Paket</h5>
                    <p class="text-muted small mb-4 mx-auto" style="max-width: 550px;">
                        Setelah pembayaran sebesar <strong>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong> terkonfirmasi, seluruh E-Tiket, Voucher Resto, Reservasi Hotel, dan Rental Mobil akan langsung aktif dan QR Code barcode dapat digunakan di lokasi.
                    </p>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        @if($invoice->payment_url)
                            <a href="{{ $invoice->payment_url }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); border: none;">
                                <i class="fa-solid fa-credit-card me-2"></i> Bayar via Midtrans
                            </a>
                        @endif

                        @if(!config('midtrans.production'))
                            <form action="{{ route('tour-assistant.invoice.confirm-direct', $invoice->invoice_number) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-lg rounded-pill px-4 fw-bold">
                                    <i class="fa-solid fa-bolt me-1"></i> Simulasi Bayar Instan (Dev Mode)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Daftar Layanan, E-Tiket & Barcode QR Code -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bolder text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-ticket text-primary"></i> Rincian E-Tiket & Voucher Paket
                    </h4>
                    <p class="text-muted small mb-0">Klik tombol pada masing-masing item untuk menampilkan barcode / QR Code resmi.</p>
                </div>
                <span class="badge bg-secondary-subtle text-dark border px-3 py-2 rounded-pill fw-bold">
                    {{ $invoice->orders->count() }} Layanan Terdaftar
                </span>
            </div>

            <div class="row g-3 mb-4">
                @foreach($invoice->orders as $orderIndex => $order)
                    @foreach($order->items as $itemIndex => $item)
                        @php
                            $uniqueId = 'ticket_modal_' . $orderIndex . '_' . $itemIndex;
                            $tickets = $item->tickets;
                        @endphp
                        <div class="col-12">
                            <div class="card border rounded-4 shadow-sm overflow-hidden">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 text-white d-flex align-items-center justify-content-center shadow-sm"
                                                 style="width: 52px; height: 52px; font-size: 22px; background: 
                                                 @if($item->resource_type === 'accommodation') linear-gradient(135deg, #3b82f6, #1d4ed8)
                                                 @elseif($item->resource_type === 'rental') linear-gradient(135deg, #0ea5e9, #0284c7)
                                                 @elseif($item->resource_type === 'culinary') linear-gradient(135deg, #f97316, #c2410c)
                                                 @elseif($item->resource_type === 'event') linear-gradient(135deg, #a855f7, #7e22ce)
                                                 @else linear-gradient(135deg, #10b981, #047857) @endif;">
                                                @if($item->resource_type === 'accommodation')
                                                    <i class="fa-solid fa-bed"></i>
                                                @elseif($item->resource_type === 'rental')
                                                    <i class="fa-solid fa-car"></i>
                                                @elseif($item->resource_type === 'culinary')
                                                    <i class="fa-solid fa-utensils"></i>
                                                @elseif($item->resource_type === 'event')
                                                    <i class="fa-solid fa-calendar-star"></i>
                                                @else
                                                    <i class="fa-solid fa-ticket"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge bg-light text-dark border text-capitalize px-2 py-0.5 rounded fw-bold" style="font-size: 11px;">
                                                        {{ $item->resource_type }}
                                                    </span>
                                                    <small class="text-muted font-mono">Order #{{ $order->order_number }}</small>
                                                </div>
                                                <h5 class="fw-bold text-dark mb-1">{{ $item->item_name }}</h5>
                                                <div class="text-muted small">
                                                    <i class="fa-solid fa-store text-secondary me-1"></i> Pengelola: <strong>{{ $order->mitra->display_name ?? 'Mitra Jelajah Tegal' }}</strong>
                                                    &bull; <span class="text-dark fw-bold">{{ $item->quantity }} Unit / Tiket</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-start text-md-end">
                                            <div class="fs-5 fw-bold text-dark mb-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                            @if($invoice->status === 'paid')
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" 
                                                        data-bs-toggle="modal" data-bs-target="#{{ $uniqueId }}"
                                                        style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); border: none;">
                                                    <i class="fa-solid fa-qrcode"></i>
                                                    <span>Lihat Barcode & E-Tiket</span>
                                                </button>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5 rounded-pill">
                                                    <i class="fa-solid fa-lock me-1"></i> QR Terkunci
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL DETAIL BARCODE & QR CODE -->
                        @if($invoice->status === 'paid')
                            <div class="modal fade" id="{{ $uniqueId }}" tabindex="-1" aria-labelledby="{{ $uniqueId }}Label" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content rounded-4 border-0 shadow-lg">
                                        <div class="modal-header bg-dark text-white border-0 py-3 px-4 rounded-top-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-qrcode text-warning fs-5"></i>
                                                <h5 class="modal-title fw-bold text-white mb-0" id="{{ $uniqueId }}Label">E-Tiket & Barcode QR Code Resmi</h5>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body p-4 p-md-5">
                                            <div class="text-center mb-4">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold small mb-2">
                                                    <i class="fa-solid fa-circle-check me-1"></i> TIKET VALID & SIAP SCAN
                                                </span>
                                                <h4 class="fw-extrabold text-dark mb-1">{{ $item->item_name }}</h4>
                                                <p class="text-muted small mb-0">Tunjukkan QR Code berikut di loket / resepsionis mitra saat tiba di lokasi.</p>
                                            </div>

                                            @if($tickets->isNotEmpty())
                                                <div class="row g-4 justify-content-center">
                                                    @foreach($tickets as $tIndex => $ticket)
                                                        <div class="col-md-{{ $tickets->count() > 1 ? '6' : '8' }}">
                                                            <div class="p-4 rounded-4 text-center border-2 border-dashed bg-light">
                                                                <span class="badge bg-primary text-white px-3 py-1 rounded-pill fw-bold text-xs mb-3">
                                                                    TIKET #{{ $tIndex + 1 }}
                                                                </span>

                                                                <!-- QR Code Image -->
                                                                <div class="bg-white p-3 rounded-3 d-inline-block shadow-sm my-2 border">
                                                                    <img src="{{ route('consumer.tickets.qr', $ticket) }}" alt="QR {{ $ticket->ticket_code }}" style="width: 170px; height: 170px;" class="d-block mx-auto">
                                                                </div>

                                                                <!-- Kode Tiket -->
                                                                <div class="my-2 p-2 bg-white rounded-3 border d-flex align-items-center justify-content-center gap-2">
                                                                    <code class="fw-bold fs-6 text-dark font-mono">{{ $ticket->ticket_code }}</code>
                                                                    <button type="button" class="btn btn-sm btn-light border-0" onclick="navigator.clipboard.writeText('{{ $ticket->ticket_code }}'); alert('Kode tiket disalin!');" title="Salin Kode">
                                                                        <i class="fa-regular fa-copy"></i>
                                                                    </button>
                                                                </div>

                                                                <div class="d-flex gap-2 mt-3">
                                                                    <a href="{{ route('consumer.tickets.qr', $ticket) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-bold">
                                                                        <i class="fa-solid fa-expand me-1"></i> Buka Fullscreen
                                                                    </a>
                                                                    <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Cetak">
                                                                        <i class="fa-solid fa-print"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="p-4 rounded-4 text-center bg-light">
                                                    <i class="fa-solid fa-circle-info fs-3 text-secondary mb-2 d-block"></i>
                                                    <p class="text-muted small mb-0">Pesanan ini menggunakan bukti reservasi digital terkonfirmasi atas nama akun Anda.</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="modal-footer bg-light border-0 py-3 px-4 rounded-bottom-4">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>

            <!-- Tombol Navigasi Bawah -->
            <div class="text-center pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('consumer.orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Pesanan Saya
                </a>
                <a href="{{ route('tour-assistant.index') }}" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); border: none;">
                    <i class="fa-solid fa-plus me-1"></i> Buat Paket Rekomendasi Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
