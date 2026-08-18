@extends('layouts.mitra')

@section('title', 'Ajukan Penarikan Saldo Mitra')
@section('page-title', 'Formulir Penarikan Saldo')
@section('page-description', 'Pilih rekening bank terverifikasi dan tentukan nominal dana yang ingin dicairkan ke rekening usaha Anda.')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
            <!-- Hero Available Balance Card -->
            <div class="card border-0 rounded-4 p-4 mb-4 shadow-sm position-relative overflow-hidden"
                 style="background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #059669 100%); color: #ffffff;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div>
                        <span class="badge text-white rounded-pill px-2.5 py-1 mb-1.5 fw-semibold" style="background: rgba(255, 255, 255, 0.2); font-size: 11px;">
                            <i class="fa-solid fa-wallet me-1 text-emerald-200"></i> Saldo Siap Ditarik Saat Ini
                        </span>
                        <h2 class="fw-extrabold mb-0 text-white" style="font-size: clamp(24px, 3vw, 28px); letter-spacing: -0.5px;">
                            Rp {{ number_format($balance?->available_amount ?? 0, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="rounded-circle bg-white text-emerald d-grid place-items-center shadow-sm" style="width: 44px; height: 44px; color: #047857;">
                        <i class="fa-solid fa-arrow-up-right-from-square fs-5"></i>
                    </div>
                </div>
                <small class="text-white-50 fs-8 mt-2 d-block">Penarikan dana bebas biaya admin & ditransfer langsung ke rekening bank tujuan.</small>
            </div>

            @if($accounts->isEmpty())
                <div class="card border-0 rounded-4 p-5 text-center bg-white shadow-sm">
                    <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px; font-size: 26px;">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Rekening Bank Terverifikasi</h5>
                    <p class="text-muted fs-7 mb-4 mx-auto" style="max-width: 420px;">
                        Untuk menjaga keamanan dana usaha, penarikan saldo hanya dapat diproses ke rekening bank resmi yang telah terdaftar dan terverifikasi.
                    </p>
                    <div>
                        <a href="{{ route('mitra.bank-accounts.index') }}" class="btn btn-emerald text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: #047857;">
                            <i class="fa-solid fa-plus-circle me-1.5"></i> Daftarkan Rekening Bank Sekarang
                        </a>
                    </div>
                </div>
            @else
                <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4 pb-3">
                        <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-money-bill-transfer text-emerald" style="color: #047857;"></i>
                            Rincian Pengajuan Penarikan
                        </h5>
                        <p class="text-muted fs-8 mb-0">Pastikan nomor rekening dan nominal yang Anda masukkan sudah benar.</p>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('mitra.withdrawals.store') }}" id="withdrawalForm">
                            @csrf
                            <input type="hidden" name="idempotency_key" value="{{ (string) str()->ulid() }}">

                            <!-- 1. Rekening Bank Tujuan -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark fs-7 mb-1.5" for="bank_account_id">
                                    Pilih Rekening Bank Tujuan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg fs-7 rounded-3 border-secondary-subtle" name="bank_account_id" id="bank_account_id" required>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $account->is_primary ? 'selected' : '' }}>
                                            {{ $account->bank_code }} — {{ $account->masked_number }} (a.n {{ $account->decrypted_account_name }})
                                            @if($account->is_primary) — [Rekening Utama] @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="d-flex justify-content-between align-items-center mt-1.5">
                                    <small class="text-muted fs-8">
                                        <i class="fa-solid fa-shield-halved text-success me-1"></i> Rekening terenkripsi AES-256
                                    </small>
                                    <a href="{{ route('mitra.bank-accounts.index') }}" class="fs-8 text-decoration-none fw-semibold">
                                        + Tambah Rekening Lain
                                    </a>
                                </div>
                            </div>

                            <!-- 2. Nominal Penarikan -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1.5">
                                    <label class="form-label fw-bold text-dark fs-7 mb-0" for="amount">
                                        Nominal Penarikan <span class="text-danger">*</span>
                                    </label>
                                    <small class="text-muted fs-8">Min: <strong>Rp 10.000</strong></small>
                                </div>

                                <div class="input-group input-group-lg shadow-xs rounded-3 overflow-hidden border border-secondary-subtle">
                                    <span class="input-group-text bg-light fw-bold text-dark border-0 px-3.5 fs-6">Rp</span>
                                    <input class="form-control border-0 fs-6 fw-bold text-dark" name="amount" id="amount" type="number"
                                           min="10000" max="{{ (int) ($balance?->available_amount ?? 0) }}" step="1"
                                           placeholder="0" value="{{ old('amount') }}" required oninput="updateSummary()">
                                </div>
                                @error('amount')
                                    <small class="text-danger d-block mt-1 fw-medium">{{ $message }}</small>
                                @enderror

                                <!-- Preset Nominal Cepat -->
                                @php
                                    $maxAvail = (int) ($balance?->available_amount ?? 0);
                                @endphp
                                <div class="d-flex align-items-center gap-1.5 flex-wrap mt-2">
                                    <span class="fs-8 text-muted me-1">Pilihan Cepat:</span>
                                    @if($maxAvail >= 100000)
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 fs-8 text-dark fw-semibold" onclick="setAmount(100000)">
                                            Rp 100rb
                                        </button>
                                    @endif
                                    @if($maxAvail >= 500000)
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 fs-8 text-dark fw-semibold" onclick="setAmount(500000)">
                                            Rp 500rb
                                        </button>
                                    @endif
                                    @if($maxAvail >= 1000000)
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 fs-8 text-dark fw-semibold" onclick="setAmount(1000000)">
                                            Rp 1 Juta
                                        </button>
                                    @endif
                                    @if($maxAvail >= 20000)
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 fs-8 text-dark fw-semibold" onclick="setAmount({{ floor($maxAvail / 2) }})">
                                            50% (Rp {{ number_format(floor($maxAvail / 2), 0, ',', '.') }})
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-emerald text-white rounded-pill px-2.5 py-0.5 fs-8 fw-bold" style="background: #047857;" onclick="setAmount({{ $maxAvail }})">
                                        Tarik Semua (100%)
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Ringkasan Perhitungan Finansial -->
                            <div class="card border rounded-3 p-3.5 bg-light mb-4">
                                <h6 class="fw-bold text-dark fs-7 mb-2.5">
                                    <i class="fa-solid fa-calculator text-emerald me-1" style="color: #047857;"></i> Ringkasan Penarikan
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mb-1.5 fs-8">
                                    <span class="text-muted">Saldo Tersedia:</span>
                                    <strong class="text-dark">Rp {{ number_format($maxAvail, 0, ',', '.') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1.5 fs-8">
                                    <span class="text-muted">Nominal Penarikan:</span>
                                    <strong class="text-danger" id="summaryAmount">- Rp 0</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 fs-8">
                                    <span class="text-muted">Biaya Administrasi:</span>
                                    <span class="badge bg-success-subtle text-success fw-bold">Rp 0 (GRATIS)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top fs-7">
                                    <strong class="text-dark">Estimasi Saldo Tersisa:</strong>
                                    <strong class="text-emerald fs-6" id="summaryRemaining" style="color: #047857;">
                                        Rp {{ number_format($maxAvail, 0, ',', '.') }}
                                    </strong>
                                </div>
                            </div>

                            <!-- 4. Catatan Opsional -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark fs-7 mb-1.5" for="notes">
                                    Catatan / Berita Penarikan <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <textarea class="form-control fs-7 rounded-3 border-secondary-subtle" name="notes" id="notes" rows="2"
                                          placeholder="Contoh: Penarikan omset periode awal bulan...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- 5. Tombol Aksi -->
                            <div class="d-flex align-items-center justify-content-between gap-3 pt-3 border-top">
                                <a href="{{ route('mitra.withdrawals.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold fs-7">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-emerald text-white rounded-pill px-4 py-2.5 fw-bold fs-7 shadow-sm d-flex align-items-center gap-2"
                                        style="background: linear-gradient(135deg, #065f46 0%, #047857 100%);"
                                        {{ $maxAvail < 10000 ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>Konfirmasi & Ajukan Penarikan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const maxAvailable = {{ (int) ($balance?->available_amount ?? 0) }};

    function setAmount(val) {
        const input = document.getElementById('amount');
        if (input) {
            input.value = Math.min(val, maxAvailable);
            updateSummary();
        }
    }

    function updateSummary() {
        const input = document.getElementById('amount');
        const val = parseInt(input?.value || 0, 10);
        const summaryAmount = document.getElementById('summaryAmount');
        const summaryRemaining = document.getElementById('summaryRemaining');

        if (summaryAmount) {
            summaryAmount.innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(val);
        }

        if (summaryRemaining) {
            const remaining = Math.max(0, maxAvailable - val);
            summaryRemaining.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(remaining);
        }
    }

    document.addEventListener('DOMContentLoaded', updateSummary);
</script>
@endpush
