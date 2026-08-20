@extends('layouts.gatekeeper')

@section('title', 'Validasi Tiket QR - Gatekeeper')
@section('page-title', 'Scanner Loket & Validasi Tiket')
@section('page-description', 'Pindai QR Code atau Barcode pengunjung untuk check-in masuk wahana/event. Sistem otomatis mencegah pemindaian ganda (duplicate-scan protection).')

@section('content')
<!-- HTML5 QR Code Scanner Library & SweetAlert2 for Toast Notifications -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<style>
    .scanner-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    .scanner-viewfinder-box {
        width: 100%;
        max-width: 480px;
        margin: 0 auto;
        border-radius: 16px;
        overflow: hidden;
        border: 2px dashed #047857;
        background: #000000;
    }
    .scan-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 13px;
        font-weight: 700;
    }
    .result-badge-verified {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .result-badge-rejected {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
</style>

<div class="row g-4">
    <!-- Left Column: Camera Scanner (Live) -->
    <div class="col-lg-7">
        <div class="scanner-main-card p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-emerald text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #047857;">
                        <i class="fa-solid fa-camera fs-6"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Pemindai Kamera Live</h5>
                        <small class="text-muted">Arahkan kamera ke QR Code e-tiket pengunjung</small>
                    </div>
                </div>
                <span id="camera-status" class="scan-status-pill bg-secondary text-white">
                    <i class="fa-solid fa-video-slash"></i> Kamera Nonaktif
                </span>
            </div>

            <!-- Video Viewfinder -->
            <div id="qr-reader" class="scanner-viewfinder-box mb-3" style="display: none;"></div>

            <div class="d-flex flex-wrap gap-2">
                <button type="button" id="start-scanner-btn" class="btn btn-primary rounded-pill fw-bold px-4 py-2.5 shadow-sm d-inline-flex align-items-center gap-2"
                        style="background: #047857; border: none;" onclick="startScanner()">
                    <i class="fa-solid fa-camera"></i>
                    <span>Aktifkan Kamera Scanner</span>
                </button>
                <button type="button" id="stop-scanner-btn" class="btn btn-outline-danger rounded-pill fw-bold px-4 py-2.5"
                        style="display: none;" onclick="stopScanner()">
                    <i class="fa-solid fa-stop"></i>
                    <span>Matikan Kamera</span>
                </button>
            </div>

            <!-- Live Scan Feedback Alert Box -->
            <div id="scan-feedback" class="alert mt-3 rounded-3" style="display: none;"></div>

            <!-- Interactive Result Card (AJAX Dynamic Result) -->
            <div id="scan-result-card" class="mt-3 p-4 rounded-4 border shadow-sm" style="display: none;">
                <div class="d-flex align-items-start gap-3">
                    <div id="result-icon-box" class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 54px; height: 54px; font-size: 24px;"></div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 id="result-title" class="fw-bold mb-0"></h5>
                            <span id="result-badge" class="badge rounded-pill px-3 py-1 fw-bold fs-7"></span>
                        </div>
                        <p id="result-message" class="mb-3 small"></p>

                        <div id="result-ticket-details" class="p-3 bg-white rounded-3 border mb-3" style="font-size: 13px;">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Kode Tiket:</span>
                                    <strong id="res-ticket-code" class="text-dark font-mono fs-6"></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Layanan / Wahana:</span>
                                    <strong id="res-ticket-service" class="text-dark"></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Nama Pengunjung:</span>
                                    <strong id="res-ticket-holder" class="text-dark"></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Waktu Check-In:</span>
                                    <strong id="res-ticket-time" class="text-dark"></strong>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-4 py-2 fw-bold" onclick="resetAndScanAgain()">
                            <i class="fa-solid fa-rotate-right me-1"></i> Pindai Tiket Selanjutnya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Manual Input & Device Reference -->
    <div class="col-lg-5">
        <div class="scanner-main-card p-4 mb-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="rounded-3 bg-light text-dark d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-keyboard fs-6"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Input Manual / Barcode Gun</h5>
                    <small class="text-muted">Ketik kode tiket atau scan dengan barcode scanner USB</small>
                </div>
            </div>

            <!-- Server Flash Messages -->
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 p-3 mb-3 d-flex align-items-start gap-2" role="alert">
                    <i class="fa-solid fa-circle-check fs-5 text-success mt-0.5"></i>
                    <div>
                        <strong>Berhasil Check-In!</strong>
                        <div class="small mt-0.5">{{ session('status') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->has('token'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 p-3 mb-3 d-flex align-items-start gap-2" role="alert">
                    <i class="fa-solid fa-circle-xmark fs-5 text-danger mt-0.5"></i>
                    <div>
                        <strong>Validasi Gagal!</strong>
                        <div class="small mt-0.5">{{ $errors->first('token') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="validate-ticket-form" method="POST" action="{{ route('gatekeeper.tickets.validate') }}">
                @csrf
                <div class="mb-3">
                    <label for="ticket-token-input" class="form-label fw-bold" style="font-size: 13px;">
                        Token / Kode Tiket (TKT-xxxx) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-barcode"></i></span>
                        <input id="ticket-token-input" class="form-control form-control-lg font-mono fw-bold text-dark" 
                               name="token" value="{{ old('token') }}"
                               placeholder="Contoh: TKT-ABC12345 atau tempel token..." required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted" style="font-size: 12px;">Pos / Loket Gerbang (Opsional)</label>
                    <input class="form-control form-control-sm" name="device_reference"
                        placeholder="Contoh: Pintu Utama Barat / Gerbang 1" value="{{ old('device_reference') }}">
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-2.5 rounded-pill shadow-sm"
                        style="background: #047857; border: none;">
                    <i class="fa-solid fa-check-double me-1"></i> Validasi & Check-In Tiket
                </button>
            </form>

            <hr class="my-4">

            <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 12px;">
                <strong class="text-dark d-flex align-items-center gap-1.5 mb-1.5">
                    <i class="fa-solid fa-shield-halved text-emerald" style="color: #047857;"></i> Perlindungan Anti Double-Scan:
                </strong>
                <ul class="ps-3 mb-0 text-muted" style="line-height: 1.6;">
                    <li>Tiket hanya berlaku untuk <strong>1 kali pemindaian (Single-Use)</strong>.</li>
                    <li>Tiket yang sudah pernah dicheck-in akan otomatis <strong>ditolak merah</strong>.</li>
                    <li>Waktu check-in dan identitas petugas scanner dicatat permanen ke log audit.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- 3. Recent Scans Table Section -->
<div class="scanner-main-card p-4 mt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-secondary fs-5"></i>
            <h5 class="fw-bold mb-0 text-dark">Riwayat Pemindaian Terakhir Anda</h5>
        </div>
        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill small fw-bold">
            10 Aktivitas Terakhir
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
            <thead class="table-light">
                <tr class="text-secondary small fw-bold text-uppercase">
                    <th class="ps-3 py-2.5">Waktu Scan</th>
                    <th>Kode Tiket</th>
                    <th>Layanan / Destinasi</th>
                    <th>Pengunjung</th>
                    <th>Hasil Validasi</th>
                    <th class="pe-3">Status Tiket</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($recentLogs) && $recentLogs->isNotEmpty())
                    @foreach($recentLogs as $log)
                        <tr>
                            <td class="ps-3 text-muted">
                                {{ $log->scanned_at ? $log->scanned_at->translatedFormat('d M Y, H:i:s') : '-' }} WIB
                            </td>
                            <td>
                                <code class="fw-bold text-dark font-mono">{{ $log->ticket?->ticket_code ?? '-' }}</code>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $log->ticket?->orderItem?->item_name ?? 'Layanan Mitra' }}</strong>
                            </td>
                            <td>
                                {{ $log->ticket?->holderUser?->name ?? 'Pengunjung' }}
                            </td>
                            <td>
                                @if($log->result === 'accepted')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold">
                                        <i class="fa-solid fa-circle-check me-1"></i> DITERIMA (Check-In)
                                    </span>
                                @elseif($log->result === 'duplicate')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> DUPLIKAT (Sudah Digunakan)
                                    </span>
                                @elseif($log->result === 'expired')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold">
                                        <i class="fa-solid fa-clock me-1"></i> KEDALUWARSA
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1 fw-bold">
                                        <i class="fa-solid fa-ban me-1"></i> DITOLAK ({{ strtoupper($log->result) }})
                                    </span>
                                @endif
                            </td>
                            <td class="pe-3">
                                @if($log->ticket?->status === 'used')
                                    <span class="badge bg-success text-white rounded-pill px-2 py-0.5 small">Used</span>
                                @else
                                    <span class="badge bg-secondary text-white rounded-pill px-2 py-0.5 small">{{ ucfirst($log->ticket?->status ?? 'unknown') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-clipboard-list fs-3 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada riwayat pemindaian tiket pada sesi ini.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript for Camera QR Scanning & Realtime Validation -->
<script>
    let html5QrCode = null;
    let isProcessing = false;

    function startScanner() {
        const readerElement = document.getElementById('qr-reader');
        const startBtn = document.getElementById('start-scanner-btn');
        const stopBtn = document.getElementById('stop-scanner-btn');
        const statusBadge = document.getElementById('camera-status');
        const feedback = document.getElementById('scan-feedback');
        const resultCard = document.getElementById('scan-result-card');

        readerElement.style.display = 'block';
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-flex';
        statusBadge.className = 'scan-status-pill bg-success text-white';
        statusBadge.innerHTML = '<i class="fa-solid fa-video"></i> Kamera Aktif (Siap Scan)';
        feedback.style.display = 'none';
        resultCard.style.display = 'none';
        isProcessing = false;

        html5QrCode = new Html5Qrcode("qr-reader");

        const config = {
            fps: 15,
            qrbox: {
                width: 250,
                height: 250
            }
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error("Camera start error: ", err);
            statusBadge.className = 'scan-status-pill bg-danger text-white';
            statusBadge.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Gagal Akses Kamera';
            feedback.className = 'alert alert-danger mt-3';
            feedback.innerHTML = 'Gagal mengakses kamera. Pastikan browser diberikan izin akses kamera (Camera Permission) atau gunakan input manual.';
            feedback.style.display = 'block';
            stopScanner();
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        isProcessing = true;

        console.log("QR Decoded: ", decodedText);

        // Pause scanner
        stopScanner();

        // Show validating feedback
        const feedback = document.getElementById('scan-feedback');
        feedback.className = 'alert alert-info mt-3 d-flex align-items-center gap-2';
        feedback.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> <div><strong>QR Code Terdeteksi!</strong> Memvalidasi tiket ke server...</div>';
        feedback.style.display = 'flex';

        // Send AJAX request for instant check without reload
        validateTokenAjax(decodedText);
    }

    function onScanFailure(error) {
        // Silent frame loop
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
            }).catch(err => console.error(err));
        }

        document.getElementById('qr-reader').style.display = 'none';
        document.getElementById('start-scanner-btn').style.display = 'inline-flex';
        document.getElementById('stop-scanner-btn').style.display = 'none';
        const statusBadge = document.getElementById('camera-status');
        statusBadge.className = 'scan-status-pill bg-secondary text-white';
        statusBadge.innerHTML = '<i class="fa-solid fa-video-slash"></i> Kamera Nonaktif';
    }

    function validateTokenAjax(token) {
        const csrfToken = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';
        const feedback = document.getElementById('scan-feedback');
        const resultCard = document.getElementById('scan-result-card');
        const iconBox = document.getElementById('result-icon-box');
        const titleEl = document.getElementById('result-title');
        const badgeEl = document.getElementById('result-badge');
        const msgEl = document.getElementById('result-message');
        const detailsBox = document.getElementById('result-ticket-details');

        fetch('{{ route("gatekeeper.tickets.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ token: token })
        })
        .then(async response => {
            const data = await response.json();
            feedback.style.display = 'none';
            resultCard.style.display = 'block';

            if (response.ok && data.success) {
                // 🟢 SUKSES
                resultCard.style.background = '#f0fdf4';
                resultCard.style.borderColor = '#86efac';
                iconBox.style.background = '#22c55e';
                iconBox.style.color = '#ffffff';
                iconBox.innerHTML = '<i class="fa-solid fa-check"></i>';
                
                titleEl.textContent = 'CHECK-IN BERHASIL (VALID)';
                titleEl.className = 'fw-bold mb-0 text-success';
                badgeEl.className = 'badge bg-success text-white rounded-pill px-3 py-1 fw-bold fs-7';
                badgeEl.textContent = 'TIKET AKTIF / USED';

                msgEl.textContent = data.message;
                msgEl.className = 'mb-3 small text-success';

                detailsBox.style.display = 'block';
                document.getElementById('res-ticket-code').textContent = data.ticket.code;
                document.getElementById('res-ticket-service').textContent = data.ticket.service;
                document.getElementById('res-ticket-holder').textContent = data.ticket.holder;
                document.getElementById('res-ticket-time').textContent = data.ticket.scanned_at;
            } else {
                // 🔴 GAGAL / SUDAH DIGUNAKAN
                resultCard.style.background = '#fef2f2';
                resultCard.style.borderColor = '#fca5a5';
                iconBox.style.background = '#ef4444';
                iconBox.style.color = '#ffffff';
                iconBox.innerHTML = '<i class="fa-solid fa-xmark"></i>';

                titleEl.textContent = 'TIKET DITOLAK / TIDAK DAPAT DIGUNAKAN';
                titleEl.className = 'fw-bold mb-0 text-danger';
                badgeEl.className = 'badge bg-danger text-white rounded-pill px-3 py-1 fw-bold fs-7';
                badgeEl.textContent = 'DITOLAK / INVALID';

                msgEl.textContent = data.message || 'Tiket tidak valid atau sudah pernah digunakan.';
                msgEl.className = 'mb-3 small text-danger fw-semibold';

                detailsBox.style.display = 'none';
            }
        })
        .catch(err => {
            console.error(err);
            feedback.className = 'alert alert-danger mt-3';
            feedback.innerHTML = 'Terjadi kesalahan jaringan atau server saat validasi tiket. Silakan coba lagi.';
            feedback.style.display = 'block';
        })
        .finally(() => {
            isProcessing = false;
        });
    }

    function resetAndScanAgain() {
        document.getElementById('scan-result-card').style.display = 'none';
        document.getElementById('scan-feedback').style.display = 'none';
        startScanner();
    }
</script>
@endsection
