@extends('layouts.consumer')
@section('title', 'Rincian Pesanan #' . $order->order_number)
@section('page-title', 'Rincian Pesanan')
@section('page-description', 'Detail transaksi, status pembayaran, dan E-Tiket resmi Jelajah Tegal.')

@section('content')
<style>
/* Modern Order Details Styles */
.order-hero-card {
    border-radius: 20px;
    padding: 24px 28px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    border: 1px solid transparent;
}
.order-hero-pending {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border-color: #fde68a;
    color: #92400e;
}
.order-hero-paid {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-color: #a7f3d0;
    color: #065f46;
}
.order-hero-expired {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-color: #fecaca;
    color: #991b1b;
}

/* Step Progress Tracker */
.order-tracker {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    margin-bottom: 30px;
    padding: 16px 20px;
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 16px;
}
.tracker-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    flex: 1;
    text-align: center;
}
.tracker-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 8px;
    transition: all 0.3s;
}
.tracker-icon-completed {
    background: #10b981;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}
.tracker-icon-active {
    background: #f59e0b;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    animation: pulse 2s infinite;
}
.tracker-icon-pending {
    background: #e2e8f0;
    color: #94a3b8;
}
.tracker-line {
    position: absolute;
    top: 36px;
    left: 12%;
    right: 12%;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
}
.tracker-line-progress {
    height: 100%;
    background: #10b981;
    transition: width 0.4s ease;
}

/* Card styling */
.order-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 18px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
}
.order-card-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--lokantara-text);
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Boarding Pass Ticket Design */
.boarding-pass-card {
    background: #ffffff;
    border: 1.5px dashed #cbd5e1;
    border-radius: 20px;
    padding: 24px;
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
}
.boarding-pass-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.07);
}
.qr-frame {
    background: #ffffff;
    padding: 12px;
    border-radius: 16px;
    border: 2px solid #e2e8f0;
    display: inline-block;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.qr-frame-used {
    border-color: #fca5a5 !important;
    background: #fef2f2 !important;
}
.qr-watermark-badge {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-18deg);
    background: rgba(220, 38, 38, 0.95);
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 8px;
    border: 2px dashed #ffffff;
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
    text-align: center;
    pointer-events: none;
    white-space: nowrap;
    z-index: 5;
}
.qr-watermark-text {
    font-size: 13px;
    font-weight: 900;
    letter-spacing: 1px;
    line-height: 1.2;
    text-transform: uppercase;
}
.qr-watermark-sub {
    font-size: 9.5px;
    font-weight: 700;
    opacity: 0.9;
    margin-top: 2px;
}

/* Copy Button */
.btn-copy {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 13px;
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 6px;
    transition: all 0.2s;
}
.btn-copy:hover {
    background: #f1f5f9;
    color: #0f172a;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.08); }
}

/* Mobile Responsiveness for Order Page */
@media (max-width: 576px) {
    .order-tracker {
        padding: 12px 6px;
    }
    .tracker-icon {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
    .tracker-step span {
        font-size: 10px !important;
    }
    .tracker-step small {
        display: none !important;
    }
    .tracker-line {
        top: 28px;
        left: 10%;
        right: 10%;
    }
    .order-hero-card {
        padding: 16px;
    }
    .order-card {
        padding: 16px;
    }
    .boarding-pass-card {
        padding: 16px;
    }
}
</style>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-circle-check fs-5 me-3 text-success"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('status') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- 1. Hero Status Banner -->
@if ($order->status->value === 'paid')
    <div class="order-hero-card order-hero-paid shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: #10b981; color: #fff; display: grid; place-items: center; font-size: 24px;">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <div>
                    <span class="badge bg-success text-white px-3 py-1 rounded-pill mb-1 fw-bold">
                        <i class="fa-solid fa-check-circle me-1"></i> PEMBAYARAN LUNAS
                    </span>
                    <h4 class="fw-extrabold mb-1" style="color: #065f46;">Tiket Resmi Siap Digunakan!</h4>
                    <p class="mb-0 fs-7" style="color: #047857;">
                        Dibayar pada {{ $order->paid_at ? $order->paid_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Hari ini' }} · Tunjukkan QR Code tiket saat tiba di lokasi.
                    </p>
                </div>
            </div>
            <div>
                <a href="#section-tickets" class="btn btn-success fw-bold px-4 py-2 rounded-pill shadow-sm text-white">
                    <i class="fa-solid fa-qrcode me-1"></i> Lihat E-Tiket &rarr;
                </a>
            </div>
        </div>
    </div>
@elseif ($order->status->value === 'pending_payment')
    <div class="order-hero-card order-hero-pending shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: #f59e0b; color: #fff; display: grid; place-items: center; font-size: 24px;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-1 fw-bold">
                        <i class="fa-solid fa-hourglass-half me-1"></i> MENUNGGU PEMBAYARAN
                    </span>
                    <h4 class="fw-extrabold mb-1" style="color: #92400e;">Selesaikan Pembayaran Anda</h4>
                    <p class="mb-0 fs-7" style="color: #b45309;">
                        Total Tagihan: <strong class="fs-6">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> · Harap selesaikan sebelum {{ $order->expires_at ? $order->expires_at->translatedFormat('d M Y, H:i') . ' WIB' : '15 menit kedepan' }}.
                    </p>
                </div>
            </div>
            <div>
                <form method="POST" action="{{ route('consumer.orders.payment.snap', $order) }}">
                    @csrf
                    <button type="submit" class="btn btn-warning fw-extrabold px-4 py-2 rounded-pill shadow-sm text-dark">
                        <i class="fa-solid fa-credit-card me-1"></i> Bayar Sekarang (Midtrans) &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>
@else
    <div class="order-hero-card order-hero-expired shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 52px; height: 52px; border-radius: 14px; background: #ef4444; color: #fff; display: grid; place-items: center; font-size: 24px;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <h5 class="fw-extrabold mb-1" style="color: #991b1b;">Pesanan {{ str($order->status->value)->headline() }}</h5>
                <p class="mb-0 fs-7" style="color: #b91c1c;">Waktu pembayaran telah berakhir atau transaksi telah dibatalkan.</p>
            </div>
        </div>
    </div>
@endif

<!-- 2. Step Progress Tracker -->
<div class="order-tracker shadow-sm">
    <div class="tracker-line">
        <div class="tracker-line-progress" style="width: {{ $order->status->value === 'paid' ? '100%' : '50%' }};"></div>
    </div>

    <div class="tracker-step">
        <div class="tracker-icon tracker-icon-completed">
            <i class="fa-solid fa-check"></i>
        </div>
        <strong class="fs-8 text-dark">Checkout</strong>
        <small class="text-muted fs-8 d-none d-sm-block">{{ $order->created_at->format('H:i') }} WIB</small>
    </div>

    <div class="tracker-step">
        <div class="tracker-icon {{ $order->status->value === 'paid' ? 'tracker-icon-completed' : 'tracker-icon-active' }}">
            @if($order->status->value === 'paid')
                <i class="fa-solid fa-check"></i>
            @else
                <i class="fa-solid fa-wallet"></i>
            @endif
        </div>
        <strong class="fs-8 text-dark">Pembayaran</strong>
        <small class="text-muted fs-8 d-none d-sm-block">{{ $order->status->value === 'paid' ? 'Lunas' : 'Menunggu' }}</small>
    </div>

    <div class="tracker-step">
        <div class="tracker-icon {{ $order->status->value === 'paid' ? 'tracker-icon-completed' : 'tracker-icon-pending' }}">
            @if($order->status->value === 'paid')
                <i class="fa-solid fa-qrcode"></i>
            @else
                <i class="fa-solid fa-lock"></i>
            @endif
        </div>
        <strong class="fs-8 text-dark">E-Tiket Terbit</strong>
        <small class="text-muted fs-8 d-none d-sm-block">{{ $order->status->value === 'paid' ? 'Aktif' : 'Terkunci' }}</small>
    </div>

    <div class="tracker-step">
        <div class="tracker-icon tracker-icon-pending">
            <i class="fa-solid fa-location-dot"></i>
        </div>
        <strong class="fs-8 text-dark">Kunjungan</strong>
        <small class="text-muted fs-8 d-none d-sm-block">Loket Mitra</small>
    </div>
</div>

<!-- 3. Main 2-Column Content Layout -->
<div class="row g-4">
    <!-- Left Column (8 Cols): Items, Tickets, Guide -->
    <div class="col-lg-8">
        <!-- Item Terpesan Card -->
        <div class="order-card">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <h2 class="order-card-title mb-0">
                    <i class="fa-solid fa-bag-shopping text-emerald"></i> Layanan / Tiket Terpesan
                </h2>
                <span class="badge bg-light text-dark border px-3 py-1 rounded-pill fs-8">
                    {{ $order->items->count() }} Item
                </span>
            </div>

            @foreach ($order->items as $item)
                <div class="p-3 rounded-4 mb-3" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
                        <div>
                            <span class="badge bg-emerald-subtle text-emerald rounded-pill px-3 py-1 mb-1 fw-bold fs-8">
                                {{ str($item->resource_type)->headline() }}
                            </span>
                            <h5 class="fw-bold text-dark mb-1">{{ $item->item_name }}</h5>
                            <div class="text-muted fs-7">
                                <i class="fa-solid fa-store me-1 text-secondary"></i> Pengelola: <strong>{{ $order->mitra_snapshot['name'] ?? 'Mitra Jelajah Tegal' }}</strong>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="text-muted fs-8 d-block">Subtotal</span>
                            <strong class="fs-5 text-emerald">Rp {{ number_format($item->line_total, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <hr class="my-2 opacity-25">

                    <div class="row g-2 text-muted fs-7 pt-1">
                        <div class="col-sm-6">
                            <i class="fa-solid fa-boxes-stacked me-1 text-secondary"></i> Jumlah: <strong class="text-dark">{{ $item->quantity }} Tiket / Unit</strong>
                        </div>
                        <div class="col-sm-6">
                            <i class="fa-solid fa-tag me-1 text-secondary"></i> Harga Satuan: <strong class="text-dark">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</strong>
                        </div>
                        @if($item->booking_date)
                            <div class="col-12 mt-1">
                                <i class="fa-solid fa-calendar-day me-1 text-primary"></i> Tanggal Kunjungan / Reservasi: <strong class="text-dark">{{ \Carbon\Carbon::parse($item->booking_date)->translatedFormat('l, d F Y') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Section: E-Tickets (Hanya Aktif & Muncul jika LUNAS) -->
        <div class="order-card" id="section-tickets">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <h2 class="order-card-title mb-0">
                    <i class="fa-solid fa-qrcode text-emerald"></i> E-Tiket & Kode QR Masuk
                </h2>
                @if ($order->status->value === 'paid')
                    <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold fs-8">
                        <i class="fa-solid fa-circle-check me-1"></i> Terverifikasi Siap Scan
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-1 rounded-pill fw-bold fs-8">
                        <i class="fa-solid fa-lock me-1"></i> QR Terkunci
                    </span>
                @endif
            </div>

            @if ($order->status->value === 'paid')
                @php
                    $allTickets = $order->items->flatMap->tickets;
                @endphp
                @if ($allTickets->isNotEmpty())
                    <div class="row g-4">
                        @foreach ($allTickets as $index => $ticket)
                            @php
                                $isTicketUsed = $ticket->status === 'used';
                            @endphp
                            <div class="col-md-6">
                                <div class="boarding-pass-card text-center {{ $isTicketUsed ? 'border-danger-subtle bg-light-subtle' : '' }}">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge {{ $isTicketUsed ? 'bg-secondary' : 'bg-success' }} text-white px-2.5 py-1 rounded-pill fs-8 fw-bold">
                                            TIKET #{{ $index + 1 }}
                                        </span>
                                        @if($isTicketUsed)
                                            <span class="badge bg-danger text-white border border-danger px-2.5 py-1 rounded-pill fs-8 fw-bold shadow-sm">
                                                <i class="fa-solid fa-circle-check me-1"></i> SUDAH DIGUNAKAN
                                            </span>
                                        @else
                                            <x-status-badge :status="$ticket->status" />
                                        @endif
                                    </div>

                                    <h6 class="fw-extrabold text-dark mb-2">{{ $order->items->first()?->item_name }}</h6>
                                    
                                    <!-- QR Code Image with Watermark Container -->
                                    <div class="position-relative d-inline-block my-2">
                                        <div class="qr-frame {{ $isTicketUsed ? 'qr-frame-used' : '' }}">
                                            <img src="{{ route('consumer.tickets.qr', $ticket) }}" alt="QR Code {{ $ticket->ticket_code }}" style="width: 155px; height: 155px; {{ $isTicketUsed ? 'filter: grayscale(100%) opacity(0.4);' : '' }}" class="d-block mx-auto">
                                        </div>
                                        @if($isTicketUsed)
                                            <div class="qr-watermark-badge">
                                                <div class="qr-watermark-text">
                                                    <i class="fa-solid fa-stamp me-1"></i> SUDAH DIGUNAKAN
                                                </div>
                                                @if($ticket->used_at)
                                                    <div class="qr-watermark-sub">{{ $ticket->used_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Ticket Code with Copy -->
                                    <div class="my-2 p-2 {{ $isTicketUsed ? 'bg-danger-subtle text-danger' : 'bg-light text-dark' }} rounded-3 d-flex align-items-center justify-content-center gap-2">
                                        <code class="fw-bold fs-6 font-mono {{ $isTicketUsed ? 'text-danger' : 'text-dark' }}">{{ $ticket->ticket_code }}</code>
                                        <button type="button" class="btn-copy" onclick="copyToClipboard('{{ $ticket->ticket_code }}', this)" title="Salin Kode Tiket">
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                    </div>

                                    @if($isTicketUsed)
                                        <p class="text-danger fw-semibold fs-8 mb-3">
                                            <i class="fa-solid fa-circle-check me-1"></i> Tiket telah divalidasi & digunakan masuk pada {{ $ticket->used_at ? $ticket->used_at->translatedFormat('d F Y, H:i') : 'hari ini' }} WIB.
                                        </p>
                                    @else
                                        <p class="text-muted fs-8 mb-3">
                                            <i class="fa-solid fa-camera-retro me-1"></i> Tunjukkan QR Code ini langsung di layar HP Anda kepada petugas loket pintu masuk.
                                        </p>
                                    @endif

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('consumer.tickets.qr', $ticket) }}" target="_blank" class="btn btn-sm {{ $isTicketUsed ? 'btn-outline-secondary' : 'btn-outline-success' }} rounded-pill w-100 fw-bold">
                                            <i class="fa-solid fa-expand me-1"></i> Layar Penuh
                                        </a>
                                        <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Cetak E-Tiket">
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
                        <p class="text-muted fs-7 mb-0">Pesanan ini merupakan layanan reservasi langsung ke pihak mitra (tidak memerlukan QR Code tiket fisik).</p>
                    </div>
                @endif
            @else
                <!-- Gating Box Saat Belum Lunas -->
                <div class="p-5 rounded-4 text-center border" style="background: #f8fafc;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #fef3c7; color: #d97706; display: grid; place-items: center; font-size: 28px; margin: 0 auto 16px;">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h5 class="fw-extrabold text-dark mb-2">QR Code E-Tiket Belum Tersedia</h5>
                    <p class="text-muted fs-7 mb-4 mx-auto" style="max-width: 480px;">
                        QR Code tiket resmi yang terenkripsi dan dapat dipindai petugas loket hanya akan digenerate secara otomatis setelah pembayaran Anda terverifikasi <strong>Lunas (Success / Paid)</strong>.
                    </p>
                    <form method="POST" action="{{ route('consumer.orders.payment.snap', $order) }}">
                        @csrf
                        <button type="submit" class="btn btn-success fw-bold px-5 py-2.5 rounded-pill shadow-sm">
                            <i class="fa-solid fa-credit-card me-2"></i> Bayar Sekarang via Midtrans &rarr;
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Panduan Masuk & Lokasi -->
        <div class="order-card">
            <h3 class="order-card-title"><i class="fa-solid fa-circle-question text-primary"></i> Panduan Penggunaan Tiket di Loket</h3>
            <ol class="mb-0 ps-3 text-muted fs-7" style="line-height: 1.8;">
                <li>Pastikan Anda tiba di lokasi sesuai tanggal kunjungan / jadwal reservasi yang tertera.</li>
                <li>Buka halaman rincian pesanan ini di HP Anda dan tunjukkan <strong>QR Code E-Tiket</strong> kepada petugas loket (*Gatekeeper*).</li>
                <li>Petugas akan memindai QR Code untuk memvalidasi tiket masuk Anda.</li>
                <li>Simpan bukti transaksi ini jika sewaktu-waktu dibutuhkan konfirmasi tambahan.</li>
            </ol>
        </div>
    </div>

    <!-- Right Column (4 Cols): Sticky Summary, Payment Action, Customer Info -->
    <div class="col-lg-4">
        <!-- Rincian Pembayaran Card -->
        <div class="order-card" style="position: sticky; top: 90px;">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <h3 class="order-card-title mb-0"><i class="fa-solid fa-receipt text-emerald"></i> Rincian Tagihan</h3>
                <x-status-badge :status="$order->status->value" />
            </div>

            <!-- Order Number Block -->
            <div class="p-2.5 rounded-3 bg-light d-flex align-items-center justify-content-between mb-3">
                <div>
                    <small class="text-muted d-block fs-8">Nomor Pesanan</small>
                    <strong class="font-mono text-dark fs-7">#{{ $order->order_number }}</strong>
                </div>
                <button type="button" class="btn-copy" onclick="copyToClipboard('{{ $order->order_number }}', this)" title="Salin Nomor Pesanan">
                    <i class="fa-regular fa-copy"></i> Salin
                </button>
            </div>

            <dl class="row mb-0 fs-7">
                <dt class="col-6 text-muted font-normal">Waktu Pemesanan</dt>
                <dd class="col-6 text-end text-dark font-medium">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</dd>

                <dt class="col-6 text-muted font-normal">Metode Bayar</dt>
                <dd class="col-6 text-end text-dark font-medium">
                    {{ $order->status->value === 'paid' ? 'Midtrans (Lunas)' : 'Midtrans Gateway' }}
                </dd>

                <dt class="col-6 text-muted font-normal">Subtotal Item</dt>
                <dd class="col-6 text-end text-dark font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</dd>

                @if($order->discount_amount > 0)
                    <dt class="col-6 text-success font-normal">Diskon Voucher</dt>
                    <dd class="col-6 text-end text-success font-semibold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</dd>
                @endif

                <dt class="col-6 text-muted font-normal">Biaya Layanan</dt>
                <dd class="col-6 text-end text-dark font-medium">Rp {{ number_format($order->admin_fee, 0, ',', '.') }}</dd>

                <div class="col-12"><hr class="my-2"></div>

                <dt class="col-6 text-dark font-bold fs-6">Total Pembayaran</dt>
                <dd class="col-6 text-end fs-5 fw-extrabold text-emerald mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</dd>
            </dl>

            <!-- Action Button if Pending -->
            @if ($order->status->value === 'pending_payment')
                <hr class="my-3">
                <form method="POST" action="{{ route('consumer.orders.payment.snap', $order) }}">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 fw-extrabold py-3 rounded-pill shadow-sm mb-2 fs-6">
                        <i class="fa-solid fa-wallet me-2"></i> Bayar Sekarang &rarr;
                    </button>
                </form>
                <div class="text-center text-muted fs-8 mt-2">
                    <i class="fa-solid fa-lock text-success me-1"></i> Transaksi Aman & Terenkripsi oleh Midtrans
                </div>
            @endif

            <!-- Data Pemesan -->
            <hr class="my-3">
            <h4 class="fs-7 fw-bold text-dark mb-2"><i class="fa-solid fa-user text-secondary me-1"></i> Data Pemesan</h4>
            <div class="fs-8 text-muted">
                <div class="mb-1"><strong class="text-dark">{{ $order->user_snapshot['name'] ?? auth()->user()->name }}</strong></div>
                <div class="mb-1"><i class="fa-regular fa-envelope me-1"></i> {{ $order->user_snapshot['email'] ?? auth()->user()->email }}</div>
                @if(!empty($order->user_snapshot['phone']))
                    <div><i class="fa-solid fa-phone me-1"></i> {{ $order->user_snapshot['phone'] }}</div>
                @endif
            </div>

            <!-- Hubungi CS -->
            <hr class="my-3">
            <div class="p-3 rounded-3 bg-light text-center fs-8 text-muted">
                <p class="mb-2">Butuh bantuan terkait pesanan ini?</p>
                <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Jelajah%20Tegal,%20saya%20butuh%20bantuan%20terkait%20pesanan%20%23{{ $order->order_number }}" target="_blank" class="btn btn-sm btn-outline-success w-100 rounded-pill fw-bold">
                    <i class="fa-brands fa-whatsapp me-1"></i> Hubungi CS via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(function() {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-success"></i> Disalin!';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 2000);
    });
}
</script>
@endsection

