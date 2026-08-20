@extends('layouts.error')

@section('title', 'Terjadi Kesalahan Server')

@section('content')
<div class="py-2">
    <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 32px;">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <h3 class="fw-extrabold text-dark mb-2">Terjadi Kesalahan</h3>
    <p class="text-muted fs-7 mb-4">
        Sistem mengalami kendala sementara atau data belum dapat dimuat. Silakan muat ulang atau kembali ke halaman sebelumnya.
    </p>

    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2">
        <button type="button" class="btn btn-lokantara rounded-pill px-4 py-2.5 fw-bold w-100 w-sm-auto" onclick="location.reload()">
            <i class="fa-solid fa-rotate-right me-1"></i> Coba Lagi
        </button>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold w-100 w-sm-auto">
            <i class="fa-solid fa-house me-1"></i> Ke Beranda
        </a>
    </div>
</div>
@endsection
