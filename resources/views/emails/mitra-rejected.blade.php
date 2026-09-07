<!DOCTYPE html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Pemberitahuan Status Pendaftaran Mitra — Jelajah Tegal</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; display: block; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; height: 100% !important; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        
        .cta-btn-link:hover {
            background-color: #047857 !important;
            box-shadow: 0 6px 18px rgba(4, 120, 87, 0.45) !important;
        }

        @media screen and (max-width: 600px) {
            .wrapper-table { padding: 12px 6px !important; }
            .email-card { width: 100% !important; max-width: 100% !important; border-radius: 14px !important; }
            .header-cell { padding: 20px 16px !important; }
            .body-cell { padding: 22px 16px 20px !important; }
            .cta-btn-link { width: 100% !important; display: block !important; box-sizing: border-box !important; padding: 14px 16px !important; font-size: 14px !important; text-align: center !important; }
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
                            <div style="display: inline-block; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 99px; padding: 4px 14px; margin-bottom: 16px;">
                                <span style="font-size: 11px; font-weight: 700; color: #b91c1c; text-transform: uppercase; letter-spacing: 0.04em;">
                                    Status: Pendaftaran Memerlukan Perbaikan
                                </span>
                            </div>

                            <h1 style="margin: 0 0 14px; font-size: 21px; font-weight: 800; color: #0f172a; line-height: 1.35; letter-spacing: -0.02em;">
                                Peninjauan Pendaftaran Kemitraan
                            </h1>

                            <p style="margin: 0 0 14px; font-size: 14.5px; color: #334155; line-height: 1.6;">
                                Yth. <strong>{{ $recipientName }}</strong>,
                            </p>

                            <p style="margin: 0 0 18px; font-size: 14px; color: #475569; line-height: 1.6;">
                                Terima kasih atas minat Anda bergabung sebagai mitra resmi <strong>Jelajah Tegal</strong>. Setelah tim kurasi melakukan verifikasi terhadap data dan kelengkapan dokumen pendaftaran <strong>{{ $mitra->display_name }}</strong>, permohonan Anda saat ini <strong>belum dapat disetujui</strong> karena terdapat berkas atau data yang belum memenuhi kriteria standardisasi platform.
                            </p>

                            <!-- Reason Box -->
                            <div style="background-color: #fff1f2; border-left: 4px solid #e11d48; border-radius: 10px; padding: 16px 18px; margin-bottom: 22px;">
                                <strong style="font-size: 13px; color: #9f1239; display: block; margin-bottom: 6px;">Catatan / Alasan dari Administrator:</strong>
                                <div style="font-size: 13.5px; color: #881337; line-height: 1.6; background-color: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #fecdd3;">
                                    {{ $reason }}
                                </div>
                            </div>

                            <!-- Detail Card -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 22px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 16px 18px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 12.5px; color: #64748b; font-weight: 600; width: 40%; vertical-align: top;">Nama Bisnis:</td>
                                                <td style="padding: 5px 0; font-size: 13.5px; color: #0f172a; font-weight: 700; vertical-align: top;">{{ $mitra->display_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 12.5px; color: #64748b; font-weight: 600; vertical-align: top;">Jenis Layanan:</td>
                                                <td style="padding: 5px 0; font-size: 13.5px; color: #0f172a; font-weight: 600; vertical-align: top;">{{ $mitra->serviceType?->name ?? 'Layanan Usaha' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 12.5px; color: #64748b; font-weight: 600; vertical-align: top;">Email Terdaftar:</td>
                                                <td style="padding: 5px 0; font-size: 13.5px; color: #0f172a; font-weight: 600; word-break: break-all; vertical-align: top;">{{ $recipientEmail }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 16px; font-size: 14px; color: #475569; line-height: 1.6;">
                                Anda dapat memperbaiki dokumen yang diminta (seperti foto KTP yang lebih jelas, foto lokasi tempat usaha dari depan, atau nomor izin usaha) dan mengajukan ulang pendaftaran kemitraan, atau menghubungi narahubung kurasi kami untuk bantuan lebih lanjut.
                            </p>

                            <!-- CTA Button -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $contactUrl }}" target="_blank" class="cta-btn-link" style="display: inline-block; min-width: 240px; max-width: 100%; box-sizing: border-box; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff !important; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 26px; border-radius: 10px; box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35); text-transform: uppercase; letter-spacing: 0.03em; text-align: center;">
                                            Hubungi Layanan Bantuan &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer-cell" style="padding: 24px 32px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b; line-height: 1.6;">
                            <p style="margin: 0 0 6px; font-weight: 600; color: #334155;">Pemerintah Kabupaten Tegal &middot; Dinas Kepemudaan, Olahraga dan Pariwisata</p>
                            <p style="margin: 0 0 8px;">Surel resmi ini dikirimkan otomatis oleh sistem Jelajah Tegal terkait verifikasi pendaftaran mitra.</p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">&copy; {{ date('Y') }} Jelajah Tegal. Hak Cipta Dilindungi Undang-Undang.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
