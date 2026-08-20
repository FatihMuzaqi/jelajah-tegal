@extends('layouts.error')

@section('title', 'Sesi Kadaluarsa')

@section('content')
<div class="py-2">
    <div class="rounded-circle bg-secondary-subtle text-secondary d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 32px;">
        <i class="fa-solid fa-clock-rotate-left"></i>
    </div>
    <h3 class="fw-extrabold text-dark mb-2">Sesi Halaman Kadaluarsa (419)</h3>
    <p class="text-muted fs-7 mb-4">
        Sesi keamanan halaman telah berakhir karena tidak ada aktivitas. Silakan muat ulang halaman ini.
    </p>

    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2">
        <button type="button" class="btn btn-lokantara rounded-pill px-4 py-2.5 fw-bold w-100 w-sm-auto" onclick="location.reload()">
            <i class="fa-solid fa-rotate-right me-1"></i> Muat Ulang Halaman
        </button>
    </div>
</div>
@endsection
