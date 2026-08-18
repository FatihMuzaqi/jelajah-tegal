@extends('layouts.mitra')

@section('title', 'Detail Penarikan ' . $withdrawal->withdrawal_number)
@section('page-title', 'Detail Permohonan Penarikan')
@section('page-description', 'Rincian informasi pencairan saldo, status verifikasi perbankan, dan bukti transfer dana.')

@section('content')
    <div class="row g-4">
        <!-- 1. Detail Voucher Permohonan -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden mb-4">
                <!-- Voucher Header Banner -->
                <div class="p-4 bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <span class="badge bg-secondary-subtle text-dark border px-2.5 py-1 rounded-pill fw-bold fs-8 mb-1.5">
                            Bukti Pengajuan Pencairan
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="fw-extrabold text-dark mb-0 font-monospace" style="letter-spacing: -0.5px;">
                                {{ $withdrawal->withdrawal_number }}
                            </h4>
                            <button type="button" class="btn btn-sm btn-light border-0 p-0 text-muted"
                                    onclick="navigator.clipboard.writeText('{{ $withdrawal->withdrawal_number }}'); alert('Nomor penarikan disalin!');"
                                    title="Salin Nomor">
                                <i class="fa-regular fa-copy fs-7"></i>
                            </button>
                        </div>
                        <small class="text-muted">{{ $withdrawal->created_at?->translatedFormat('d F Y, H:i') }} WIB</small>
                    </div>

                    <div class="text-start text-md-end">
                        <small class="text-muted d-block fs-8 text-uppercase fw-bold">Nominal Penarikan</small>
                        <h3 class="fw-extrabold text-emerald mb-0" style="color: #047857; font-size: 26px; letter-spacing: -0.5px;">
                            Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                        </h3>
                        <small class="text-success fw-semibold fs-8">Bebas Biaya Transfer (Rp 0)</small>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Status Alert Banner -->
                    <div class="mb-4">
                        @if($withdrawal->status->value === 'paid')
                            <div class="alert alert-success border-0 rounded-4 p-3.5 d-flex align-items-center gap-3 mb-0">
                                <div class="rounded-circle bg-success text-white d-grid place-items-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fa-solid fa-check fs-5"></i>
                                </div>
                                <div>
                                    <strong class="d-block fs-7 text-success-emphasis">Pencairan Dana Telah Berhasil Ditransfer</strong>
                                    <small class="text-success-emphasis">Dana telah dikirimkan ke rekening bank tujuan Anda.</small>
                                </div>
                            </div>
                        @elseif($withdrawal->status->value === 'submitted')
                            <div class="alert alert-primary border-0 rounded-4 p-3.5 d-flex align-items-center gap-3 mb-0">
                                <div class="rounded-circle bg-primary text-white d-grid place-items-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fa-solid fa-paper-plane fs-5"></i>
                                </div>
                                <div>
                                    <strong class="d-block fs-7 text-primary-emphasis">Permohonan Telah Diterima</strong>
                                    <small class="text-primary-emphasis">Permohonan Anda sedang masuk dalam antrean review tim finance.</small>
                                </div>
                            </div>
                        @elseif($withdrawal->status->value === 'under_review')
                            <div class="alert alert-warning border-0 rounded-4 p-3.5 d-flex align-items-center gap-3 mb-0">
                                <div class="rounded-circle bg-warning text-dark d-grid place-items-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                                </div>
                                <div>
                                    <strong class="d-block fs-7 text-warning-emphasis">Sedang Ditinjau Tim Finance</strong>
                                    <small class="text-warning-emphasis">Verifikasi rekonsiliasi data saldo dan validasi nomor rekening.</small>
                                </div>
                            </div>
                        @elseif($withdrawal->status->value === 'processing')
                            <div class="alert alert-info border-0 rounded-4 p-3.5 d-flex align-items-center gap-3 mb-0">
                                <div class="rounded-circle bg-info text-white d-grid place-items-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fa-solid fa-spinner fa-spin fs-5"></i>
                                </div>
                                <div>
                                    <strong class="d-block fs-7 text-info-emphasis">Sedang Ditransfer via Iris Payout</strong>
                                    <small class="text-info-emphasis">Instruksi transfer telah dikirimkan ke jaringan perbankan.</small>
                                </div>
                            </div>
                        @elseif($withdrawal->status->value === 'rejected')
                            <div class="alert alert-danger border-0 rounded-4 p-3.5 d-flex align-items-center gap-3 mb-0">
                                <div class="rounded-circle bg-danger text-white d-grid place-items-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                                </div>
                                <div>
                                    <strong class="d-block fs-7 text-danger-emphasis">Permohonan Ditolak</strong>
                                    <small class="text-danger-emphasis">{{ $withdrawal->rejection_reason ?? 'Informasi rekening atau saldo tidak valid.' }}</small>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Detail Data Table -->
                    <div class="card border rounded-3 p-3 bg-light mb-4">
                        <table class="table table-borderless fs-7 mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0 py-2" style="width: 170px;">Rekening Bank Tujuan:</td>
                                    <td class="py-2">
                                        @php
                                            $rawNum = $withdrawal->bank_snapshot['account_number'] ?? ($withdrawal->bank_snapshot['last_four'] ?? '****');
                                            $cleanLast4 = substr(preg_replace('/\D+/', '', (string)$rawNum), -4);
                                            $rawName = $withdrawal->bank_snapshot['account_name'] ?? '-';
                                            if (str_starts_with($rawName, 'eyJpdiI')) {
                                                try {
                                                    $rawName = \Illuminate\Support\Facades\Crypt::decryptString($rawName);
                                                    if (str_starts_with($rawName, 's:') && @unserialize($rawName) !== false) {
                                                        $rawName = unserialize($rawName);
                                                    }
                                                } catch (\Throwable) {}
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 fs-8">{{ $withdrawal->bank_snapshot['bank_code'] ?? 'Bank' }}</span>
                                            <span class="font-monospace fw-bold text-dark fs-7">•••• {{ $cleanLast4 ?: '****' }}</span>
                                        </div>
                                        <div class="text-muted fs-8 mt-0.5">a.n <strong>{{ $rawName }}</strong></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">Catatan Penarikan:</td>
                                    <td class="py-2 text-dark">{{ $withdrawal->notes ?: '-' }}</td>
                                </tr>
                                @if($withdrawal->transfer)
                                    <tr class="border-top">
                                        <td class="text-success fw-bold ps-0 py-2">Referensi Transfer Bank:</td>
                                        <td class="py-2">
                                            <code class="fw-bold fs-7 text-success font-monospace">{{ $withdrawal->transfer->transfer_reference }}</code>
                                            <small class="text-muted d-block fs-8">Waktu Transfer: {{ $withdrawal->transfer->transferred_at?->translatedFormat('d M Y, H:i') }} WIB</small>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                        <a href="{{ route('mitra.withdrawals.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold fs-7">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Riwayat
                        </a>

                        @if(in_array($withdrawal->status->value, ['submitted', 'under_review'], true))
                            <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold fs-7"
                                    data-bs-toggle="collapse" data-bs-target="#cancelWithdrawalForm">
                                <i class="fa-solid fa-ban me-1"></i> Batalkan Penarikan
                            </button>
                        @endif
                    </div>

                    <!-- Cancel Form Collapse -->
                    @if(in_array($withdrawal->status->value, ['submitted', 'under_review'], true))
                        <div class="collapse mt-3" id="cancelWithdrawalForm">
                            <div class="p-3.5 rounded-4 bg-danger-subtle border border-danger-subtle">
                                <h6 class="fw-bold text-danger mb-1.5 fs-7">Konfirmasi Pembatalan Penarikan</h6>
                                <p class="fs-8 text-danger-emphasis mb-3">
                                    Saldo tertahan sebesar <strong>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</strong> akan seketika dikembalikan ke saldo siap ditarik akun Anda.
                                </p>
                                <form method="POST" action="{{ route('mitra.withdrawals.cancel', $withdrawal) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-2.5">
                                        <input class="form-control form-control-sm fs-8 rounded-3" name="reason"
                                               placeholder="Tuliskan alasan pembatalan penarikan ini..." required>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-sm fw-bold rounded-pill px-4">
                                        Ya, Batalkan Permohonan Ini
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Alur Timeline Proses Pencairan -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom p-4 pb-3">
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-timeline text-emerald" style="color: #047857;"></i>
                        Tahapan Pencairan Dana
                    </h5>
                    <p class="text-muted fs-8 mb-0">Estimasi waktu pemrosesan transfer bank: 1x24 jam kerja.</p>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3.5 position-relative">
                        <!-- Step 1 -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-grid place-items-center {{ $withdrawal->created_at ? 'bg-success text-white' : 'bg-light text-muted' }} shadow-xs"
                                 style="width: 36px; height: 36px; flex-shrink: 0;">
                                <i class="fa-solid fa-check fs-7"></i>
                            </div>
                            <div>
                                <strong class="d-block text-dark fs-7">1. Permohonan Diajukan</strong>
                                <small class="text-muted fs-8">Saldo sebesar Rp {{ number_format($withdrawal->amount, 0, ',', '.') }} diamankan & dipindahkan ke saldo tertahan.</small>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-grid place-items-center {{ in_array($withdrawal->status->value, ['under_review', 'approved', 'processing', 'paid']) ? 'bg-success text-white' : 'bg-light text-muted' }} shadow-xs"
                                 style="width: 36px; height: 36px; flex-shrink: 0;">
                                <i class="fa-solid fa-shield-halved fs-7"></i>
                            </div>
                            <div>
                                <strong class="d-block text-dark fs-7">2. Verifikasi & Rekonsiliasi</strong>
                                <small class="text-muted fs-8">Pemeriksaan integritas transaksi penjualan & validitas nomor rekening bank.</small>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-grid place-items-center {{ in_array($withdrawal->status->value, ['processing', 'paid']) ? 'bg-success text-white' : 'bg-light text-muted' }} shadow-xs"
                                 style="width: 36px; height: 36px; flex-shrink: 0;">
                                <i class="fa-solid fa-money-bill-transfer fs-7"></i>
                            </div>
                            <div>
                                <strong class="d-block text-dark fs-7">3. Transfer Dana (Midtrans Iris Payout)</strong>
                                <small class="text-muted fs-8">Pengiriman dana ke rekening perbankan nasional.</small>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-grid place-items-center {{ $withdrawal->status->value === 'paid' ? 'bg-success text-white' : 'bg-light text-muted' }} shadow-xs"
                                 style="width: 36px; height: 36px; flex-shrink: 0;">
                                <i class="fa-solid fa-circle-check fs-7"></i>
                            </div>
                            <div>
                                <strong class="d-block text-dark fs-7">4. Selesai (Dana Diterima)</strong>
                                <small class="text-muted fs-8">Dana telah berhasil masuk ke mutasi rekening bank penerima.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
