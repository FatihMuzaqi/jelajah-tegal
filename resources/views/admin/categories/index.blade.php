@extends('layouts.admin')

@section('title', 'Master Kategori Layanan')
@section('page-title', 'Master Kategori Layanan')
@section('page-description', 'Kelola daftar kategori per sektor layanan (Wisata, Penginapan, Kuliner, Event, Rental) untuk opsi di Mitra.')

@section('content')
    {{-- Header Action Toolbar & Search --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mb-4">
        {{-- Filter Service Types --}}
        <div class="d-flex align-items-center gap-2 overflow-x-auto pb-1 pb-md-0">
            <a href="{{ route('admin.categories.index', array_merge(request()->except('service_type_id', 'page'), [])) }}"
                class="btn btn-sm rounded-pill px-3.5 py-1.5 fw-bold fs-8 {{ !request('service_type_id') ? 'btn-dark' : 'btn-light border text-muted' }}">
                Semua Layanan
            </a>
            @foreach ($serviceTypes as $st)
                <a href="{{ route('admin.categories.index', array_merge(request()->except('service_type_id', 'page'), ['service_type_id' => $st->id])) }}"
                    class="btn btn-sm rounded-pill px-3.5 py-1.5 fw-bold fs-8 {{ request('service_type_id') == $st->id ? 'btn-primary' : 'btn-light border text-muted' }}">
                    {{ $st->name }}
                </a>
            @endforeach
        </div>

        {{-- Add & Search Buttons --}}
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="d-flex align-items-center gap-2">
                @if (request('service_type_id'))
                    <input type="hidden" name="service_type_id" value="{{ request('service_type_id') }}">
                @endif
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control bg-light border-start-0 fs-8" placeholder="Cari nama kategori...">
                </div>
            </form>

            <button type="button" class="btn btn-emerald btn-sm rounded-pill px-3.5 py-1.5 fw-bold text-white shadow-sm flex-shrink-0" data-bs-toggle="modal" data-bs-target="#createCategoryModal" style="background: #047857;">
                <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
            </button>
        </div>
    </div>

    {{-- Data Table Card --}}
    <x-table-wrapper title="Daftar Master Kategori ({{ $categories->total() }} Item)">
        @if ($categories->isEmpty())
            <tbody>
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Tidak Ada Kategori Ditemukan"
                            caption="Silakan sesuaikan filter atau tambahkan kategori layanan baru."
                            icon="fa-solid fa-tags" />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th>Nama Kategori</th>
                    <th>Sektor Layanan</th>
                    <th>Slug System</th>
                    <th>Status</th>
                    <th style="width: 140px;" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $index => $cat)
                    <tr>
                        <td class="text-center text-muted fw-semibold">
                            {{ $categories->firstItem() + $index }}
                        </td>
                        <td>
                            <strong class="text-dark d-block fs-7">{{ $cat->name }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold fs-8">
                                {{ $cat->serviceType->name ?? 'Semua Layanan' }}
                            </span>
                        </td>
                        <td>
                            <code class="font-monospace text-dark fs-8">{{ $cat->slug }}</code>
                        </td>
                        <td>
                            @if ($cat->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold fs-8">
                                    <i class="fa-solid fa-circle-check me-1"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fw-bold fs-8">
                                    <i class="fa-solid fa-circle-xmark me-1"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-sm btn-light border text-dark rounded-circle" style="width: 32px; height: 32px; padding: 0;"
                                    data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $cat->id }}" title="Edit Kategori">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $cat->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle" style="width: 32px; height: 32px; padding: 0;" title="Hapus Kategori">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Modal Edit Kategori --}}
                            <div class="modal fade text-start" id="editCategoryModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <form action="{{ route('admin.categories.update', $cat) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-bottom p-4">
                                                <h5 class="modal-title fw-bold text-dark fs-6">
                                                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Master Kategori
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold fs-8 text-dark">Sektor Layanan <span class="text-danger">*</span></label>
                                                    <select name="service_type_id" class="form-select fs-8" required>
                                                        @foreach ($serviceTypes as $st)
                                                            <option value="{{ $st->id }}" @selected($cat->service_type_id == $st->id)>
                                                                {{ $st->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold fs-8 text-dark">Nama Kategori <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control fs-8" value="{{ old('name', $cat->name) }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold fs-8 text-dark">Slug URL <span class="text-danger">*</span></label>
                                                    <input type="text" name="slug" class="form-control fs-8" value="{{ old('slug', $cat->slug) }}" required>
                                                </div>

                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCategoryCheck{{ $cat->id }}" @checked($cat->is_active)>
                                                    <label class="form-check-label fw-semibold fs-8 text-dark" for="activeCategoryCheck{{ $cat->id }}">
                                                        Status Kategori Aktif (Dapat dipilih Mitra)
                                                    </label>
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
            {{ $categories->links() }}
        </x-slot:pagination>
    </x-table-wrapper>

    {{-- Modal Tambah Kategori Baru --}}
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark fs-6">
                            <i class="fa-solid fa-tags text-emerald me-2" style="color: #047857;"></i> Tambah Master Kategori Layanan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold fs-8 text-dark">Sektor Layanan <span class="text-danger">*</span></label>
                            <select name="service_type_id" class="form-select fs-8" required>
                                <option value="">-- Pilih Sektor Layanan --</option>
                                @foreach ($serviceTypes as $st)
                                    <option value="{{ $st->id }}" @selected(request('service_type_id') == $st->id)>
                                        {{ $st->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold fs-8 text-dark">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control fs-8" placeholder="Contoh: Wisata Edukasi & Agrowisata" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold fs-8 text-dark">Slug URL (Opsional)</label>
                            <input type="text" name="slug" class="form-control fs-8" placeholder="Otomatis dari nama jika dikosongkan">
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="createCategoryActiveCheck" checked>
                            <label class="form-check-label fw-semibold fs-8 text-dark" for="createCategoryActiveCheck">
                                Status Kategori Aktif
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-light border rounded-pill px-4 fw-bold fs-8" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-emerald rounded-pill px-4 fw-bold fs-8 text-white" style="background: #047857;">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
