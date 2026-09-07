@extends('layouts.mitra')

@section('title', 'Katalog Wisata')
@section('page-title', 'Katalog Destinasi Wisata')
@section('page-description', 'Kelola destinasi wisata, status publikasi, paket tiket masuk, dan riwayat moderasi.')

@section('page-actions')
    @can('tourism.manage')
        <a class="btn btn-lokantara rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" href="{{ route('mitra.tourism.create') }}">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Wisata</span>
        </a>
    @endcan
@endsection

@section('content')
    @php
        $activeMitra = \App\Models\Mitra::find(session('active_mitra_id'));
    @endphp
    @if ($activeMitra && ! $activeMitra->isKycVerified())
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;">
                    <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-dark">Verifikasi KYC Diperlukan</h6>
                    <p class="small text-muted mb-0">Dokumen legalitas bisnis Anda belum diverifikasi oleh Admin. Silakan lengkapi KYC untuk dapat menambahkan destinasi wisata baru.</p>
                </div>
            </div>
            <a href="{{ route('mitra.kyc.index') }}" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-id-card me-1"></i> Lengkapi KYC
            </a>
        </div>
    @endif

    <x-table-wrapper title="Daftar Destinasi Wisata">
        @if ($items->isEmpty())
            <tbody>
                <tr>
                    <td>
                        <x-empty-state title="Belum ada destinasi wisata" description="Buat draft destinasi wisata pertama Anda untuk diajukan ke kurasi publik." compact>
                            @can('tourism.manage')
                                <a class="btn btn-lokantara rounded-pill px-4 py-2 mt-2 fw-bold d-inline-flex align-items-center gap-2" href="{{ route('mitra.tourism.create') }}">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Wisata Sekarang</span>
                                </a>
                            @endcan
                        </x-empty-state>
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Nama Destinasi</th>
                    <th>Wilayah</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Diperbarui</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>
                            <strong class="d-block text-dark">{{ $item->name }}</strong>
                            <small class="text-muted d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-ticket text-secondary" style="font-size: 11px;"></i>
                                {{ $item->offers->count() }} Paket Tiket
                            </small>
                        </td>
                        <td>
                            <span class="badge text-bg-light border">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                {{ $item->region?->name ?? 'Tegal' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border">
                                <i class="fa-solid fa-umbrella-beach me-1"></i>
                                {{ $item->category?->name ?? 'Wisata' }}
                            </span>
                        </td>
                        <td><x-status-badge :status="$item->status" /></td>
                        <td>
                            <small class="text-muted d-inline-flex align-items-center gap-1">
                                <i class="fa-regular fa-clock" style="font-size: 11px;"></i>
                                {{ $item->updated_at->diffForHumans() }}
                            </small>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-lokantara fw-semibold rounded-pill px-3"
                               href="{{ route('mitra.tourism.show', $item) }}">
                                <i class="fa-solid fa-gear me-1"></i> Kelola
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $items->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
