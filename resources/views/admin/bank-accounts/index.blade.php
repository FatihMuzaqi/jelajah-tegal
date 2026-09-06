@extends('layouts.admin')
@section('title', 'Verifikasi Rekening Bank Mitra')
@section('page-title', 'Verifikasi Rekening Bank Mitra')
@section('page-description', 'Validasi dan kelola keabsahan data rekening bank mitra untuk keperluan pencairan saldo pendapatan & penarikan dana.')

@section('content')
<style>
/* Stat Cards Custom UI */
.stat-card-custom {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 22px 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card-custom:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}
.stat-card-custom .stat-watermark {
    position: absolute;
    right: -25px;
    bottom: -25px;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0, 0, 0, 0.03) 0%, rgba(0, 0, 0, 0.01) 70%, transparent 100%);
    border: 1px solid rgba(0, 0, 0, 0.02);
    pointer-events: none;
}
.stat-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #ffffff;
    flex-shrink: 0;
}
.stat-card-icon.amber {
    background-color: #854d0e;
}
.stat-card-icon.green {
    background-color: #15803d;
}
.stat-card-icon.blue {
    background-color: #1e3a8a;
}
.stat-card-label {
    font-size: 13.5px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 4px;
}
.stat-card-value-wrap {
    display: flex;
    align-items: baseline;
    gap: 6px;
}
.stat-card-value {
    font-size: 28px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
}
.stat-card-unit {
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
}

/* Filter Pills & Search */
.filter-pill {
    display: inline-flex;
    align-items: center;
    padding: 7px 18px;
    border-radius: 9999px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease-in-out;
    white-space: nowrap;
    border: 1px solid transparent;
}
.filter-pill.all-active {
    background-color: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}
.filter-pill.all-inactive {
    background-color: #ffffff;
    color: #475569;
    border-color: #e2e8f0;
}
.filter-pill.all-inactive:hover {
    background-color: #f8fafc;
    color: #0f172a;
}

.filter-pill.pending-active {
    background-color: #78350f;
    color: #ffffff;
    border-color: #78350f;
}
.filter-pill.pending-inactive {
    background-color: #ffffff;
    color: #78350f;
    border-color: #e2e8f0;
}
.filter-pill.pending-inactive:hover {
    background-color: #fefce8;
}

.filter-pill.verified-active {
    background-color: #15803d;
    color: #ffffff;
    border-color: #15803d;
}
.filter-pill.verified-inactive {
    background-color: #ffffff;
    color: #15803d;
    border-color: #e2e8f0;
}
.filter-pill.verified-inactive:hover {
    background-color: #f0fdf4;
}

.filter-pill.rejected-active {
    background-color: #b91c1c;
    color: #ffffff;
    border-color: #b91c1c;
}
.filter-pill.rejected-inactive {
    background-color: #ffffff;
    color: #b91c1c;
    border-color: #e2e8f0;
}
.filter-pill.rejected-inactive:hover {
    background-color: #fef2f2;
}

.search-pill-wrapper {
    position: relative;
    min-width: 250px;
}
.search-pill-wrapper i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
    pointer-events: none;
}
.search-pill-input {
    width: 100%;
    border-radius: 9999px;
    border: 1px solid #e2e8f0;
    background-color: #ffffff;
    padding: 7px 16px 7px 36px;
    font-size: 13px;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s ease-in-out;
}
.search-pill-input:focus {
    border-color: #0f172a;
}
.search-pill-input::placeholder {
    color: #94a3b8;
}

/* Card Container & Empty State */
.bank-accounts-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.bank-accounts-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
}
.bank-accounts-card-header h2 {
    font-size: 15.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.bank-accounts-empty-state {
    padding: 56px 24px;
    text-align: center;
}
.bank-accounts-empty-icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    background-color: #f8fafc;
    border: 1px solid #f1f5f9;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 22px;
    margin-bottom: 16px;
}
.bank-accounts-empty-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
}
.bank-accounts-empty-desc {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 0;
}

/* Dark Mode Overrides */
[data-theme='dark'] .stat-card-custom,
[data-theme='dark'] .bank-accounts-card {
    background: var(--lokantara-surface, #1e293b);
    border-color: var(--lokantara-border, #334155);
}
[data-theme='dark'] .stat-card-label {
    color: var(--lokantara-muted, #94a3b8);
}
[data-theme='dark'] .stat-card-value {
    color: var(--lokantara-text, #f8fafc);
}
[data-theme='dark'] .stat-card-custom .stat-watermark {
    background: radial-gradient(circle, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 70%, transparent 100%);
    border-color: rgba(255, 255, 255, 0.02);
}
[data-theme='dark'] .filter-pill.all-inactive,
[data-theme='dark'] .filter-pill.pending-inactive,
[data-theme='dark'] .filter-pill.verified-inactive,
[data-theme='dark'] .filter-pill.rejected-inactive {
    background-color: var(--lokantara-surface, #1e293b);
    border-color: var(--lokantara-border, #334155);
}
[data-theme='dark'] .search-pill-input {
    background-color: var(--lokantara-surface, #1e293b);
    border-color: var(--lokantara-border, #334155);
    color: var(--lokantara-text, #f8fafc);
}
[data-theme='dark'] .bank-accounts-card-header {
    border-bottom-color: var(--lokantara-border, #334155);
}
[data-theme='dark'] .bank-accounts-card-header h2 {
    color: var(--lokantara-text, #f8fafc);
}
[data-theme='dark'] .bank-accounts-empty-icon {
    background-color: rgba(255, 255, 255, 0.04);
    border-color: var(--lokantara-border, #334155);
    color: #94a3b8;
}
[data-theme='dark'] .bank-accounts-empty-title {
    color: var(--lokantara-text, #f8fafc);
}
</style>

    <!-- 1. Ringkasan Status & KPI Rekening -->
    <div class="row g-3 mb-4">
        <!-- Menunggu Verifikasi -->
        <div class="col-12 col-md-4">
            <div class="stat-card-custom">
                <div class="stat-watermark"></div>
                <div class="stat-card-icon amber">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="stat-card-label">Menunggu Verifikasi</div>
                    <div class="stat-card-value-wrap">
                        <span class="stat-card-value">{{ $counts['pending'] ?? 0 }}</span>
                        <span class="stat-card-unit">Rekening</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekening Terverifikasi -->
        <div class="col-12 col-md-4">
            <div class="stat-card-custom">
                <div class="stat-watermark"></div>
                <div class="stat-card-icon green">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="stat-card-label">Rekening Terverifikasi</div>
                    <div class="stat-card-value-wrap">
                        <span class="stat-card-value">{{ $counts['verified'] ?? 0 }}</span>
                        <span class="stat-card-unit">Rekening</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Rekening Terdaftar -->
        <div class="col-12 col-md-4">
            <div class="stat-card-custom">
                <div class="stat-watermark"></div>
                <div class="stat-card-icon blue">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="stat-card-label">Total Rekening Terdaftar</div>
                    <div class="stat-card-value-wrap">
                        <span class="stat-card-value">{{ $counts['total'] ?? 0 }}</span>
                        <span class="stat-card-unit">Total</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Filter Toolbar & Search -->
    <div class="d-flex flex-column gap-2 mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
            <!-- Status Filter Pills -->
            <div class="d-flex align-items-center gap-2 overflow-x-auto pb-1 pb-md-0">
                <a href="{{ route('admin.bank-accounts.index', array_merge(request()->except('status', 'page'), [])) }}"
                   class="filter-pill {{ !request('status') ? 'all-active' : 'all-inactive' }}">
                    Semua ({{ $counts['total'] ?? 0 }})
                </a>
                <a href="{{ route('admin.bank-accounts.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
                   class="filter-pill {{ request('status') === 'pending' ? 'pending-active' : 'pending-inactive' }}">
                    <i class="fa-regular fa-clock me-1.5" style="{{ request('status') === 'pending' ? 'color: #fff;' : 'color: #d97706;' }}"></i>
                    Menunggu Verifikasi ({{ $counts['pending'] ?? 0 }})
                </a>
                <a href="{{ route('admin.bank-accounts.index', array_merge(request()->except('status', 'page'), ['status' => 'verified'])) }}"
                   class="filter-pill {{ request('status') === 'verified' ? 'verified-active' : 'verified-inactive' }}">
                    <i class="fa-solid fa-circle-check me-1.5" style="{{ request('status') === 'verified' ? 'color: #fff;' : 'color: #16a34a;' }}"></i>
                    Terverifikasi ({{ $counts['verified'] ?? 0 }})
                </a>
                <a href="{{ route('admin.bank-accounts.index', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}"
                   class="filter-pill {{ request('status') === 'rejected' ? 'rejected-active' : 'rejected-inactive' }}">
                    <i class="fa-solid fa-circle-xmark me-1.5" style="{{ request('status') === 'rejected' ? 'color: #fff;' : 'color: #dc2626;' }}"></i>
                    Ditolak ({{ $counts['rejected'] ?? 0 }})
                </a>
            </div>

            <!-- Search Box -->
            <form method="GET" action="{{ route('admin.bank-accounts.index') }}" class="d-flex align-items-center gap-2">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="search-pill-wrapper flex-grow-1">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="search-pill-input" placeholder="Cari mitra, bank, no rekening...">
                </div>
                @if (request('q') || request('status'))
                    <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- 3. Tabel Data Rekening Bank Mitra / Empty State -->
    <div class="bank-accounts-card">
        <div class="bank-accounts-card-header">
            <h2>Daftar Rekening Bank Mitra</h2>
        </div>

        @if ($accounts->isEmpty())
            <div class="bank-accounts-empty-state">
                <div class="bank-accounts-empty-icon">
                    <i class="fa-regular fa-folder-open"></i>
                </div>
                <div class="bank-accounts-empty-title">Tidak ada rekening bank ditemukan</div>
                <p class="bank-accounts-empty-desc">Belum ada data rekening yang sesuai dengan filter atau pencarian Anda.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table lokantara-table mb-0">
                    <thead>
                        <tr>
                            <th style="min-width: 220px;">Mitra &amp; Legalitas</th>
                            <th style="min-width: 200px;">Bank &amp; Nomor Rekening</th>
                            <th style="min-width: 190px;">Atas Nama Pemilik</th>
                            <th style="min-width: 120px;">Status</th>
                            <th style="min-width: 160px;">Didaftarkan &amp; Verifikator</th>
                            <th class="text-end" style="min-width: 220px;">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            @php
                                $bankStyle = match(strtoupper($account->bank_code)) {
                                    'BCA' => 'background-color: #005caa; color: #ffffff;',
                                    'BRI' => 'background-color: #00529c; color: #ffffff;',
                                    'BNI' => 'background-color: #f15a22; color: #ffffff;',
                                    'MANDIRI' => 'background-color: #002d62; color: #ffffff;',
                                    'BSI' => 'background-color: #00a39d; color: #ffffff;',
                                    'JATENG' => 'background-color: #c8102e; color: #ffffff;',
                                    'CIMB' => 'background-color: #7b1113; color: #ffffff;',
                                    'PERMATA' => 'background-color: #4a7729; color: #ffffff;',
                                    default => 'background-color: #1e293b; color: #ffffff;',
                                };
                            @endphp
                            <tr>
                                <!-- 1. Mitra & Legalitas -->
                                <td data-label="Mitra">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <a href="{{ route('admin.mitras.show', $account->mitra) }}" class="text-dark fw-bold fs-7 text-decoration-none hover-primary">
                                            {{ $account->mitra->display_name }}
                                        </a>
                                        @if ($account->mitra->category === 'dinas')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fs-8 fw-bold">
                                                <i class="fa-solid fa-building-columns me-1"></i> Dinas
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 fs-8">
                                                <i class="fa-solid fa-store me-1"></i> {{ $account->mitra->isDinas() ? 'Dinas' : 'Swasta' }}
                                            </span>
                                        @endif
                                    </div>
                                    <small class="text-muted fs-8 d-block">
                                        Legal: {{ $account->mitra->legal_name ?? $account->mitra->display_name }} &middot; <code class="text-muted">/{{ $account->mitra->slug }}</code>
                                    </small>
                                </td>

                                <!-- 2. Bank & Nomor Rekening -->
                                <td data-label="Bank & Rekening">
                                    <div class="d-flex align-items-center gap-1.5 mb-1">
                                        <span class="badge rounded-pill px-2.5 py-1 fs-8 fw-bold shadow-xs" style="{{ $bankStyle }}">
                                            {{ $account->bank_code }}
                                        </span>
                                        @if ($account->is_primary)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5 fs-8 fw-bold" title="Rekening Utama untuk Pencairan Dana">
                                                <i class="fa-solid fa-star me-0.5"></i> Utama
                                            </span>
                                        @endif
                                    </div>
                                    <div class="font-monospace fw-bold text-dark fs-7 d-flex align-items-center gap-1.5">
                                        <span>{{ $account->decrypted_account_number }}</span>
                                    </div>
                                </td>

                                <!-- 3. Atas Nama Pemilik -->
                                <td data-label="Atas Nama">
                                    <div class="fw-semibold text-dark fs-7">
                                        {{ $account->decrypted_account_name }}
                                    </div>
                                    <small class="text-muted fs-8 d-block">
                                        Sesuai Buku Tabungan / Bank
                                    </small>
                                </td>

                                <!-- 4. Status -->
                                <td data-label="Status">
                                    <x-status-badge :status="$account->status" />
                                </td>

                                <!-- 5. Didaftarkan & Verifikator -->
                                <td data-label="Informasi">
                                    <span class="text-dark fs-8 d-block">
                                        <i class="fa-regular fa-calendar text-muted me-1"></i>{{ $account->created_at->format('d M Y, H:i') }}
                                    </span>
                                    @if ($account->verifier && $account->verified_at)
                                        <small class="text-muted fs-8 d-block mt-0.5">
                                            <i class="fa-solid fa-user-check text-success me-1"></i>Oleh: {{ $account->verifier->name }}
                                        </small>
                                    @endif
                                </td>

                                <!-- 6. Aksi Verifikasi -->
                                <td data-label="Aksi" class="text-end">
                                    <div class="d-inline-flex align-items-center justify-content-end gap-1.5 flex-nowrap">
                                        <!-- Tombol Detail Modal -->
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 fs-8 text-secondary d-inline-flex align-items-center gap-1"
                                                data-bs-toggle="modal" data-bs-target="#bankDetailModal-{{ $account->id }}" title="Lihat rincian rekening">
                                            <i class="fa-solid fa-circle-info"></i>
                                            <span>Detail</span>
                                        </button>

                                        @if ($account->status === 'pending')
                                            <!-- Tombol Setujui -->
                                            <form method="POST" action="{{ route('admin.bank-accounts.verification', $account) }}" class="d-inline m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="decision" value="verify">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold fs-8 d-inline-flex align-items-center gap-1 shadow-sm" title="Setujui rekening bank">
                                                    <i class="fa-solid fa-check"></i>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak -->
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-bold fs-8 d-inline-flex align-items-center gap-1"
                                                    data-bs-toggle="modal" data-bs-target="#rejectBankModal-{{ $account->id }}" title="Tolak rekening bank">
                                                <i class="fa-solid fa-xmark"></i>
                                                <span>Tolak</span>
                                            </button>
                                        @elseif ($account->status === 'verified')
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fs-8 d-inline-flex align-items-center gap-1"
                                                    data-bs-toggle="modal" data-bs-target="#rejectBankModal-{{ $account->id }}" title="Ubah status menjadi ditolak">
                                                <i class="fa-solid fa-ban"></i>
                                                <span>Nonaktifkan</span>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('admin.bank-accounts.verification', $account) }}" class="d-inline m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="decision" value="verify">
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fs-8 d-inline-flex align-items-center gap-1" title="Verifikasi ulang rekening ini">
                                                    <i class="fa-solid fa-rotate-left"></i>
                                                    <span>Verifikasi Ulang</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <!-- MODAL DETAIL REKENING BANK -->
                                    <div class="modal fade text-start" id="bankDetailModal-{{ $account->id }}" tabindex="-1" aria-labelledby="bankDetailModalLabel-{{ $account->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                <div class="modal-header bg-dark text-white py-3 px-4">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="fa-solid fa-building-columns text-info fs-5"></i>
                                                        <div>
                                                            <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="bankDetailModalLabel-{{ $account->id }}">
                                                                Rincian Rekening Bank Mitra
                                                            </h5>
                                                            <small class="text-white-50">
                                                                {{ $account->mitra->display_name }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 bg-light">
                                                    <!-- Bank Display Card -->
                                                    <div class="card border rounded-3 p-3 bg-white mb-3 shadow-sm">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="badge rounded-pill px-3 py-1 fs-7 fw-bold" style="{{ $bankStyle }}">
                                                                {{ $account->bank_code }}
                                                            </span>
                                                            <x-status-badge :status="$account->status" />
                                                        </div>
                                                        <div class="font-monospace fw-bold text-dark fs-5 mb-1">
                                                            {{ $account->decrypted_account_number }}
                                                        </div>
                                                        <div class="text-dark fs-7 fw-semibold">
                                                            a.n. {{ $account->decrypted_account_name }}
                                                        </div>
                                                        @if ($account->is_primary)
                                                            <div class="mt-2">
                                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fs-8 fw-bold">
                                                                    <i class="fa-solid fa-star me-1"></i> Rekening Utama Pencairan Saldo
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Data Mitra Info -->
                                                    <div class="card border rounded-3 p-3 bg-white mb-3 shadow-none">
                                                        <h6 class="fw-bold text-dark fs-7 mb-2 border-bottom pb-1.5">
                                                            <i class="fa-solid fa-store text-primary me-1.5"></i> Data Mitra Terdaftar
                                                        </h6>
                                                        <div class="row g-2 fs-8">
                                                            <div class="col-6">
                                                                <span class="text-muted d-block">Nama Usaha:</span>
                                                                <strong class="text-dark">{{ $account->mitra->display_name }}</strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="text-muted d-block">Nama Legal:</span>
                                                                <strong class="text-dark">{{ $account->mitra->legal_name ?? '-' }}</strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="text-muted d-block">Kategori Mitra:</span>
                                                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5">{{ $account->mitra->isDinas() ? 'Dinas' : 'Swasta' }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="text-muted d-block">Waktu Didaftarkan:</span>
                                                                <span class="text-dark">{{ $account->created_at->format('d M Y H:i') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if ($account->verifier && $account->verified_at)
                                                        <div class="p-2.5 rounded-3 bg-success-subtle border border-success-subtle fs-8 text-success-emphasis">
                                                            <i class="fa-solid fa-shield-check me-1"></i> Diverifikasi oleh <strong>{{ $account->verifier->name }}</strong> pada {{ $account->verified_at->format('d M Y H:i') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer bg-white border-top py-2.5 px-4 d-flex justify-content-between">
                                                    <a href="{{ route('admin.mitras.show', $account->mitra) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1.5 fs-8">
                                                        <i class="fa-solid fa-store me-1"></i> Buka Profil Mitra
                                                    </a>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Tutup</button>
                                                        @if ($account->status === 'pending')
                                                            <form method="POST" action="{{ route('admin.bank-accounts.verification', $account) }}" class="d-inline m-0">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="decision" value="verify">
                                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold fs-8">
                                                                    <i class="fa-solid fa-check me-1"></i> Setujui
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MODAL TOLAK REKENING BANK -->
                                    <div class="modal fade text-start" id="rejectBankModal-{{ $account->id }}" tabindex="-1" aria-labelledby="rejectBankModalLabel-{{ $account->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" method="POST" action="{{ route('admin.bank-accounts.verification', $account) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="decision" value="reject">
                                                <div class="modal-header bg-danger text-white py-3 px-4">
                                                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="rejectBankModalLabel-{{ $account->id }}">
                                                        <i class="fa-solid fa-triangle-exclamation me-1.5"></i> Tolak / Nonaktifkan Rekening Bank
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 bg-white">
                                                    <div class="p-3 bg-light rounded-3 border mb-3">
                                                        <div class="fs-8 text-muted">Mitra: <strong>{{ $account->mitra->display_name }}</strong></div>
                                                        <div class="fs-8 text-muted mt-0.5">Bank: <strong>{{ $account->bank_code }} - {{ $account->decrypted_account_number }}</strong></div>
                                                        <div class="fs-8 text-muted mt-0.5">Atas Nama: <strong>{{ $account->decrypted_account_name }}</strong></div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-dark fs-7 mb-1">
                                                            Alasan Penolakan / Penonaktifan <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Nama pemilik rekening tidak sesuai dengan identitas KTP/legalitas penanggung jawab..."></textarea>
                                                        <div class="form-text fs-8 text-muted">Alasan ini akan dikirimkan sebagai notifikasi ke akun mitra.</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-top py-2.5 px-4">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold">
                                                        <i class="fa-solid fa-xmark me-1"></i> Konfirmasi Penolakan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($accounts->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $accounts->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection

