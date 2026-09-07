@extends('layouts.public')

@section('title', 'Pendaftaran Berhasil Terkirim — Jelajah Tegal')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center bg-white">
                <!-- Icon Success -->
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3 shadow-sm" style="width: 76px; height: 76px; background: #ecfdf5; color: #047857; font-size: 32px;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold fs-8 mx-auto mb-2">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Pendaftaran Berhasil Dikirim
                </span>

                <h1 class="fw-bold fs-3 text-dark mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    Terima Kasih, Pengajuan Kemitraan Anda Telah Kami Terima!
                </h1>

                <p class="text-muted small mx-auto mb-3" style="max-width: 580px; font-size: 14px; line-height: 1.6;">
                    Data profil usaha dan berkas legalitas Anda telah masuk ke dalam antrean verifikasi tim kurasi Jelajah Tegal. Kami akan meninjau keabsahan data dalam waktu 1x24 jam kerja.
                </p>

                <!-- Info Alert: No Manual Email Verification Needed -->
                <div class="alert border-0 rounded-4 text-start mx-auto mb-4 p-3.5" style="background: #eff6ff; border: 1px solid #bfdbfe !important; max-width: 580px;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width: 32px; height: 32px; font-size: 14px;">
                            <i class="fa-solid fa-envelope-circle-check"></i>
                        </div>
                        <div>
                            <strong class="d-block text-primary-emphasis fs-7 mb-1">
                                Anda Tidak Perlu Mengirim Ulang Verifikasi Email Mandiri
                            </strong>
                            <p class="text-dark small mb-0" style="font-size: 12.5px; line-height: 1.55;">
                                Demi kenyamanan Anda, verifikasi akun ditangani terpusat. Begitu tim administrator menyetujui pendaftaran kemitraan Anda, sistem akan secara otomatis mengirimkan <strong>email persetujuan resmi</strong> ke alamat email pemilik:
                                @if($mitra && $mitra->owner?->email)
                                    <span class="badge bg-light text-primary border border-primary-subtle fw-semibold mt-1 d-inline-block">{{ $mitra->owner->email }}</span>
                                @endif
                                <br>
                                Di dalam email tersebut tersedia tautan khusus yang akan <strong>langsung memverifikasi email dan otomatis memasukkan Anda (auto-login)</strong> ke dalam Dashboard Mitra tanpa perlu input ulang kata sandi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tracking Card -->
                @if ($mitra)
                    <div class="p-3.5 rounded-4 border text-start mb-4 text-dark mx-auto" style="background: #f8fafc; max-width: 580px; font-size: 13px;">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                            <span class="text-muted fs-8">Status Pengajuan:</span>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-0.5 fw-bold fs-8">
                                <i class="fa-solid fa-hourglass-half me-1"></i> Menunggu Kurasi Admin
                            </span>
                        </div>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted fs-8 d-block">Nama Usaha / Mitra:</span>
                                <strong class="text-dark">{{ $mitra->display_name }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted fs-8 d-block">Layanan:</span>
                                <span class="badge bg-light text-dark border">{{ $mitra->serviceType?->name ?? 'Layanan' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted fs-8 d-block">Penanggung Jawab:</span>
                                <span>{{ $mitra->owner?->name ?? '-' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted fs-8 d-block">Email Penerima Akses:</span>
                                <span>{{ $mitra->owner?->email ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Visual Verification Timeline -->
                <div class="p-3.5 rounded-4 border mb-4 text-start mx-auto" style="background: #ffffff; max-width: 580px;">
                    <h6 class="fw-bold text-dark fs-7 mb-3"><i class="fa-solid fa-list-check text-primary me-1"></i> Tahapan Selanjutnya:</h6>
                    
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 12px;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark fs-7">1. Berkas & Formulir Diterima</strong>
                            <small class="text-muted" style="font-size: 11.5px;">Data identitas, titik koordinat peta, dan dokumen legalitas berhasil disimpan.</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 12px; font-weight: 800;">
                            2
                        </div>
                        <div>
                            <strong class="d-block text-dark fs-7">2. Kurasi & Persetujuan oleh Administrator</strong>
                            <small class="text-muted" style="font-size: 11.5px;">Pemeriksaan keabsahan berkas oleh Dinas Kepemudaan, Olahraga, dan Pariwisata / Tim Jelajah Tegal.</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-light text-muted border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 12px; font-weight: 800;">
                            3
                        </div>
                        <div>
                            <strong class="d-block text-muted fs-7">3. Notifikasi Email & Sekali Klik Auto-Login</strong>
                            <small class="text-muted" style="font-size: 11.5px;">Setelah disetujui, email masuk dikirim beserta tautan sekali klik langsung ke Dashboard Mitra Anda.</small>
                        </div>
                    </div>
                </div>

                <!-- Action CTA Buttons -->
                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                    <a href="{{ route('home') }}" class="btn btn-lokantara rounded-pill px-4 py-2 fw-semibold fs-7 shadow-sm">
                        <i class="fa-solid fa-house me-1"></i> Kembali ke Beranda
                    </a>
                    <a href="{{ route('public.about') }}" class="btn btn-light border rounded-pill px-4 py-2 fw-semibold fs-7">
                        <i class="fa-solid fa-circle-info me-1"></i> Tentang Jelajah Tegal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
