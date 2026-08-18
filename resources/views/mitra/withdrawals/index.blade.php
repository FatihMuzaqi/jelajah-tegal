@extends('layouts.mitra')

@section('title', 'Dompet & Penarikan Saldo Mitra')
@section('page-title', 'Dompet & Penarikan Saldo')
@section('page-description', 'Kelola saldo pendapatan usaha, pantau mutasi pesanan masuk secara real-time, dan ajukan pencairan dana ke rekening bank Anda.')

@section('page-actions')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('mitra.bank-accounts.index') }}" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 fw-semibold fs-7 d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-building-columns"></i>
            <span>Rekening Bank ({{ count($bankAccounts ?? []) }})</span>
        </a>
        <a href="{{ route('mitra.withdrawals.create') }}" class="btn btn-emerald text-white rounded-pill px-4 py-2 fw-bold fs-7 shadow-sm d-inline-flex align-items-center gap-2"
           style="background: linear-gradient(135deg, #065f46 0%, #047857 100%);">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Ajukan Penarikan</span>
        </a>
    </div>
@endsection

@section('content')
    <style>
        .fintech-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        @media (max-width: 1200px) {
            .fintech-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 576px) {
            .fintech-grid {
                grid-template-columns: 1fr;
            }
        }
        .fintech-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 140px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .fintech-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06);
        }
        .fintech-hero-card {
            background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #059669 100%);
            border: 1px solid #065f46;
            color: #ffffff;
        }
        .fintech-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            line-height: 1;
        }
        .fintech-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .fintech-card-value {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin: 0;
        }
        .fintech-card-footer {
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
        }
        .fintech-hero-card .fintech-card-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.18);
        }
        .bank-banner-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
        }
    </style>

    <!-- 1. Precision Fintech Stat Cards Grid -->
    <div class="fintech-grid">
        <!-- Hero Available Balance Card -->
        <div class="fintech-card fintech-hero-card">
            <div>
                <div class="fintech-card-header">
                    <span class="badge text-white rounded-pill px-2.5 py-1 fw-semibold" style="background: rgba(255, 255, 255, 0.2); font-size: 11px; backdrop-filter: blur(4px);">
                        <i class="fa-solid fa-wallet me-1 text-emerald-200"></i> Saldo Siap Ditarik
                    </span>
                    <div class="fintech-icon-box bg-white text-success shadow-xs">
                        <i class="fa-solid fa-arrow-up-right-from-square" style="color: #047857;"></i>
                    </div>
                </div>
                <h2 class="fintech-card-value text-white">
                    Rp {{ number_format($balance?->available_amount ?? 0, 0, ',', '.') }}
                </h2>
            </div>

            <div class="fintech-card-footer">
                <span class="text-white-50">Bebas biaya transfer bank</span>
                @if(($balance?->available_amount ?? 0) >= 10000)
                    <a href="{{ route('mitra.withdrawals.create') }}" class="btn btn-sm btn-light text-emerald rounded-pill px-3 py-1 fw-bold fs-8 shadow-xs" style="color: #065f46;">
                        Tarik Dana &rarr;
                    </a>
                @endif
            </div>
        </div>

        <!-- Held Balance Card -->
        <div class="fintech-card">
            <div>
                <div class="fintech-card-header">
                    <span class="badge text-warning-emphasis bg-warning-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                        <i class="fa-solid fa-hourglass-half me-1"></i> Saldo Sedang Diproses
                    </span>
                    <div class="fintech-icon-box bg-warning-subtle text-warning-emphasis">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                </div>
                <h3 class="fintech-card-value text-dark">
                    Rp {{ number_format($balance?->held_amount ?? 0, 0, ',', '.') }}
                </h3>
            </div>
            <div class="fintech-card-footer">
                <span class="text-muted">Dalam proses verifikasi / transfer</span>
            </div>
        </div>

        <!-- Total Earned (Net Income) Card -->
        <div class="fintech-card">
            <div>
                <div class="fintech-card-header">
                    <span class="badge text-primary bg-primary-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                        <i class="fa-solid fa-arrow-down-left me-1"></i> Total Saldo Masuk
                    </span>
                    <div class="fintech-icon-box bg-primary-subtle text-primary">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <h3 class="fintech-card-value text-dark">
                    Rp {{ number_format($balance?->total_earned_amount ?? 0, 0, ',', '.') }}
                </h3>
            </div>
            <div class="fintech-card-footer">
                <span class="text-muted">Akumulasi pendapatan bersih usaha</span>
            </div>
        </div>

        <!-- Total Payouts Transferred Card -->
        <div class="fintech-card">
            <div>
                <div class="fintech-card-header">
                    <span class="badge text-success bg-success-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                        <i class="fa-solid fa-circle-check me-1"></i> Total Berhasil Dicairkan
                    </span>
                    <div class="fintech-icon-box bg-success-subtle text-success">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                </div>
                <h3 class="fintech-card-value text-success">
                    Rp {{ number_format($totalWithdrawn ?? 0, 0, ',', '.') }}
                </h3>
            </div>
            <div class="fintech-card-footer">
                <span class="text-muted">Telah ditransfer ke rekening bank</span>
            </div>
        </div>
    </div>

    <!-- 2. Primary Bank Account & Payout Hub Banner -->
    <div class="bank-banner-card">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="fintech-icon-box text-white shadow-xs"
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #0f172a 0%, #334155 100%); font-size: 20px;">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <strong class="text-dark fs-6">Rekening Utama Pencairan:</strong>
                        @if($primaryBankAccount)
                            <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 fs-8">{{ $primaryBankAccount->bank_code }}</span>
                            <span class="font-monospace fw-bold text-dark fs-7">{{ $primaryBankAccount->masked_number }}</span>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fs-8">
                                <i class="fa-solid fa-circle-check me-1"></i> Terverifikasi
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1 fs-8">Belum Ada Rekening Terverifikasi</span>
                        @endif
                    </div>
                    <small class="text-muted d-block mt-0.5">
                        @if($primaryBankAccount)
                            a.n <strong>{{ $primaryBankAccount->decrypted_account_name }}</strong> &middot; Didukung oleh sistem transfer otomatis & Midtrans Iris.
                        @else
                            Daftarkan rekening bank resmi untuk memproses pencairan saldo usaha Anda dengan aman.
                        @endif
                    </small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap w-100 w-md-auto justify-content-start justify-content-md-end">
                <a href="{{ route('mitra.bank-accounts.index') }}" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 fw-semibold fs-7 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-gear"></i>
                    <span>Kelola Rekening Bank</span>
                </a>
                <a href="{{ route('mitra.withdrawals.create') }}" class="btn btn-emerald text-white rounded-pill px-4 py-2 fw-bold fs-7 shadow-sm d-flex align-items-center gap-1.5"
                   style="background: linear-gradient(135deg, #065f46 0%, #047857 100%);">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Tarik Saldo</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. Tab Segmented Control & Data Table Hub -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <!-- Tab Navigation Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4 pb-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-receipt text-emerald" style="color: #047857;"></i>
                        Mutasi Finansial & Riwayat Penarikan
                    </h5>
                    <p class="text-muted fs-8 mb-0">Pantau seluruh catatan transaksi dana keluar dan saldo masuk penjualan secara transparan.</p>
                </div>

                <!-- Tab Buttons -->
                <ul class="nav nav-pills gap-1.5 p-1 bg-light rounded-pill border" id="financeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-3.5 py-1.5 fw-bold fs-7" id="withdrawals-tab" data-bs-toggle="pill"
                                data-bs-target="#tab-withdrawals" type="button" role="tab">
                            <i class="fa-solid fa-money-bill-transfer me-1.5"></i> Riwayat Penarikan ({{ $withdrawals->total() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-3.5 py-1.5 fw-bold fs-7" id="orders-tab" data-bs-toggle="pill"
                                data-bs-target="#tab-orders" type="button" role="tab">
                            <i class="fa-solid fa-arrow-down-left me-1.5"></i> Saldo Masuk Pesanan ({{ count($incomingOrders ?? []) }})
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="financeTabsContent">
                <!-- ========================================== -->
                <!-- TAB 1: RIWAYAT PENARIKAN DANA (WITHDRAWALS) -->
                <!-- ========================================== -->
                <div class="tab-pane fade show active p-3 p-md-4" id="tab-withdrawals" role="tabpanel">
                    <!-- Filter & Search Toolbar -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mb-3">
                        <!-- Quick Status Filters -->
                        <div class="d-flex align-items-center gap-1.5 overflow-x-auto pb-1 pb-md-0">
                            <a href="{{ route('mitra.withdrawals.index') }}"
                               class="btn btn-sm rounded-pill px-3 fw-semibold fs-8 {{ !request('status') ? 'btn-dark' : 'btn-light text-muted border' }}">
                                Semua
                            </a>
                            <a href="{{ route('mitra.withdrawals.index', ['status' => 'submitted']) }}"
                               class="btn btn-sm rounded-pill px-3 fw-semibold fs-8 {{ request('status') === 'submitted' ? 'btn-dark' : 'btn-light text-muted border' }}">
                                Diajukan
                            </a>
                            <a href="{{ route('mitra.withdrawals.index', ['status' => 'under_review']) }}"
                               class="btn btn-sm rounded-pill px-3 fw-semibold fs-8 {{ request('status') === 'under_review' ? 'btn-dark' : 'btn-light text-muted border' }}">
                                Review
                            </a>
                            <a href="{{ route('mitra.withdrawals.index', ['status' => 'processing']) }}"
                               class="btn btn-sm rounded-pill px-3 fw-semibold fs-8 {{ request('status') === 'processing' ? 'btn-dark' : 'btn-light text-muted border' }}">
                                Diproses
                            </a>
                            <a href="{{ route('mitra.withdrawals.index', ['status' => 'paid']) }}"
                               class="btn btn-sm rounded-pill px-3 fw-semibold fs-8 {{ request('status') === 'paid' ? 'btn-dark' : 'btn-light text-muted border' }}">
                                Selesai
                            </a>
                        </div>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('mitra.withdrawals.index') }}" class="d-flex align-items-center gap-2">
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0 fs-8"
                                       placeholder="Cari No. Penarikan...">
                            </div>
                            @if(request('q') || request('status'))
                                <a href="{{ route('mitra.withdrawals.index') }}" class="btn btn-sm btn-light border text-muted" title="Reset Filter">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </a>
                            @endif
                        </form>
                    </div>

                    @if($withdrawals->isEmpty())
                        <div class="py-5 text-center bg-light rounded-4 border my-2">
                            <div class="rounded-circle bg-white shadow-xs text-muted d-inline-flex align-items-center justify-content-center mb-2" style="width: 52px; height: 52px; font-size: 22px;">
                                <i class="fa-solid fa-money-bill-transfer text-emerald" style="color: #047857;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Belum Ada Permintaan Penarikan</h6>
                            <p class="text-muted fs-8 mb-3 mx-auto" style="max-width: 450px;">
                                @if(request('q') || request('status'))
                                    Tidak ditemukan data penarikan dengan filter pencarian saat ini.
                                @else
                                    Anda belum pernah mengajukan penarikan saldo. Klik tombol di bawah untuk mencairkan saldo yang tersedia.
                                @endif
                            </p>
                            @if(($balance?->available_amount ?? 0) >= 10000)
                                <a href="{{ route('mitra.withdrawals.create') }}" class="btn btn-sm btn-emerald text-white rounded-pill px-4 py-2 fw-bold shadow-sm" style="background: #047857;">
                                    <i class="fa-solid fa-plus-circle me-1.5"></i> Ajukan Penarikan Sekarang
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive rounded-3 border">
                            <table class="table table-hover align-middle mb-0 fs-7">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 py-3 text-muted fw-bold fs-8 text-uppercase">No. Penarikan</th>
                                        <th class="py-3 text-muted fw-bold fs-8 text-uppercase">Nominal Penarikan</th>
                                        <th class="py-3 text-muted fw-bold fs-8 text-uppercase">Rekening Bank Tujuan</th>
                                        <th class="py-3 text-muted fw-bold fs-8 text-uppercase">Waktu Pengajuan</th>
                                        <th class="py-3 text-muted fw-bold fs-8 text-uppercase">Status</th>
                                        <th class="pe-3 py-3 text-end text-muted fw-bold fs-8 text-uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($withdrawals as $item)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="font-monospace fw-bold text-dark">{{ $item->withdrawal_number }}</span>
                                                    <button type="button" class="btn btn-sm btn-light border-0 p-0 text-muted"
                                                            onclick="navigator.clipboard.writeText('{{ $item->withdrawal_number }}'); alert('Nomor penarikan disalin!');"
                                                            title="Salin Nomor">
                                                        <i class="fa-regular fa-copy fs-8"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-dark fs-6" style="letter-spacing: -0.3px;">
                                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                </strong>
                                                <small class="text-success d-block fs-8 fw-semibold">Bebas Biaya Transfer</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-dark text-white rounded-pill px-2 py-0.5 fs-8">{{ $item->bank_snapshot['bank_code'] ?? 'Bank' }}</span>
                                                    <div>
                                                        @php
                                                            $rawNum = $item->bank_snapshot['account_number'] ?? ($item->bank_snapshot['last_four'] ?? '****');
                                                            $cleanLast4 = substr(preg_replace('/\D+/', '', (string)$rawNum), -4);
                                                            $rawName = $item->bank_snapshot['account_name'] ?? '-';
                                                            if (str_starts_with($rawName, 'eyJpdiI')) {
                                                                try {
                                                                    $rawName = \Illuminate\Support\Facades\Crypt::decryptString($rawName);
                                                                    if (str_starts_with($rawName, 's:') && @unserialize($rawName) !== false) {
                                                                        $rawName = unserialize($rawName);
                                                                    }
                                                                } catch (\Throwable) {}
                                                            }
                                                        @endphp
                                                        <span class="text-dark font-monospace fw-bold">•••• {{ $cleanLast4 ?: '****' }}</span>
                                                        <small class="text-muted d-block fs-8">a.n {{ $rawName }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-dark d-block fw-medium">{{ $item->created_at?->translatedFormat('d M Y') }}</span>
                                                <small class="text-muted">{{ $item->created_at?->translatedFormat('H:i') }} WIB</small>
                                            </td>
                                            <td>
                                                @if($item->status->value === 'paid')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold fs-8">
                                                        <i class="fa-solid fa-circle-check me-1"></i> Ditransfer (Lunas)
                                                    </span>
                                                @elseif($item->status->value === 'submitted')
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold fs-8">
                                                        <i class="fa-solid fa-paper-plane me-1"></i> Diajukan
                                                    </span>
                                                @elseif($item->status->value === 'under_review')
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold fs-8">
                                                        <i class="fa-solid fa-clock-rotate-left me-1"></i> Ditinjau Tim
                                                    </span>
                                                @elseif($item->status->value === 'processing')
                                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1 fw-bold fs-8">
                                                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Proses Transfer
                                                    </span>
                                                @elseif($item->status->value === 'rejected')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold fs-8">
                                                        <i class="fa-solid fa-circle-xmark me-1"></i> Ditolak
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 fw-bold fs-8">
                                                        {{ strtoupper($item->status->value) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="pe-3 text-end">
                                                <a href="{{ route('mitra.withdrawals.show', $item) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold fs-8">
                                                    Rincian <i class="fa-solid fa-chevron-right ms-1 fs-8"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted fs-8">
                                Menampilkan {{ $withdrawals->firstItem() ?? 0 }} - {{ $withdrawals->lastItem() ?? 0 }} dari total {{ $withdrawals->total() }} penarikan
                            </small>
                            <div>
                                {{ $withdrawals->links() }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ========================================== -->
                <!-- TAB 2: MUTASI SALDO MASUK DARI PESANAN     -->
                <!-- ========================================== -->
                <div class="tab-pane fade p-3 p-md-4" id="tab-orders" role="tabpanel">
                    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-0.5">Riwayat Transaksi Penjualan & Saldo Masuk Bersih</h6>
                            <small class="text-muted fs-8">Saldo bersih masuk otomatis setelah konsumen menyelesaikan pembayaran tiket/layanan.</small>
                        </div>
                        <a href="{{ route('mitra.orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold fs-8">
                            Buka Semua Pesanan &rarr;
                        </a>
                    </div>

                    @if(empty($incomingOrders) || count($incomingOrders) === 0)
                        <div class="py-5 text-center bg-light rounded-4 border my-2">
                            <div class="rounded-circle bg-white shadow-xs text-muted d-inline-flex align-items-center justify-content-center mb-2" style="width: 52px; height: 52px; font-size: 22px;">
                                <i class="fa-solid fa-receipt text-primary"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Belum Ada Transaksi Saldo Masuk</h6>
                            <p class="text-muted fs-8 mb-0 mx-auto" style="max-width: 450px;">
                                Saldo masuk akan otomatis dicatat di sini begitu ada pesanan dari konsumen yang berhasil dibayar.
                            </p>
                        </div>
                    @else
                        <div class="table-responsive rounded-3 border">
                            <table class="table table-hover align-middle mb-0 fs-7">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 py-3 text-muted fw-bold fs-8 text-uppercase">No. Pesanan</th>
                                        <th class="py-3 text-muted fw-bold fs-8 text-uppercase">Layanan / Item</th>
                                        <th class="py-3 text-muted fw-bold fs-8 text-uppercase">Waktu Pembayaran</th>
                                        <th class="py-3 text-muted fw-bold fs-8 text-uppercase">Total Transaksi</th>
                                        <th class="py-3 text-muted fw-bold fs-8 text-uppercase">Komisi Platform</th>
                                        <th class="py-3 text-muted fw-bold fs-8 text-uppercase">Saldo Masuk Bersih (Net)</th>
                                        <th class="pe-3 py-3 text-end text-muted fw-bold fs-8 text-uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomingOrders as $order)
                                        <tr>
                                            <td class="ps-3">
                                                <a href="{{ route('mitra.orders.show', $order) }}" class="fw-bold text-dark font-monospace text-decoration-none">
                                                    {{ $order->order_number }}
                                                </a>
                                                <small class="text-muted d-block fs-8">{{ $order->user_snapshot['name'] ?? ($order->user->name ?? 'Pengunjung') }}</small>
                                            </td>
                                            <td>
                                                <strong class="text-dark d-block fs-7">{{ $order->items->first()?->item_name ?? 'Layanan Mitra' }}</strong>
                                                <small class="text-muted fs-8">{{ $order->items->count() }} item layanan</small>
                                            </td>
                                            <td>
                                                <span class="text-dark d-block fw-medium">{{ $order->paid_at?->translatedFormat('d M Y') ?? '-' }}</span>
                                                <small class="text-muted">{{ $order->paid_at?->translatedFormat('H:i') ?? '' }} WIB</small>
                                            </td>
                                            <td>
                                                <span class="text-dark fw-medium">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted fs-8">-Rp {{ number_format($order->commission_amount, 0, ',', '.') }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success fs-7 fw-bold font-monospace px-2.5 py-1">
                                                    +Rp {{ number_format($order->mitra_net_amount, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="pe-3 text-end">
                                                <a href="{{ route('mitra.orders.show', $order) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold fs-8 text-muted">
                                                    Detail <i class="fa-solid fa-chevron-right ms-1 fs-8"></i>
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
        </div>
    </div>
@endsection
