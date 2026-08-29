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
                    Pendaftaran Berhasil Dikirim
                </span>

                <h1 class="fw-bold fs-3 text-dark mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    Terima Kasih, Pengajuan Kemitraan Anda Telah Kami Terima!
                </h1>

                <p class="text-muted small mx-auto mb-4" style="max-width: 560px; font-size: 14px; line-height: 1.6;">
                    Data profil usaha dan dokumen legalitas Anda telah masuk ke dalam antrean kurasi tim verifikator Jelajah Tegal. Kami akan meninjau keabsahan dokumen dalam waktu 1x24 jam kerja.
                </p>

                <!-- Tracking Card -->
                @if ($mitra)
                    <div class="p-3.5 rounded-4 border text-start mb-4 text-dark mx-auto" style="background: #f8fafc; max-width: 580px; font-size: 13px;">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                            <span class="text-muted fs-8">Status Pendaftaran:</span>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-0.5 fw-bold fs-8">
                                <i class="fa-solid fa-clock me-1"></i> Menunggu Verifikasi Admin
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
                                <span class="text-muted fs-8 d-block">Email Akun:</span>
                                <span>{{ $mitra->owner?->email ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Visual Verification Timeline -->
                <div class="p-3.5 rounded-4 border mb-4 text-start mx-auto" style="background: #ffffff; max-width: 580px;">
                    <h6 class="fw-bold text-dark fs-7 mb-3"><i class="fa-solid fa-timeline text-primary me-1"></i> Alur Tahapan Verifikasi:</h6>
                    
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 12px;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark fs-7">1. Formulir & Dokumen Terkirim</strong>
                            <small class="text-muted" style="font-size: 11.5px;">Data identitas, KTP, dan kelengkapan profil usaha berhasil tersimpan di sistem.</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 12px; font-weight: 800;">
                            2
                        </div>
                        <div>
                            <strong class="d-block text-dark fs-7">2. Verifikasi Berkas & Kurasi Tim Admin (Sedang Berjalan)</strong>
                            <small class="text-muted" style="font-size: 11.5px;">Pemeriksaan keaslian lokasi, KTP, NIB/NPWP, dan foto tempat usaha oleh dinas/admin.</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-light text-muted border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 12px; font-weight: 800;">
                            3
                        </div>
                        <div>
                            <strong class="d-block text-muted fs-7">3. Aktivasi Portal & Terbit Badge "Terverifikasi"</strong>
                            <small class="text-muted" style="font-size: 11.5px;">Setelah disetujui, akun mitra aktif dan dapat langsung mempublikasikan tiket serta produk wisata.</small>
                        </div>
                    </div>
                </div>

                <!-- Action CTA Buttons -->
                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                    <a href="{{ route('home') }}" class="btn btn-light border rounded-pill px-4 py-2 fw-semibold fs-7">
                        <i class="fa-solid fa-house me-1"></i> Kembali ke Beranda
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-lokantara rounded-pill px-4 py-2 fw-bold fs-7 shadow-sm">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk ke Portal Mitra
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
