@extends('layouts.error')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="py-2">
    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 32px;">
        <i class="fa-solid fa-compass"></i>
    </div>
    <h3 class="fw-extrabold text-dark mb-2">Halaman Tidak Ditemukan (404)</h3>
    <p class="text-muted fs-7 mb-4">
        Halaman yang Anda cari tidak tersedia, telah dipindahkan, atau link yang digunakan tidak valid.
    </p>

    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2">
        <a href="{{ url('/') }}" class="btn btn-lokantara rounded-pill px-4 py-2.5 fw-bold w-100 w-sm-auto">
            <i class="fa-solid fa-house me-1"></i> Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
