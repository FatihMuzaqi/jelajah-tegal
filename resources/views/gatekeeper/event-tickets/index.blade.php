@extends('layouts.gatekeeper')

@section('title', 'Scanner QR Tiket - Gatekeeper')
@section('page-title', 'Scanner QR Tiket Pengunjung')
@section('page-description', 'Pindai QR Code atau Barcode pengunjung untuk memvalidasi tiket masuk. Pop-up verifikasi otomatis muncul saat tiket terdeteksi.')

@section('content')
<!-- HTML5 QR Code Scanner Library & SweetAlert2 -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .scanner-hero-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .scanner-viewport-wrapper {
        position: relative;
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
        border-radius: 20px;
        overflow: hidden;
        background: #090d16;
        border: 2px solid #047857;
        box-shadow: 0 8px 25px rgba(4, 120, 87, 0.15);
    }
    #qr-reader {
        width: 100%;
        border: none !important;
    }
    #qr-reader video {
        border-radius: 18px;
        object-fit: cover;
    }
    .scan-laser-line {
        position: absolute;
        top: 20%;
        left: 5%;
        right: 5%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #10b981, #34d399, transparent);
        box-shadow: 0 0 12px #10b981;
        animation: scanAnimation 2.2s infinite ease-in-out;
        z-index: 10;
        pointer-events: none;
    }
    @keyframes scanAnimation {
        0% { top: 15%; opacity: 0.8; }
        50% { top: 85%; opacity: 1; }
        100% { top: 15%; opacity: 0.8; }
    }
    .scan-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: 99px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .pulse-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: currentColor;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.9); opacity: 1; }
        50% { transform: scale(1.4); opacity: 0.5; }
        100% { transform: scale(0.9); opacity: 1; }
    }
</style>

<div class="row justify-content-center g-4">
    <!-- Main Center Column: Full Camera Scanner -->
    <div class="col-lg-9 col-xl-8">
        <div class="scanner-hero-card p-4 p-md-5 text-center">
            
            <!-- Top Status Indicator -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div class="d-flex align-items-center gap-2 text-start">
                    <div class="rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #047857;">
                        <i class="fa-solid fa-qrcode fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Pemindai Kamera Live</h5>
                        <small class="text-muted">Arahkan kamera ke QR Code smartphone pengunjung</small>
                    </div>
                </div>
                <span id="camera-status" class="scan-status-pill bg-secondary text-white">
                    <span class="pulse-dot"></span> Kamera Nonaktif
                </span>
            </div>

            <!-- Viewfinder Area -->
            <div id="scanner-container" class="scanner-viewport-wrapper mb-4" style="display: none;">
                <div class="scan-laser-line"></div>
                <div id="qr-reader"></div>
            </div>

            <!-- Initial Camera Placeholder / Illustration -->
            <div id="camera-idle-placeholder" class="p-5 rounded-4 border mb-4 text-center" style="background: #f8fafc; border-style: dashed !important; border-width: 2px !important;">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 76px; height: 76px; background: #ecfdf5; color: #047857;">
                    <i class="fa-solid fa-camera fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Kamera Belum Aktif</h5>
                <p class="text-muted small mb-0" style="max-width: 380px; margin: 0 auto;">
                    Klik tombol hijau di bawah untuk menyalakan kamera. Pop-up rincian tiket akan langsung muncul saat QR Code terdeteksi.
                </p>
            </div>

            <!-- Controls -->
            <div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
                <button type="button" id="start-scanner-btn" class="btn btn-primary rounded-pill fw-bold px-5 py-3 shadow d-inline-flex align-items-center gap-2 fs-6"
                        style="background: #047857; border: none;" onclick="startScanner()">
                    <i class="fa-solid fa-camera"></i>
                    <span>Nyalakan Kamera Scanner</span>
                </button>
                <button type="button" id="stop-scanner-btn" class="btn btn-outline-danger rounded-pill fw-bold px-4 py-3"
                        style="display: none;" onclick="stopScanner()">
                    <i class="fa-solid fa-stop"></i>
                    <span>Matikan Kamera</span>
                </button>
            </div>

            <!-- Optional Collapsible Manual Input -->
            <div class="pt-3 border-top text-start">
                <button class="btn btn-link text-muted text-decoration-none p-0 small fw-semibold d-inline-flex align-items-center gap-1.5"
                        type="button" data-bs-toggle="collapse" data-bs-target="#manualInputCollapse" aria-expanded="false">
                    <i class="fa-solid fa-keyboard"></i>
                    <span>Kamera bermasalah? Klik di sini untuk input kode manual</span>
                </button>

                <div class="collapse mt-3" id="manualInputCollapse">
                    <div class="p-3 bg-light rounded-3 border">
                        <form id="manual-ticket-form" onsubmit="handleManualSubmit(event)">
                            <label class="form-label fw-bold small">Masukkan Kode Tiket (Contoh: TKT-xxxx):</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-barcode"></i></span>
                                <input type="text" id="manual-token-input" class="form-control font-mono fw-bold" placeholder="TKT-ABC12345 atau tempel token..." required>
                                <button type="submit" class="btn btn-primary fw-bold px-4" style="background: #047857; border: none;">
                                    <i class="fa-solid fa-check me-1"></i> Periksa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Recent Scans Table Section -->
<div class="scanner-hero-card p-4 mt-5">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-secondary fs-5"></i>
            <h5 class="fw-bold mb-0 text-dark">Riwayat 10 Pemindaian Terakhir</h5>
        </div>
        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill small fw-bold">
            Log Aktivitas Validator
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
            <tbody id="recent-logs-tbody">
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

<!-- JavaScript for Camera QR Scanning & SweetAlert2 Pop-up -->
<script>
    let html5QrCode = null;
    let isProcessing = false;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Web Audio Chime Synthesizer
    function playBeep(isSuccess = true) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (isSuccess) {
                // High double chime
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.12); // A5
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.35);
            } else {
                // Low buzz
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, ctx.currentTime); // A3
                osc.frequency.setValueAtTime(164.81, ctx.currentTime + 0.15); // E3
                gain.gain.setValueAtTime(0.35, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.4);
            }
        } catch (e) {
            // Audio context not allowed before interaction
        }
    }

    function startScanner() {
        const container = document.getElementById('scanner-container');
        const idlePlaceholder = document.getElementById('camera-idle-placeholder');
        const startBtn = document.getElementById('start-scanner-btn');
        const stopBtn = document.getElementById('stop-scanner-btn');
        const statusBadge = document.getElementById('camera-status');

        container.style.display = 'block';
        idlePlaceholder.style.display = 'none';
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-flex';
        statusBadge.className = 'scan-status-pill bg-success text-white';
        statusBadge.innerHTML = '<span class="pulse-dot"></span> Kamera Aktif (Scanning...)';
        isProcessing = false;

        html5QrCode = new Html5Qrcode("qr-reader");

        const config = {
            fps: 15,
            qrbox: {
                width: 260,
                height: 260
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
            
            Swal.fire({
                icon: 'warning',
                title: 'Akses Kamera Ditolak',
                text: 'Pastikan browser telah diberikan izin akses kamera, atau gunakan input manual di bawah.',
                confirmButtonColor: '#047857'
            });
            stopScanner();
        });
    }

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        console.log("QR Decoded: ", decodedText);

        // Pause / Stop camera immediately
        stopScanner();

        // Process validation with SweetAlert2 popup
        validateTicketToken(decodedText);
    }

    function onScanFailure(error) {
        // Frame loop
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
            }).catch(err => console.error(err));
        }

        document.getElementById('scanner-container').style.display = 'none';
        document.getElementById('camera-idle-placeholder').style.display = 'block';
        document.getElementById('start-scanner-btn').style.display = 'inline-flex';
        document.getElementById('stop-scanner-btn').style.display = 'none';
        const statusBadge = document.getElementById('camera-status');
        statusBadge.className = 'scan-status-pill bg-secondary text-white';
        statusBadge.innerHTML = '<span class="pulse-dot"></span> Kamera Nonaktif';
    }

    function handleManualSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('manual-token-input');
        const token = input.value.trim();
        if (!token) return;

        validateTicketToken(token);
        input.value = '';
    }

    function validateTicketToken(token) {
        // Show Loading Swal
        Swal.fire({
            title: 'Memvalidasi Tiket...',
            html: '<div class="spinner-border text-success my-2" role="status"></div><p class="text-muted small mt-2">Memeriksa keaslian dan status tiket di server...</p>',
            showConfirmButton: false,
            allowOutsideClick: false,
        });

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

            if (response.ok && data.success) {
                // 🟢 SUKSES
                playBeep(true);

                Swal.fire({
                    icon: 'success',
                    title: '<span style="color:#047857; font-weight:800; font-size:22px;">TIKET TERVERIFIKASI!</span>',
                    html: `
                        <div style="text-align:left; background:#f0fdf4; border:1.5px solid #a7f3d0; border-radius:14px; padding:18px; margin-top:14px; font-size:14px; color:#064e3b; box-shadow: 0 4px 12px rgba(4,120,87,0.08);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; border-bottom:1px solid #d1fae5; padding-bottom:8px;">
                                <span style="font-size:12px; color:#047857; font-weight:bold; text-transform:uppercase;">Hasil Validasi:</span>
                                <span style="background:#047857; color:#ffffff; padding:3px 12px; border-radius:99px; font-weight:bold; font-size:11px;">CHECK-IN SUKSES</span>
                            </div>
                            <div style="margin-bottom:8px;">
                                <span style="color:#065f46; font-size:12px; display:block;">Kode Tiket:</span>
                                <code style="background:#ffffff; padding:3px 8px; border-radius:6px; font-weight:bold; font-size:16px; color:#047857; border:1px solid #a7f3d0;">${data.ticket.code}</code>
                            </div>
                            <div style="margin-bottom:8px;">
                                <span style="color:#065f46; font-size:12px; display:block;">Destinasi / Layanan:</span>
                                <strong style="font-size:15px; color:#0f172a;">${data.ticket.service}</strong>
                            </div>
                            <div style="margin-bottom:8px;">
                                <span style="color:#065f46; font-size:12px; display:block;">Nama Pengunjung:</span>
                                <strong style="font-size:14px; color:#0f172a;">${data.ticket.holder}</strong>
                            </div>
                            <div style="margin-bottom:8px;">
                                <span style="color:#065f46; font-size:12px; display:block;">Waktu Check-In:</span>
                                <span style="font-size:13px; color:#334155;">${data.ticket.scanned_at}</span>
                            </div>
                            <div style="margin-top:12px; padding:8px 12px; background:#ecfdf5; border-radius:8px; font-size:12px; color:#047857; font-weight:600; border:1px dashed #6ee7b7;">
                                <i class="fa-solid fa-lock me-1"></i> Tiket telah berstatus <strong>USED (Digunakan)</strong> dan tidak dapat di-scan lagi.
                            </div>
                        </div>
                    `,
                    confirmButtonText: '<i class="fa-solid fa-camera me-1"></i> Pindai Tiket Selanjutnya',
                    confirmButtonColor: '#047857',
                    allowOutsideClick: false,
                    showCloseButton: true,
                }).then(() => {
                    startScanner();
                });

            } else {
                // 🔴 GAGAL / SUDAH DIGUNAKAN
                playBeep(false);

                Swal.fire({
                    icon: 'error',
                    title: '<span style="color:#dc2626; font-weight:800; font-size:22px;">TIKET DITOLAK!</span>',
                    html: `
                        <div style="text-align:left; background:#fef2f2; border:1.5px solid #fecaca; border-radius:14px; padding:18px; margin-top:14px; font-size:14px; color:#991b1b; box-shadow: 0 4px 12px rgba(220,38,38,0.08);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; border-bottom:1px solid #fee2e2; padding-bottom:8px;">
                                <span style="font-size:12px; color:#dc2626; font-weight:bold; text-transform:uppercase;">Status Tiket:</span>
                                <span style="background:#dc2626; color:#ffffff; padding:3px 12px; border-radius:99px; font-weight:bold; font-size:11px;">INVALID / DITOLAK</span>
                            </div>
                            <div style="font-weight:bold; font-size:14px; margin-bottom:6px; color:#7f1d1d;">Peringatan Sistem:</div>
                            <div style="line-height:1.6; font-size:13.5px; color:#b91c1c; background:#ffffff; padding:12px; border-radius:8px; border:1px solid #fca5a5;">
                                ${data.message || 'Tiket tidak valid atau sudah pernah digunakan sebelumnya.'}
                            </div>
                        </div>
                    `,
                    confirmButtonText: '<i class="fa-solid fa-rotate-right me-1"></i> Coba Pindai Ulang',
                    confirmButtonColor: '#dc2626',
                    allowOutsideClick: false,
                    showCloseButton: true,
                }).then(() => {
                    startScanner();
                });
            }
        })
        .catch(err => {
            console.error(err);
            playBeep(false);

            Swal.fire({
                icon: 'error',
                title: 'Gangguan Jaringan',
                text: 'Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.',
                confirmButtonColor: '#047857',
            }).then(() => {
                startScanner();
            });
        })
        .finally(() => {
            isProcessing = false;
        });
    }
</script>
@endsection
