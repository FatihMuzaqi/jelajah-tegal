@extends('layouts.error')

@section('title', 'Pemeliharaan Sistem')

@section('content')
<div class="py-2">
    <div class="rounded-circle bg-info-subtle text-info d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 32px;">
        <i class="fa-solid fa-screwdriver-wrench"></i>
    </div>
    <h3 class="fw-extrabold text-dark mb-2">Pemeliharaan Sistem (503)</h3>
    <p class="text-muted fs-7 mb-4">
        Platform Jelajah Tegal sedang dalam pemeliharaan berkala untuk peningkatan kualitas layanan. Silakan coba kembali beberapa saat lagi.
    </p>

    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2">
        <button type="button" class="btn btn-lokantara rounded-pill px-4 py-2.5 fw-bold w-100 w-sm-auto" onclick="location.reload()">
            <i class="fa-solid fa-rotate-right me-1"></i> Coba Cek Lagi
        </button>
    </div>
</div>
@endsection
