@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna Platform')
@section('page-description', 'Kelola data pengguna terdaftar, status verifikasi, hak akses peran (role), dan reset kata
    sandi.')

@section('content')
    <!-- Search & Filter Toolbar -->
    <div class='d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mb-3'>
        <div class='d-flex align-items-center gap-2 overflow-x-auto pb-1 pb-md-0'>
            <a href='{{ route('admin.users.index', array_merge(request()->except('role', 'page'), [])) }}'
                class='btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ !request('role') ? 'btn-dark' : 'btn-light border text-muted' }}'>
                Semua Pengguna
            </a>
            <a href='{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'mitra-owner'])) }}'
                class='btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('role') === 'mitra-owner' ? 'btn-primary' : 'btn-light border text-muted' }}'>
                <i class="fa-solid fa-store me-1"></i> Mitra Owner
            </a>
            <a href='{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'consumer'])) }}'
                class='btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('role') === 'consumer' ? 'btn-primary' : 'btn-light border text-muted' }}'>
                <i class="fa-solid fa-user me-1"></i> Wisatawan
            </a>
            <a href='{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'admin'])) }}'
                class='btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('role') === 'admin' ? 'btn-primary' : 'btn-light border text-muted' }}'>
                <i class="fa-solid fa-shield-halved me-1"></i> Admin
            </a>
        </div>

        <!-- Search Form -->
        <form method='GET' action='{{ route('admin.users.index') }}' class='d-flex align-items-center gap-2'>
            @if (request('role'))
                <input type='hidden' name='role' value='{{ request('role') }}'>
            @endif
            @if (request('status'))
                <input type='hidden' name='status' value='{{ request('status') }}'>
            @endif
            <div class='input-group input-group-sm'>
                <span class='input-group-text bg-light border-end-0 text-muted'>
                    <i class='fa-solid fa-magnifying-glass'></i>
                </span>
                <input type='text' name='q' value='{{ request('q') }}'
                    class='form-control bg-light border-start-0 fs-8' placeholder='Cari nama, email, telp...'>
            </div>
            @if (request('q') || request('role') || request('status'))
                <a href='{{ route('admin.users.index') }}' class='btn btn-sm btn-light border text-muted'
                    title='Reset Filter'>
                    <i class='fa-solid fa-rotate-left'></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <x-table-wrapper title='Daftar Pengguna ({{ $users->total() }} Akun)'>
        @if ($users->isEmpty())
            <tbody>
                <tr>
                    <td colspan='5'>
                        <x-empty-state title='Tidak ada pengguna ditemukan'
                            description='Silakan sesuaikan kata kunci pencarian Anda.' compact />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Nama & Kontak</th>
                    <th>Hak Akses / Peran</th>
                    <th>Status Akun</th>
                    <th>Terdaftar</th>
                    <th class='text-end'>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td data-label='Nama & Kontak'>
                            <strong class='d-block text-dark fs-7'>{{ $u->name }}</strong>
                            <small class='text-muted d-block fs-8'>
                                <i class="fa-regular fa-envelope me-1"></i>{{ $u->email }}
                                @if ($u->phone)
                                    &middot; <i class="fa-solid fa-phone ms-1 me-0.5"></i>{{ $u->phone }}
                                @endif
                            </small>
                        </td>
                        <td data-label='Peran'>
                            <div class='d-flex flex-wrap gap-1'>
                                @forelse($u->roles as $role)
                                    <span
                                        class='badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-0.5 fs-8'>
                                        {{ str($role->name)->replace('-', ' ')->headline() }}
                                    </span>
                                @empty
                                    <span class='text-muted fs-8'>—</span>
                                @endforelse
                            </div>
                        </td>
                        <td data-label='Status'>
                            <x-status-badge :status="$u->status" />
                        </td>
                        <td data-label='Terdaftar'>
                            <span class='fs-8 text-muted'>{{ $u->created_at?->format('d M Y, H:i') }}</span>
                        </td>
                        <td data-label='Aksi' class='text-end'>
                            <button type='button'
                                class='btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fs-8 fw-semibold'
                                data-bs-toggle='modal' data-bs-target='#reset-user-pw-{{ $u->id }}'
                                title='Reset Kata Sandi Pengguna'>
                                <i class='fa-solid fa-key me-1'></i>
                            </button>

                            <!-- Modal Reset Password Pengguna -->
                            <div class='modal fade text-start' id='reset-user-pw-{{ $u->id }}' tabindex='-1'
                                aria-labelledby='resetUserPwLabel{{ $u->id }}' aria-hidden='true'>
                                <div class='modal-dialog modal-dialog-centered'>
                                    <div class='modal-content border-0 shadow-lg rounded-4'>
                                        <form method='POST' action='{{ route('admin.users.reset-password', $u) }}'>
                                            @csrf
                                            <div class='modal-header border-bottom py-3 px-4' style='background: #f8fafc;'>
                                                <h6 class='modal-title fw-bold text-dark'
                                                    id='resetUserPwLabel{{ $u->id }}'>
                                                    <i class='fa-solid fa-key text-primary me-1'></i> Reset Password
                                                    Pengguna
                                                </h6>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                                    aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body p-4'>
                                                <div class='p-3 rounded-3 mb-3'
                                                    style='background: #f1f5f9; font-size: 13px;'>
                                                    <div class='mb-1'><strong>Nama:</strong> {{ $u->name }}</div>
                                                    <div><strong>Email Akun:</strong> <code>{{ $u->email }}</code>
                                                    </div>
                                                </div>

                                                <div class='mb-3'>
                                                    <div class='d-flex justify-content-between align-items-center mb-1'>
                                                        <label class='form-label fw-bold text-dark mb-0'
                                                            style='font-size: 13px;'>
                                                            Kata Sandi Baru <span class='text-danger'>*</span>
                                                        </label>
                                                        <button type='button'
                                                            class='btn btn-link btn-sm p-0 text-decoration-none'
                                                            style='font-size: 11px;'
                                                            onclick="generateUserPwd('new_user_pwd_{{ $u->id }}', 'conf_user_pwd_{{ $u->id }}')">
                                                            <i class='fa-solid fa-wand-magic-sparkles me-1'></i> Buat Acak
                                                        </button>
                                                    </div>
                                                    <input type='text' name='password'
                                                        id='new_user_pwd_{{ $u->id }}'
                                                        class='form-control font-monospace' placeholder='Minimal 8 karakter'
                                                        required>
                                                </div>

                                                <div class='mb-2'>
                                                    <label class='form-label fw-bold text-dark' style='font-size: 13px;'>
                                                        Ulangi Kata Sandi Baru <span class='text-danger'>*</span>
                                                    </label>
                                                    <input type='text' name='password_confirmation'
                                                        id='conf_user_pwd_{{ $u->id }}'
                                                        class='form-control font-monospace'
                                                        placeholder='Ulangi kata sandi baru' required>
                                                </div>
                                            </div>
                                            <div class='modal-footer border-top py-2.5 px-4' style='background: #f8fafc;'>
                                                <button type='button' class='btn btn-sm btn-secondary rounded-pill px-3'
                                                    data-bs-dismiss='modal'>Batal</button>
                                                <button type='submit'
                                                    class='btn btn-sm btn-lokantara rounded-pill px-4 fw-bold'>
                                                    <i class='fa-solid fa-floppy-disk me-1'></i> Simpan Kata Sandi
                                                </button>
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
        <x-slot:pagination>{{ $users->links() }}</x-slot:pagination>
    </x-table-wrapper>

    @push('scripts')
        <script>
            function generateUserPwd(newId, confId) {
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
