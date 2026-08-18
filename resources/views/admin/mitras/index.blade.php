@extends('layouts.admin')

@section('title', 'Manajemen Mitra')
@section('page-title', 'Manajemen Mitra')
@section('page-description', 'Kelola tenant mitra pariwisata daerah Tegal (Dinas & Non-Dinas), aktivasi operasional, dan moderasi akun.')

@section('page-actions')
    <a class='btn btn-lokantara fw-bold d-inline-flex align-items-center gap-2' href='{{ route('admin.mitras.create') }}'>
        <i class='fa-solid fa-plus-circle'></i>
        <span>Buat Mitra Baru</span>
    </a>
@endsection

@section('content')
    <!-- 1. Category & Filter Toolbar -->
    <div class='d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mb-3'>
        <!-- Category Filter Pills -->
        <div class='d-flex align-items-center gap-2 overflow-x-auto pb-1 pb-md-0'>
            <a href='{{ route('admin.mitras.index', array_merge(request()->except('category', 'page'), [])) }}'
               class='btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ !request('category') ? 'btn-dark' : 'btn-light border text-muted' }}'>
                Semua ({{ $counts['total'] ?? 0 }})
            </a>
            <a href='{{ route('admin.mitras.index', array_merge(request()->except('category', 'page'), ['category' => 'dinas'])) }}'
               class='btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('category') === 'dinas' ? 'btn-primary' : 'btn-light border text-primary' }}'>
                <i class='fa-solid fa-building-columns me-1'></i> Dinas ({{ $counts['dinas'] ?? 0 }})
            </a>
            <a href='{{ route('admin.mitras.index', array_merge(request()->except('category', 'page'), ['category' => 'non_dinas'])) }}'
               class='btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('category') === 'non_dinas' ? 'btn-dark' : 'btn-light border text-muted' }}'>
                <i class='fa-solid fa-store me-1'></i> Non-Dinas ({{ $counts['non_dinas'] ?? 0 }})
            </a>
        </div>

        <!-- Search Form -->
        <form method='GET' action='{{ route('admin.mitras.index') }}' class='d-flex align-items-center gap-2'>
            @if(request('category'))
                <input type='hidden' name='category' value='{{ request('category') }}'>
            @endif
            @if(request('status'))
                <input type='hidden' name='status' value='{{ request('status') }}'>
            @endif
            <div class='input-group input-group-sm'>
                <span class='input-group-text bg-light border-end-0 text-muted'>
                    <i class='fa-solid fa-magnifying-glass'></i>
                </span>
                <input type='text' name='q' value='{{ request('q') }}' class='form-control bg-light border-start-0 fs-8'
                       placeholder='Cari nama, legal, slug, owner...'>
            </div>
            @if(request('q') || request('category') || request('status'))
                <a href='{{ route('admin.mitras.index') }}' class='btn btn-sm btn-light border text-muted' title='Reset Filter'>
                    <i class='fa-solid fa-rotate-left'></i>
                </a>
            @endif
        </form>
    </div>

    <!-- 2. Data Table Wrapper -->
    <x-table-wrapper title='Daftar Tenant Mitra'>
        @if ($mitras->isEmpty())
            <tbody>
                <tr>
                    <td colspan='6'>
                        <x-empty-state title='Tidak ada mitra ditemukan'
                            description='Silakan sesuaikan filter pencarian Anda atau buat mitra baru melalui tombol di atas.' compact />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Nama & Kategori Mitra</th>
                    <th>Penanggung Jawab (Owner)</th>
                    <th>Wilayah</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th class='text-end'>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mitras as $mitra)
                    <tr>
                        <td data-label='Nama Mitra'>
                            <div class='d-flex align-items-center gap-2 mb-1 flex-wrap'>
                                <strong class='text-dark fs-7'>{{ $mitra->display_name }}</strong>
                                @if($mitra->category === 'dinas')
                                    <span class='badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fs-8 fw-bold'>
                                        <i class='fa-solid fa-building-columns me-1'></i> Dinas
                                    </span>
                                @else
                                    <span class='badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 fs-8'>
                                        <i class='fa-solid fa-store me-1'></i> Non-Dinas
                                    </span>
                                @endif
                            </div>
                            <small class='d-block text-muted fs-8'>
                                Legal: {{ $mitra->legal_name }} &middot; <code class='text-muted'>/{{ $mitra->slug }}</code>
                            </small>
                        </td>
                        <td data-label='Owner'>
                            <span class='text-dark fw-medium fs-8 d-block'>{{ $mitra->owner?->name ?? '—' }}</span>
                            <small class='text-muted fs-8'>{{ $mitra->owner?->email ?? '—' }}</small>
                        </td>
                        <td data-label='Lokasi'>
                            <span class='fs-8'>{{ $mitra->region?->name ?? '—' }}</span>
                        </td>
                        <td data-label='Status'>
                            <x-status-badge :status='$mitra->status' />
                        </td>
                        <td data-label='Terdaftar'>
                            <span class='fs-8 text-muted'>{{ $mitra->created_at->format('d M Y') }}</span>
                        </td>
                        <td data-label='Aksi' class='text-end'>
                            <div class='d-flex justify-content-end align-items-center gap-1.5'>
                                <!-- Edit Button -->
                                <a href='{{ route('admin.mitras.edit', $mitra) }}' class='btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1'
                                   title='Edit Informasi Mitra'>
                                    <i class='fa-solid fa-pen-to-square'></i>
                                </a>

                                <!-- Status Activation / Suspend -->
                                @if ($mitra->status !== 'active')
                                    <form method='POST' action='{{ route('admin.mitras.status', $mitra) }}'>
                                        @csrf
                                        @method('PATCH')
                                        <input type='hidden' name='status' value='active'>
                                        <button class='btn btn-sm btn-success rounded-pill px-2.5 py-1 fw-bold fs-8'>
                                            <i class='fa-solid fa-check me-1'></i> Aktifkan
                                        </button>
                                    </form>
                                @else
                                    <button class='btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fs-8'
                                            data-bs-toggle='modal' data-bs-target='#suspend-{{ $mitra->id }}'>
                                        Suspend
                                    </button>
                                @endif
                            </div>

                            <!-- Modal Suspend -->
                            <div class='modal fade text-start' id='suspend-{{ $mitra->id }}' tabindex='-1'>
                                <div class='modal-dialog'>
                                    <form class='modal-content lokantara-modal' method='POST'
                                          action='{{ route('admin.mitras.status', $mitra) }}'>
                                        @csrf
                                        @method('PATCH')
                                        <div class='modal-header'>
                                            <h2 class='modal-title fs-5 fw-bold'>Suspend Mitra: {{ $mitra->display_name }}</h2>
                                            <button class='btn-close' type='button' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            <input type='hidden' name='status' value='suspended'>
                                            <div class='alert alert-warning fs-8 py-2 px-3 mb-3'>
                                                <i class='fa-solid fa-triangle-exclamation me-1'></i>
                                                Menangguhkan mitra akan menonaktifkan visibilitas katalog dan hak akses transaksi sementara.
                                            </div>
                                            <x-textarea name='reason' label='Alasan Suspend' required placeholder='Tuliskan alasan penangguhan tenant...' />
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'>Batal</button>
                                            <button type='submit' class='btn btn-danger fw-bold'>Tangguhkan Mitra</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $mitras->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
