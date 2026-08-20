@extends('layouts.admin')

@section('title', 'Master Wilayah / Kecamatan')
@section('page-title', 'Master Wilayah / Kecamatan')
@section('page-description', 'Kelola daftar wilayah dan kecamatan se-Kabupaten & Kota Tegal untuk pilihan lokasi Mitra dan Destinasi.')

@section('content')
    {{-- Header Action Toolbar & Search --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mb-4">
        <div>
            <form method="GET" action="{{ route('admin.regions.index') }}" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="max-width: 320px;">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control bg-light border-start-0 fs-8" placeholder="Cari nama kecamatan / kode...">
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.regions.index') }}" class="btn btn-sm btn-light border text-muted" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>

        <button type="button" class="btn btn-emerald btn-sm rounded-pill px-3.5 py-1.5 fw-bold text-white shadow-sm flex-shrink-0" data-bs-toggle="modal" data-bs-target="#createRegionModal" style="background: #047857;">
            <i class="fa-solid fa-plus me-1"></i> Tambah Wilayah / Kecamatan
        </button>
    </div>

    {{-- Data Table Card --}}
    <x-table-wrapper title="Daftar Master Wilayah / Kecamatan ({{ $regions->total() }} Wilayah)">
        @if ($regions->isEmpty())
            <tbody>
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Tidak Ada Wilayah Ditemukan"
                            caption="Silakan sesuaikan kata kunci pencarian atau tambahkan kecamatan baru."
                            icon="fa-solid fa-map-location-dot" />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th>Nama Wilayah / Kecamatan</th>
                    <th>Kode Sistem</th>
                    <th>Level Administratif</th>
                    <th style="width: 140px;" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($regions as $index => $reg)
                    <tr>
                        <td class="text-center text-muted fw-semibold">
                            {{ $regions->firstItem() + $index }}
                        </td>
                        <td>
                            <strong class="text-dark d-block fs-7">Kecamatan {{ $reg->name }}</strong>
                        </td>
                        <td>
                            <code class="font-monospace text-dark fs-8">{{ $reg->code ?? '-' }}</code>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fw-bold fs-8">
                                <i class="fa-solid fa-building-flag me-1"></i> {{ strtoupper($reg->level ?? 'DISTRICT') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-sm btn-light border text-dark rounded-circle" style="width: 32px; height: 32px; padding: 0;"
                                    data-bs-toggle="modal" data-bs-target="#editRegionModal{{ $reg->id }}" title="Edit Wilayah">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.regions.destroy', $reg) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus wilayah {{ $reg->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle" style="width: 32px; height: 32px; padding: 0;" title="Hapus Wilayah">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Modal Edit Wilayah --}}
                            <div class="modal fade text-start" id="editRegionModal{{ $reg->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <form action="{{ route('admin.regions.update', $reg) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-bottom p-4">
                                                <h5 class="modal-title fw-bold text-dark fs-6">
                                                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Wilayah / Kecamatan
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold fs-8 text-dark">Nama Kecamatan <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control fs-8" value="{{ old('name', $reg->name) }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold fs-8 text-dark">Kode Wilayah</label>
                                                    <input type="text" name="code" class="form-control fs-8" value="{{ old('code', $reg->code) }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                                                <button type="button" class="btn btn-light border rounded-pill px-4 fw-bold fs-8" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold fs-8">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>
            {{ $regions->links() }}
        </x-slot:pagination>
    </x-table-wrapper>

    {{-- Modal Tambah Wilayah Baru --}}
    <div class="modal fade" id="createRegionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('admin.regions.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark fs-6">
                            <i class="fa-solid fa-map-location-dot text-emerald me-2" style="color: #047857;"></i> Tambah Wilayah / Kecamatan Baru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold fs-8 text-dark">Nama Kecamatan <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control fs-8" placeholder="Contoh: Bumijawa" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold fs-8 text-dark">Kode Wilayah (Opsional)</label>
                            <input type="text" name="code" class="form-control fs-8" placeholder="Contoh: BMJ">
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-light border rounded-pill px-4 fw-bold fs-8" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-emerald rounded-pill px-4 fw-bold fs-8 text-white" style="background: #047857;">Simpan Wilayah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
