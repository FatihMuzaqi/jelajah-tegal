@extends('layouts.gatekeeper')

@section('title', 'Validasi Tiket QR')
@section('page-title', 'Validasi Tiket Masuk & Event')
@section('page-description', 'Pindai kode QR dari layar smartphone pengunjung menggunakan kamera atau masukkan token
    secara manual.')

@section('content')
    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        .scanner-card {
            background: var(--lokantara-surface);
            border: 1px solid var(--lokantara-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(17, 26, 24, 0.04);
        }

        #qr-reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            border: 2px dashed var(--lokantara-primary);
        }

        .scan-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 600;
        }
    </style>

    <div class="row g-4">
        <!-- Left Column: Camera Scanner (7 Cols) -->
        <div class="col-lg-7">
            <div class="scanner-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fs-5 fw-bold mb-0">📷 Pemindai Kamera (Live Scanner)</h3>
                    <span id="camera-status" class="scan-status-badge bg-secondary text-white">
                        ⚪ Kamera Nonaktif
                    </span>
                </div>

                <p class="text-muted mb-3" style="font-size: 13px;">
                    Arahkan kamera ke QR Code yang ditunjukkan oleh pengunjung pada smartphone atau tiket cetak mereka.
                </p>

                <!-- Video Viewfinder -->
                <div id="qr-reader" class="mb-3" style="display: none;"></div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="button" id="start-scanner-btn" class="btn btn-lokantara fw-bold px-4"
                        onclick="startScanner()">
                        📷 Aktifkan Kamera Scanner
                    </button>
                    <button type="button" id="stop-scanner-btn" class="btn btn-outline-danger fw-bold px-4"
                        style="display: none;" onclick="stopScanner()">
                        ⏹️ Matikan Kamera
                    </button>
                </div>

                <!-- Scan Feedback Alert Box -->
                <div id="scan-feedback" class="alert mt-3" style="display: none;"></div>
            </div>
        </div>

        <!-- Right Column: Manual Token Input & Device Info (5 Cols) -->
        <div class="col-lg-5">
            <div class="scanner-card">
                <h3 class="fs-5 fw-bold mb-3">⌨️ Input Token Manual / Barcode Scanner</h3>
                <p class="text-muted mb-3" style="font-size: 13px;">
                    Gunakan formulir ini jika menggunakan barcode scanner USB atau jika QR Code sulit terbaca.
                </p>

                <form id="validate-ticket-form" method="POST" action="{{ route('gatekeeper.tickets.validate') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 13px;">Token / Kode QR Tiket</label>
                        <input id="ticket-token-input" class="form-control" name="token"
                            placeholder="Tempel atau ketik token tiket di sini..." required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size: 12px;">Referensi Perangkat / Pos Loket
                            (Opsional)</label>
                        <input class="form-control form-control-sm" name="device_reference"
                            placeholder="Cth: Pintu Utama Barat / Gerbang 1">
                    </div>

                    <button type="submit" class="btn btn-lokantara w-100 fw-bold py-2">
                        ✔ Validasi & Check-In Tiket
                    </button>
                </form>

                <hr class="my-4">

                <div class="p-3 rounded"
                    style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 12px;">
                    <strong>ℹ️ Ketentuan Validasi Tiket:</strong>
                    <ul class="ps-3 mb-0 mt-1 text-muted">
                        <li>Tiket hanya dapat divalidasi <strong>satu kali (1x Check-in)</strong>.</li>
                        <li>Sistem otomatis menolak tiket kedaluwarsa atau tiket milik Mitra lain.</li>
                        <li>Waktu pemindaian dan identitas petugas loket akan dicatat dalam log audit.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Camera QR Scanning -->
    <script>
        let html5QrCode = null;

        function startScanner() {
            const readerElement = document.getElementById('qr-reader');
            const startBtn = document.getElementById('start-scanner-btn');
            const stopBtn = document.getElementById('stop-scanner-btn');
            const statusBadge = document.getElementById('camera-status');
            const feedback = document.getElementById('scan-feedback');

            readerElement.style.display = 'block';
            startBtn.style.display = 'none';
            stopBtn.style.display = 'inline-block';
            statusBadge.className = 'scan-status-badge bg-success text-white';
            statusBadge.innerHTML = '🟢 Kamera Aktif (Siap Scan)';
            feedback.style.display = 'none';

            html5QrCode = new Html5Qrcode("qr-reader");

            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            };

            html5QrCode.start({
                    facingMode: "environment"
                },
                config,
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Camera start error: ", err);
                statusBadge.className = 'scan-status-badge bg-danger text-white';
                statusBadge.innerHTML = '🔴 Gagal Mengakses Kamera';
                feedback.className = 'alert alert-danger mt-3';
                feedback.innerHTML =
                    'Gagal mengakses kamera. Pastikan Anda telah memberikan izin kamera pada browser atau gunakan input manual.';
                feedback.style.display = 'block';
                stopScanner();
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log("QR Decoded: ", decodedText);

            // Stop scanning
            stopScanner();

            // Populate input field
            const tokenInput = document.getElementById('ticket-token-input');
            tokenInput.value = decodedText;

            // Show feedback
            const feedback = document.getElementById('scan-feedback');
            feedback.className = 'alert alert-info mt-3';
            feedback.innerHTML = '⏳ <strong>QR Code Terdeteksi!</strong> Memvalidasi tiket...';
            feedback.style.display = 'block';

            // Submit form automatically
            document.getElementById('validate-ticket-form').submit();
        }

        function onScanFailure(error) {
            // Silent fail for frame-by-frame scanner loop
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(err => console.error(err));
            }

            document.getElementById('qr-reader').style.display = 'none';
            document.getElementById('start-scanner-btn').style.display = 'inline-block';
            document.getElementById('stop-scanner-btn').style.display = 'none';
            const statusBadge = document.getElementById('camera-status');
            statusBadge.className = 'scan-status-badge bg-secondary text-white';
            statusBadge.innerHTML = '⚪ Kamera Nonaktif';
        }
    </script>
@endsection
