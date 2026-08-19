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

                                <!-- Reset Password Button -->
                                <button type='button' class='btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fs-8'
                                        data-bs-toggle='modal' data-bs-target='#reset-pw-mitra-{{ $mitra->id }}'
                                        title='Reset Kata Sandi Akun Owner'>
                                    <i class='fa-solid fa-key me-1'></i> Reset Password
                                </button>

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

                            <!-- Modal Reset Password Owner -->
                            <div class='modal fade text-start' id='reset-pw-mitra-{{ $mitra->id }}' tabindex='-1' aria-labelledby='resetPwMitraLabel{{ $mitra->id }}' aria-hidden='true'>
                                <div class='modal-dialog modal-dialog-centered'>
                                    <div class='modal-content border-0 shadow-lg rounded-4'>
                                        <form method='POST' action='{{ route('admin.mitras.reset-owner-password', $mitra) }}'>
                                            @csrf
                                            <div class='modal-header border-bottom py-3 px-4' style='background: #f8fafc;'>
                                                <h6 class='modal-title fw-bold text-dark' id='resetPwMitraLabel{{ $mitra->id }}'>
                                                    <i class='fa-solid fa-key text-primary me-1'></i> Reset Password Owner Mitra
                                                </h6>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body p-4'>
                                                <div class='p-3 rounded-3 mb-3' style='background: #f1f5f9; font-size: 13px;'>
                                                    <div class='mb-1'><strong>Mitra:</strong> {{ $mitra->display_name }} ({{ $mitra->category_label }})</div>
                                                    <div><strong>Akun Owner:</strong> {{ $mitra->owner?->name ?? 'Belum ada' }} &middot; <code>{{ $mitra->owner?->email ?? '-' }}</code></div>
                                                </div>

                                                <div class='mb-3'>
                                                    <div class='d-flex justify-content-between align-items-center mb-1'>
                                                        <label class='form-label fw-bold text-dark mb-0' style='font-size: 13px;'>
                                                            Kata Sandi Baru <span class='text-danger'>*</span>
                                                        </label>
                                                        <button type='button' class='btn btn-link btn-sm p-0 text-decoration-none' style='font-size: 11px;'
                                                                onclick="generateAdminMitraPassword('new_mitra_pwd_{{ $mitra->id }}', 'conf_mitra_pwd_{{ $mitra->id }}')">
                                                            <i class='fa-solid fa-wand-magic-sparkles me-1'></i> Buat Password Acak
                                                        </button>
                                                    </div>
                                                    <input type='text' name='password' id='new_mitra_pwd_{{ $mitra->id }}' class='form-control font-monospace' placeholder='Minimal 8 karakter' required>
                                                </div>

                                                <div class='mb-2'>
                                                    <label class='form-label fw-bold text-dark' style='font-size: 13px;'>
                                                        Ulangi Kata Sandi Baru <span class='text-danger'>*</span>
                                                    </label>
                                                    <input type='text' name='password_confirmation' id='conf_mitra_pwd_{{ $mitra->id }}' class='form-control font-monospace' placeholder='Ulangi kata sandi baru' required>
                                                </div>
                                            </div>
                                            <div class='modal-footer border-top py-2.5 px-4' style='background: #f8fafc;'>
                                                <button type='button' class='btn btn-sm btn-secondary rounded-pill px-3' data-bs-dismiss='modal'>Batal</button>
                                                <button type='submit' class='btn btn-sm btn-lokantara rounded-pill px-4 fw-bold'>
                                                    <i class='fa-solid fa-floppy-disk me-1'></i> Simpan Kata Sandi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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

    @push('scripts')
    <script>
        function generateAdminMitraPassword(newId, confId) {
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%';
            let pwd = '';
            for (let i = 0; i < 10; i++) {
                pwd += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById(newId).value = pwd;
            document.getElementById(confId).value = pwd;
        }
    </script>
    @endpush
@endsection
