@extends('layouts.consumer')
@section('title', 'Rincian Pesanan ' . $order->order_number)
@section('page-title', $order->order_number)
@section('page-description', 'Rincian transaksi & status e-ticket Jelajah Tegal.')

@section('content')
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="content-card mb-4">
        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
            <div>
                <h5 class="fw-bold text-dark mb-1">Rincian Pembayaran</h5>
                <span class="text-muted fs-7">Nomor Pesanan: #{{ $order->order_number }}</span>
            </div>
            <x-status-badge :status="$order->status->value" />
        </div>

        <dl class="row mb-0">
            <dt class="col-sm-4 text-muted font-normal">Mitra Penyedia</dt>
            <dd class="col-sm-8 fw-bold text-dark">{{ $order->mitra_snapshot['name'] ?? 'Mitra Jelajah Tegal' }}</dd>

            <dt class="col-sm-4 text-muted font-normal">Subtotal</dt>
            <dd class="col-sm-8 font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</dd>

            <dt class="col-sm-4 text-muted font-normal">Diskon Voucher</dt>
            <dd class="col-sm-8 text-success font-semibold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</dd>

            <dt class="col-sm-4 text-muted font-normal">Biaya Layanan / Admin</dt>
            <dd class="col-sm-8 font-semibold">Rp {{ number_format($order->admin_fee, 0, ',', '.') }}</dd>

            <dt class="col-sm-4 text-muted font-normal fs-6">Total Pembayaran</dt>
            <dd class="col-sm-8 fs-5 fw-extrabold text-success">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</dd>
        </dl>

        @if ($order->status->value === 'pending_payment')
            <div class="mt-4 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-wallet text-success me-1"></i> Konfirmasi Pembayaran / Terbitkan Tiket</h6>
                <p class="text-muted fs-7 mb-3">Pilih opsi pembayaran atau konfirmasi pemesanan langsung untuk menerbitkan QR Code tiket Anda.</p>
                
                <div class="d-flex flex-wrap gap-2">
                    <!-- Direct Confirmation (Tanpa Midtrans / Bayar di Loket) -->
                    <form method="POST" action="{{ route('consumer.orders.confirm-direct', $order) }}">
                        @csrf
                        <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill text-white">
                            <i class="fa-solid fa-qrcode me-1"></i> Konfirmasi & Terbitkan QR Tiket
                        </button>
                    </form>

                    @if (config('midtrans.enabled'))
                        <form method="POST" action="{{ route('consumer.orders.payment.snap', $order) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-dark fw-bold px-4 rounded-pill">
                                <i class="fa-solid fa-credit-card me-1"></i> Bayar via Midtrans Snap
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Section: Item & E-Tickets -->
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-ticket text-danger me-2"></i> Tiket & Layanan Terpesan</h5>

    @foreach ($order->items as $item)
        <div class="content-card mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-extrabold text-dark mb-0">{{ $item->item_name }}</h6>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">{{ str($item->resource_type)->headline() }}</span>
            </div>

            <div class="text-muted fs-7 mb-3">
                <span><i class="fa-solid fa-boxes-stacked me-1"></i> {{ $item->quantity }} Tiket</span> · 
                <span>Harga Satuan: Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                @if($item->booking_date)
                    · <span><i class="fa-solid fa-calendar me-1"></i> Tanggal: {{ \Carbon\Carbon::parse($item->booking_date)->translatedFormat('d F Y') }}</span>
                @endif
            </div>

            @if($item->tickets->isNotEmpty())
                <div class="row g-3 pt-2">
                    @foreach ($item->tickets as $ticket)
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-4 border bg-white shadow-sm text-center">
                                <div class="badge bg-emerald-subtle text-emerald rounded-pill px-3 py-1 mb-2 fw-bold fs-8">
                                    <i class="fa-solid fa-shield-halved me-1"></i> Tiket Resmi
                                </div>
                                <h6 class="fw-extrabold text-dark font-mono mb-1">{{ $ticket->ticket_code }}</h6>
                                <div class="mb-2">
                                    <x-status-badge :status="$ticket->status" />
                                </div>

                                @if (in_array($ticket->status, ['unused', 'active'], true))
                                    <!-- QR Code Display -->
                                    <div class="p-2 bg-light rounded-3 my-2 d-inline-block border">
                                        <img src="{{ route('consumer.tickets.qr', $ticket) }}" alt="QR Code {{ $ticket->ticket_code }}" style="width: 140px; height: 140px;" class="d-block mx-auto">
                                    </div>
                                    <p class="text-muted fs-8 mb-2"><i class="fa-solid fa-scan me-1"></i> Tunjukkan QR Code ini kepada petugas di pintu masuk</p>
                                    <a class="btn btn-sm btn-outline-success rounded-pill w-100 fw-bold" href="{{ route('consumer.tickets.qr', $ticket) }}" target="_blank">
                                        <i class="fa-solid fa-expand me-1"></i> Buka Full QR Code
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-3 rounded-3 text-center bg-light">
                    <p class="text-muted fs-7 mb-0">Tiket QR Code akan terbit secara otomatis begitu pembayaran dikonfirmasi.</p>
                </div>
            @endif
        </div>
    @endforeach
@endsection
