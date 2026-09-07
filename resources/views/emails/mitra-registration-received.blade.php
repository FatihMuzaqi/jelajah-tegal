<!DOCTYPE html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Pendaftaran Kemitraan Diterima — Jelajah Tegal</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; display: block; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; height: 100% !important; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        
        @media screen and (max-width: 600px) {
            .wrapper-table { padding: 12px 6px !important; }
            .email-card { width: 100% !important; max-width: 100% !important; border-radius: 14px !important; }
            .header-cell { padding: 20px 16px !important; }
            .body-cell { padding: 22px 16px 20px !important; }
            .footer-cell { padding: 22px 16px !important; font-size: 11.5px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapper-table" style="table-layout: fixed; background-color: #f1f5f9; padding: 24px 12px;">
        <tr>
            <td align="center" style="padding: 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="email-card" style="max-width: 600px; width: 100%; margin: 0 auto; background-color: #ffffff; border-radius: 18px; overflow: hidden; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07); border: 1px solid #e2e8f0;">
                    
                    <!-- Header with Brand & Logo -->
                    <tr>
                        <td class="header-cell" style="background: linear-gradient(135deg, #047857 0%, #064e3b 100%); padding: 28px 32px; text-align: left; border-bottom: 3px solid #10b981;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding-right: 12px; vertical-align: middle;">
                                                    <div style="width: 40px; height: 40px; background: #ffffff; border-radius: 10px; text-align: center; line-height: 40px; display: inline-block; overflow: hidden;">
                                                        <img src="{{ config('app.url') }}/images/logo.png" alt="Logo Jelajah Tegal" width="32" height="32" style="vertical-align: middle; border-radius: 6px; margin: 4px auto 0; display: block;">
                                                    </div>
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <span style="font-size: 19px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; display: block; line-height: 1.2;">JELAJAH TEGAL</span>
                                                    <span style="font-size: 10.5px; font-weight: 600; color: #a7f3d0; letter-spacing: 0.06em; text-transform: uppercase;">Portal Terpadu Pariwisata Daerah</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="body-cell" style="padding: 32px 32px 24px; background-color: #ffffff;">
                            
                            <!-- Status Badge -->
                            <div style="display: inline-block; background-color: #fefce8; border: 1px solid #fef08a; border-radius: 99px; padding: 4px 14px; margin-bottom: 16px;">
                                <span style="font-size: 11px; font-weight: 700; color: #854d0e; text-transform: uppercase; letter-spacing: 0.04em;">
                                    Status: Menunggu Kurasi &amp; Verifikasi Admin
                                </span>
                            </div>

                            <h1 style="margin: 0 0 14px; font-size: 21px; font-weight: 800; color: #0f172a; line-height: 1.35; letter-spacing: -0.02em;">
                                Pendaftaran Mitra Berhasil Dikirim
                            </h1>

                            <p style="margin: 0 0 14px; font-size: 14.5px; color: #334155; line-height: 1.6;">
                                Halo <strong>{{ $recipientName }}</strong>,
                            </p>

                            <p style="margin: 0 0 18px; font-size: 14px; color: #475569; line-height: 1.6;">
                                Terima kasih telah mendaftarkan bisnis <strong>{{ $mitra->display_name }}</strong> untuk bergabung dalam ekosistem promosi pariwisata terpadu <strong>Jelajah Tegal</strong>. Berkas dan formulir pendaftaran Anda telah kami terima secara lengkap dan telah masuk dalam antrean kurasi tim administrator.
                            </p>

                            <!-- Detail Card -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 22px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 16px 18px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 12.5px; color: #64748b; font-weight: 600; width: 40%; vertical-align: top;">Nama Usaha:</td>
                                                <td style="padding: 5px 0; font-size: 13.5px; color: #047857; font-weight: 700; vertical-align: top;">{{ $mitra->display_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 12.5px; color: #64748b; font-weight: 600; vertical-align: top;">Jenis Layanan:</td>
                                                <td style="padding: 5px 0; font-size: 13.5px; color: #0f172a; font-weight: 600; vertical-align: top;">{{ $mitra->serviceType?->name ?? 'Layanan Usaha' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 12.5px; color: #64748b; font-weight: 600; vertical-align: top;">Wilayah:</td>
                                                <td style="padding: 5px 0; font-size: 13.5px; color: #0f172a; font-weight: 600; vertical-align: top;">{{ $mitra->region?->name ?? 'Tegal' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 12.5px; color: #64748b; font-weight: 600; vertical-align: top;">Waktu Kirim:</td>
                                                <td style="padding: 5px 0; font-size: 13.5px; color: #0f172a; font-weight: 600; vertical-align: top;">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box Timeline -->
                            <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px;">
                                <strong style="font-size: 12.5px; color: #065f46; display: block; margin-bottom: 4px;">Tahap Verifikasi Selanjutnya:</strong>
                                <p style="margin: 0; font-size: 12.5px; color: #047857; line-height: 1.55;">
                                    Proses verifikasi dokumen KTP, legalitas usaha, dan rekening bank membutuhkan waktu rata-rata 1x24 jam kerja. Setelah disetujui, kami akan mengirimkan email konfirmasi resmi dan akses login ke Portal Mitra Anda akan otomatis terbuka.
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer-cell" style="padding: 24px 32px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b; line-height: 1.6;">
                            <p style="margin: 0 0 6px; font-weight: 600; color: #334155;">Pemerintah Kabupaten Tegal &middot; Dinas Kepemudaan, Olahraga dan Pariwisata</p>
                            <p style="margin: 0 0 8px;">Surel konfirmasi otomatis sistem Jelajah Tegal atas pendaftaran mitra baru.</p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">&copy; {{ date('Y') }} Jelajah Tegal. Hak Cipta Dilindungi Undang-Undang.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
