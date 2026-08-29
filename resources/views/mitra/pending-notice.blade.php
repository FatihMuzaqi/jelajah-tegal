@extends('layouts.public')

@section('title', 'Status Kemitraan — Jelajah Tegal')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white text-center">
                @if ($mitra && $mitra->status === 'rejected')
                    <!-- REJECTED STATE -->
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3 shadow-sm" style="width: 76px; height: 76px; background: #fef2f2; color: #dc2626; font-size: 32px;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold fs-8 mx-auto mb-2">
                        Pendaftaran Belum Disetujui
                    </span>

                    <h2 class="fw-bold fs-3 text-dark mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        Pengajuan Kemitraan Ditolak
                    </h2>

                    <p class="text-muted small mx-auto mb-4" style="max-width: 540px; font-size: 13.5px;">
                        Mohon maaf, pengajuan kemitraan untuk <strong>{{ $mitra->display_name }}</strong> belum dapat disetujui oleh tim kurator dikarenakan alasan berikut:
                    </p>

                    <!-- Rejection Reason Card -->
                    <div class="p-3.5 rounded-4 border border-danger-subtle bg-danger-subtle bg-opacity-25 text-start mb-4 mx-auto" style="max-width: 580px;">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="fa-solid fa-circle-exclamation text-danger mt-1 fs-5 flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-danger fs-7 mb-1">Catatan Verifikator Admin:</strong>
                                <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.6;">
                                    {{ $mitra->rejection_reason ?: 'Dokumen pendukung atau kelengkapan legalitas belum memenuhi standar operasional kemitraan daerah.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted fs-8 mx-auto mb-4" style="max-width: 500px;">
                        Silakan hubungi tim administrasi Dinas Pariwisata atau ajukan kembali pendaftaran dengan dokumen yang telah diperbaiki.
                    </p>

                @else
                    <!-- PENDING STATE -->
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3 shadow-sm" style="width: 76px; height: 76px; background: #fffbeb; color: #d97706; font-size: 32px;">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>

                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-bold fs-8 mx-auto mb-2">
                        Sedang Dalam Peninjauan
                    </span>

                    <h2 class="fw-bold fs-3 text-dark mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        Akun Mitra Menunggu Verifikasi
                    </h2>

                    <p class="text-muted small mx-auto mb-4" style="max-width: 540px; font-size: 13.5px;">
                        Pengajuan kemitraan untuk unit usaha <strong>{{ $mitra->display_name ?? 'Anda' }}</strong> saat ini sedang dalam proses verifikasi dan audit berkas oleh tim Dinas Pemuda, Olahraga dan Pariwisata.
                    </p>

                    <!-- Info Box -->
                    <div class="p-3.5 rounded-4 border bg-light text-start mb-4 mx-auto" style="max-width: 580px; font-size: 13px;">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted fs-8 d-block">Nama Usaha:</span>
                                <strong class="text-dark">{{ $mitra->display_name ?? '-' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted fs-8 d-block">Penanggung Jawab:</span>
                                <span>{{ $user->name ?? '-' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted fs-8 d-block">Waktu Pengajuan:</span>
                                <span>{{ $mitra ? $mitra->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted fs-8 d-block">Estimasi Waktu:</span>
                                <span class="text-success fw-semibold">Maksimal 1 x 24 Jam Kerja</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                    <a href="{{ route('home') }}" class="btn btn-light border rounded-pill px-4 py-2 fs-7 fw-semibold">
                        <i class="fa-solid fa-house me-1"></i> Ke Beranda
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-4 py-2 fs-7 fw-semibold">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar (Logout)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
