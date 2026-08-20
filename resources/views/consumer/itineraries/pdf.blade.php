<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rencana Liburan AI - #{{ $invoice->invoice_number }} - Jelajah Tegal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <style>
        :root {
            --primary: #047857;
            --primary-dark: #064e3b;
            --primary-light: #ecfdf5;
            --dark: #0f172a;
            --muted: #64748b;
            --border: #cbd5e1;
            --border-light: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--dark);
            background: #f8fafc;
            line-height: 1.5;
            font-size: 13px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .pdf-toolbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-light);
            padding: 14px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .pdf-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .pdf-btn-primary {
            background: var(--primary);
            color: #ffffff;
        }
        .pdf-btn-primary:hover {
            background: var(--primary-dark);
        }

        .pdf-btn-outline {
            background: #ffffff;
            color: var(--dark);
            border-color: var(--border);
        }
        .pdf-btn-outline:hover {
            background: #f1f5f9;
        }

        /* Document Container (A4 Formatted) */
        .pdf-document {
            max-width: 960px;
            margin: 30px auto;
            background: #ffffff;
            padding: 40px 48px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid var(--border-light);
        }

        /* Official Header */
        .doc-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 20px;
            margin-bottom: 24px;
        }

        .doc-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .doc-brand img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            border-radius: 8px;
        }

        .doc-brand-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary-dark);
            line-height: 1.1;
        }

        .doc-brand-sub {
            font-size: 11px;
            color: var(--muted);
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .doc-meta {
            text-align: right;
        }

        .doc-title-badge {
            display: inline-block;
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 800;
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 99px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
            border: 1px solid rgba(4, 120, 87, 0.2);
        }

        .doc-invoice-num {
            font-size: 14px;
            font-weight: 800;
            color: var(--dark);
        }

        .doc-status-paid {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            font-weight: 800;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 6px;
            border: 1px solid #bbf7d0;
            margin-top: 4px;
        }

        /* Profile Summary Box */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            background: #f8fafc;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 28px;
        }

        .summary-item small {
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 2px;
            letter-spacing: 0.04em;
        }

        .summary-item strong {
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
        }

        /* Section Headings */
        .section-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid var(--border-light);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        /* Structured Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 12px;
        }

        .data-table th {
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.04em;
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #0f172a;
        }

        .data-table td {
            padding: 9px 12px;
            border: 1px solid var(--border-light);
            vertical-align: middle;
            color: #1e293b;
        }

        .data-table tr:nth-child(even) {
            background: #f8fafc;
        }

        .data-table tr.day-header-row {
            background: #e2e8f0 !important;
            font-weight: 800;
            color: #0f172a;
        }

        .data-table tr.day-header-row td {
            padding: 7px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-top: 2px solid #94a3b8;
            border-bottom: 1px solid #94a3b8;
        }

        .category-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            background: #e0f2fe;
            color: #0369a1;
            white-space: nowrap;
        }

        .time-pill {
            font-family: monospace;
            font-weight: 700;
            font-size: 11px;
            color: var(--primary-dark);
            white-space: nowrap;
        }

        .cost-col {
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        /* Footer Notes & QR */
        .doc-footer {
            border-top: 1px solid var(--border);
            padding-top: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            font-size: 11px;
            color: var(--muted);
        }

        .qr-box {
            text-align: center;
            flex-shrink: 0;
        }

        .qr-box img {
            width: 80px;
            height: 80px;
            border: 1px solid var(--border);
            padding: 4px;
            border-radius: 6px;
            background: #ffffff;
        }

        /* Print Media Styles */
        @media print {
            .pdf-toolbar {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                font-size: 11.5px;
            }

            .pdf-document {
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
            }

            .data-table th {
                background: #0f172a !important;
                color: #ffffff !important;
            }

            .data-table tr.day-header-row {
                background: #e2e8f0 !important;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Toolbar (Hidden when printing) -->
    <div class="pdf-toolbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('consumer.itineraries.show', $invoice->id) }}" class="pdf-btn pdf-btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail
            </a>
            <span style="font-size: 13px; font-weight: 700; color: var(--muted);">
                Rencana Perjalanan AI #{{ $invoice->invoice_number }}
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <button type="button" class="pdf-btn pdf-btn-primary" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    @php
        $meta = $itinerary ?? [];
        $days = $meta['days'] ?? [];
        $startDate = $meta['start_date'] ?? $invoice->created_at->format('Y-m-d');
        $endDate = $meta['end_date'] ?? $invoice->created_at->format('Y-m-d');
        $totalDays = $meta['total_days'] ?? (count($days) ?: 1);
        $nights = $meta['nights'] ?? max(0, $totalDays - 1);
        $pax = $meta['pax'] ?? 1;
        $user = $invoice->user;
    @endphp

    <!-- Official Document Sheet -->
    <div class="pdf-document">
        
        <!-- 1. Official Header -->
        <div class="doc-header">
            <div class="doc-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Jelajah Tegal">
                <div>
                    <h1 class="doc-brand-title">JELAJAH TEGAL</h1>
                    <p class="doc-brand-sub">Platform Digital Terpadu Pariwisata & Ekonomi Kreatif Tegal</p>
                </div>
            </div>
            <div class="doc-meta">
                <span class="doc-title-badge">Dokumen Rencana Liburan AI</span>
                <div class="doc-invoice-num">#{{ $invoice->invoice_number }}</div>
                <div>
                    <span class="doc-status-paid">
                        <i class="fa-solid fa-circle-check"></i> STATUS: LUNAS (PAID)
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. Package Profile & Tourist Summary Grid -->
        <div class="summary-grid">
            <div class="summary-item">
                <small>Nama Wisatawan</small>
                <strong>{{ $user?->name ?? 'Wisatawan' }}</strong>
                <div style="font-size: 11px; color: var(--muted);">{{ $user?->phone ?? $user?->email }}</div>
            </div>
            <div class="summary-item">
                <small>Paket & Tema Liburan</small>
                <strong>{{ $meta['package_name'] ?? 'Paket Rekomendasi AI' }}</strong>
                <div style="font-size: 11px; color: var(--primary); font-weight: 600;">{{ $meta['headline'] ?? 'Eksplorasi Tegal' }}</div>
            </div>
            <div class="summary-item">
                <small>Periode & Durasi</small>
                <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
                <div style="font-size: 11px; color: var(--muted);">{{ $totalDays }} Hari / {{ $nights }} Malam ({{ $pax }} Orang)</div>
            </div>
            <div class="summary-item">
                <small>Total Biaya Paket</small>
                <strong style="color: var(--primary); font-size: 14px;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong>
                <div style="font-size: 10px; color: var(--muted);">Dibayar: {{ $invoice->paid_at ? $invoice->paid_at->translatedFormat('d M Y, H:i') : now()->translatedFormat('d M Y') }}</div>
            </div>
        </div>

        <!-- 3. TABEL 1: JADWAL KRONOLOGIS PERJALANAN (HARI & JAM) -->
        <div class="section-header">
            <i class="fa-solid fa-calendar-days text-primary"></i> Tabel 1: Jadwal & Rencana Perjalanan Lengkap (Hari & Jam)
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">No</th>
                    <th style="width: 90px;">Waktu (WIB)</th>
                    <th style="width: 100px;">Kategori</th>
                    <th>Destinasi & Rincian Agenda Kegiatan</th>
                    <th style="width: 150px;">Lokasi / Wilayah</th>
                    <th style="width: 110px; text-align: right;">Estimasi Biaya</th>
                </tr>
            </thead>
            <tbody>
                @php $rowNumber = 1; @endphp
                @forelse($days as $day)
                    <!-- Day Header Row -->
                    <tr class="day-header-row">
                        <td colspan="6">
                            <strong>{{ $day['title'] }}</strong> &mdash; {{ $day['formatted_date'] }}
                        </td>
                    </tr>

                    @foreach($day['activities'] as $act)
                        @php
                            $actType = $act['type'] ?? 'tourism';
                            $categoryLabel = match($actType) {
                                'tourism' => 'Wisata',
                                'accommodation' => 'Penginapan',
                                'culinary' => 'Kuliner',
                                'rental' => 'Rental',
                                'event' => 'Event',
                                default => 'Aktivitas'
                            };
                            $itemData = $act['item'] ?? null;
                            $estimatedCost = ($itemData && !empty($itemData['subtotal'])) ? ('Rp ' . number_format($itemData['subtotal'], 0, ',', '.')) : 'Termasuk Paket';
                            $locationName = $act['location'] ?? ($act['location_name'] ?? 'Tegal');
                        @endphp
                        <tr>
                            <td style="text-align: center; color: var(--muted); font-weight: 600;">
                                {{ $rowNumber++ }}
                            </td>
                            <td>
                                <span class="time-pill">{{ $act['time'] }}</span>
                            </td>
                            <td>
                                <span class="category-badge">{{ $categoryLabel }}</span>
                            </td>
                            <td>
                                <strong style="color: #0f172a; display: block; margin-bottom: 2px;">{{ $act['title'] }}</strong>
                                <span style="font-size: 11px; color: var(--muted);">{{ $act['description'] }}</span>
                            </td>
                            <td>
                                <span style="font-size: 11.5px; font-weight: 600; color: #334155;">
                                    <i class="fa-solid fa-location-dot" style="color: #ef4444; font-size: 10px; margin-right: 3px;"></i>
                                    {{ $locationName }}
                                </span>
                            </td>
                            <td class="cost-col">
                                {{ $estimatedCost }}
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--muted); padding: 20px;">
                            Tidak ada jadwal kegiatan khusus.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 4. TABEL 2: RINCIAN VOUCHER & LAYANAN MITRA TERBAYAR -->
        <div class="section-header" style="margin-top: 20px;">
            <i class="fa-solid fa-receipt text-primary"></i> Tabel 2: Rincian Layanan & E-Tiket Multi-Mitra Terbayar
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">No</th>
                    <th style="width: 170px;">Mitra Penyedia</th>
                    <th>Item Layanan / Kamar / Paket Wisata</th>
                    <th style="width: 60px; text-align: center;">Qty</th>
                    <th style="width: 110px; text-align: right;">Harga Satuan</th>
                    <th style="width: 120px; text-align: right;">Total Subtotal</th>
                    <th style="width: 110px; text-align: center;">Kode E-Tiket</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $orderIndex = 1; 
                    $calculatedTotal = 0;
                @endphp
                @forelse($invoice->orders as $order)
                    @foreach($order->items as $item)
                        @php
                            $calculatedTotal += (float) $item->line_total;
                            $ticketCodes = $item->tickets->pluck('ticket_code')->toArray();
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: var(--muted);">
                                {{ $orderIndex++ }}
                            </td>
                            <td>
                                <strong style="color: var(--primary-dark);">{{ $order->mitra?->display_name ?? 'Mitra Jelajah Tegal' }}</strong>
                            </td>
                            <td>
                                <strong style="color: #0f172a;">{{ $item->item_name }}</strong>
                                <div style="font-size: 10.5px; color: var(--muted);">Tipe: {{ str($item->resource_type)->headline() }}</div>
                            </td>
                            <td style="text-align: center; font-weight: 700;">
                                {{ $item->quantity }}
                            </td>
                            <td style="text-align: right;">
                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--primary-dark);">
                                Rp {{ number_format($item->line_total, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center; font-family: monospace; font-size: 11px; font-weight: 700;">
                                @if(!empty($ticketCodes))
                                    {{ implode(', ', $ticketCodes) }}
                                @else
                                    <span style="color: #16a34a;">LUNAS</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--muted); padding: 16px;">
                            Tidak ada rincian pesanan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #f1f5f9; font-weight: 800; font-size: 13px;">
                    <td colspan="5" style="text-align: right; padding: 10px 12px; text-transform: uppercase;">
                        TOTAL PEMBAYARAN KESELURUHAN (LUNAS):
                    </td>
                    <td style="text-align: right; padding: 10px 12px; color: var(--primary); font-size: 14px;">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- 5. Footer Notes & Verification QR -->
        <div class="doc-footer">
            <div style="flex-grow: 1;">
                <strong style="color: #0f172a; display: block; margin-bottom: 3px; font-size: 12px;">Ketentuan & Panduan Wisata:</strong>
                <ul style="padding-left: 16px; line-height: 1.6; margin-bottom: 8px;">
                    <li>Tunjukkan dokumen ini atau QR Code E-Tiket kepada petugas mitra terdaftar saat memasuki destinasi atau check-in hotel.</li>
                    <li>Estimasi waktu bersifat rekomendasi optimal dan dapat disesuaikan dengan kondisi lalu lintas serta cuaca lokal.</li>
                    <li>Untuk bantuan darurat atau perubahan jadwal, hubungi Helpdesk Resmi Jelajah Tegal di (0283) 123-4567.</li>
                </ul>
                <div style="font-size: 10px; color: var(--muted);">
                    Dicetak secara otomatis dari Platform Resmi Jelajah Tegal pada {{ now()->translatedFormat('d F Y, H:i:s T') }}.
                </div>
            </div>

            <div class="qr-box">
                @if(!empty($qrDataUri))
                    <img src="{{ $qrDataUri }}" alt="QR Code Invoice">
                @else
                    <div style="width: 80px; height: 80px; border: 1px solid var(--border); display: grid; place-items: center; font-size: 9px; text-align: center; color: var(--muted); border-radius: 6px;">
                        VALIDATED<br>OFFICIAL
                    </div>
                @endif
                <div style="font-size: 9px; font-weight: 700; color: var(--muted); margin-top: 3px; text-transform: uppercase;">
                    Verifikasi Invoice
                </div>
            </div>
        </div>

    </div>

</body>
</html>
