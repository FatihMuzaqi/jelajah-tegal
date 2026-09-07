@extends('layouts.mitra')

@section('title', 'Katalog Penginapan')
@section('page-title', 'Katalog Penginapan & Hotel')
@section('page-description', 'Kelola properti penginapan, kamar, tarif sewa, kalender ketersediaan, dan status moderasi.')

@section('page-actions')
    @can('accommodation.manage')
        <a class="btn btn-lokantara rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" href="{{ route('mitra.accommodation.create') }}">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Properti</span>
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
                    <p class="small text-muted mb-0">Dokumen legalitas bisnis Anda belum diverifikasi oleh Admin. Silakan lengkapi KYC untuk dapat menambahkan penginapan / akomodasi baru.</p>
                </div>
            </div>
            <a href="{{ route('mitra.kyc.index') }}" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-id-card me-1"></i> Lengkapi KYC
            </a>
        </div>
    @endif

    <x-table-wrapper title="Daftar Properti Penginapan">
        @if ($items->isEmpty())
            <tbody>
                <tr>
                    <td>
                        <x-empty-state title="Belum ada properti penginapan" description="Tambahkan properti hotel, homestay, atau villa pertama Anda untuk diajukan ke kurasi publik." compact>
                            @can('accommodation.manage')
                                <a class="btn btn-lokantara rounded-pill px-4 py-2 mt-2 fw-bold d-inline-flex align-items-center gap-2" href="{{ route('mitra.accommodation.create') }}">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Penginapan Sekarang</span>
                                </a>
                            @endcan
                        </x-empty-state>
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Nama Properti</th>
                    <th>Wilayah</th>
                    <th>Tipe & Kelas</th>
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
                                <i class="fa-solid fa-bed text-secondary" style="font-size: 11px;"></i>
                                {{ $item->accommodation?->rooms?->count() ?? 0 }} Tipe Kamar
                            </small>
                        </td>
                        <td>
                            <span class="badge text-bg-light border">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                {{ $item->region?->name ?? 'Tegal' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border">
                                <i class="fa-solid fa-hotel me-1"></i>
                                {{ str($item->accommodation?->property_type ?? 'Hotel')->headline() }}
                            </span>
                            @if($item->accommodation?->star_rating)
                                <span class="badge bg-warning-subtle text-warning-emphasis border ms-1">
                                    <i class="fa-solid fa-star text-warning me-0.5"></i>
                                    {{ $item->accommodation->star_rating }}
                                </span>
                            @endif
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
                               href="{{ route('mitra.accommodation.show', $item) }}">
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
