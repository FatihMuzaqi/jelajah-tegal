@extends('layouts.error')

@section('title', 'Akses Ditolak')

@section('content')
<div class="py-2">
    <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 32px;">
        <i class="fa-solid fa-shield-halved"></i>
    </div>
    <h3 class="fw-extrabold text-dark mb-2">Akses Ditolak (403)</h3>
    <p class="text-muted fs-7 mb-4">
        Anda tidak memiliki hak akses atau perizinan untuk membuka halaman ini.
    </p>

    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2">
        <a href="{{ route('post-login') }}" class="btn btn-lokantara rounded-pill px-4 py-2.5 fw-bold w-100 w-sm-auto">
            <i class="fa-solid fa-gauge me-1"></i> Ke Dashboard
        </a>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold w-100 w-sm-auto">
            <i class="fa-solid fa-house me-1"></i> Ke Beranda
        </a>
    </div>
</div>
@endsection
