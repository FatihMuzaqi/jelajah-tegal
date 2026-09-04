@extends('layouts.admin')
@section('title', 'Verifikasi Rekening Bank Mitra')
@section('page-title', 'Verifikasi Rekening Bank Mitra')
@section('page-description', 'Validasi dan kelola keabsahan data rekening bank mitra untuk keperluan pencairan saldo pendapatan & penarikan dana.')

@section('content')
    <!-- 1. Ringkasan Status & KPI Rekening -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 20px;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="text-muted fs-8 fw-semibold text-uppercase tracking-wider d-block">Menunggu Verifikasi</span>
                    <h3 class="fw-bold text-dark mb-0 fs-4">{{ $counts['pending'] ?? 0 }} <span class="fs-8 text-muted fw-normal">Rekening</span></h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 20px;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="text-muted fs-8 fw-semibold text-uppercase tracking-wider d-block">Rekening Terverifikasi</span>
                    <h3 class="fw-bold text-dark mb-0 fs-4">{{ $counts['verified'] ?? 0 }} <span class="fs-8 text-muted fw-normal">Rekening</span></h3>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 20px;">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="text-muted fs-8 fw-semibold text-uppercase tracking-wider d-block">Total Rekening Terdaftar</span>
                    <h3 class="fw-bold text-dark mb-0 fs-4">{{ $counts['total'] ?? 0 }} <span class="fs-8 text-muted fw-normal">Total</span></h3>
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
                   class="btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ !request('status') ? 'btn-dark' : 'btn-light border text-muted' }}">
                    Semua ({{ $counts['total'] ?? 0 }})
                </a>
                <a href="{{ route('admin.bank-accounts.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-light border text-warning-emphasis' }}">
                    <i class="fa-solid fa-clock me-1"></i> Menunggu Verifikasi ({{ $counts['pending'] ?? 0 }})
                </a>
                <a href="{{ route('admin.bank-accounts.index', array_merge(request()->except('status', 'page'), ['status' => 'verified'])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('status') === 'verified' ? 'btn-success' : 'btn-light border text-success' }}">
                    <i class="fa-solid fa-circle-check me-1"></i> Terverifikasi ({{ $counts['verified'] ?? 0 }})
                </a>
                <a href="{{ route('admin.bank-accounts.index', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('status') === 'rejected' ? 'btn-danger' : 'btn-light border text-danger' }}">
                    <i class="fa-solid fa-circle-xmark me-1"></i> Ditolak ({{ $counts['rejected'] ?? 0 }})
                </a>
            </div>

            <!-- Search Box -->
            <form method="GET" action="{{ route('admin.bank-accounts.index') }}" class="d-flex align-items-center gap-2">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="form-control bg-light border-start-0 fs-8" placeholder="Cari mitra, bank, no rekening...">
                </div>
                @if (request('q') || request('status'))
                    <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-sm btn-light border text-muted" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- 3. Tabel Data Rekening Bank Mitra -->
    <x-table-wrapper title="Daftar Rekening Bank Mitra">
        @if ($accounts->isEmpty())
            <tbody>
                <tr>
                    <td colspan="6">
                        <x-empty-state 
                            title="Tidak ada rekening bank ditemukan" 
                            description="Belum ada data rekening yang sesuai dengan filter atau pencarian Anda." 
                            compact 
                        />
                    </td>
                </tr>
            </tbody>
        @else
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
        @endif
        <x-slot:pagination>{{ $accounts->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
