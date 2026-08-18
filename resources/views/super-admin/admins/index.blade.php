@extends('layouts.super-admin')

@section('title', 'Manajemen Administrator')
@section('page-title', 'Kelola Tim Administrator')
@section('page-description', 'Pusat pendaftaran, pemantauan status, dan tata kelola akun staf administrator platform Jelajah Tegal.')

@section('page-actions')
    <a href="{{ route('super-admin.admins.create') }}" class="btn btn-lokantara fw-bold d-inline-flex align-items-center gap-2 shadow-sm px-3.5 py-2">
        <i class="fa-solid fa-user-plus"></i>
        <span>Tambah Admin Baru</span>
    </a>
@endsection

@section('content')
    <!-- 1. KPI Stat Cards -->
    <div class="superadmin-grid">
        <!-- Total Tim Admin -->
        <div class="superadmin-card">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="superadmin-badge bg-primary-subtle text-primary border border-primary-subtle">
                        <i class="fa-solid fa-users"></i> Total Tim Admin
                    </span>
                    <div class="superadmin-icon-box bg-primary-subtle text-primary border border-primary-subtle">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold text-dark mb-0 fs-3" style="letter-spacing: -0.5px;">
                    {{ $counts['total'] }} <span class="fs-7 fw-normal text-muted">Akun Terdaftar</span>
                </h3>
            </div>
            <div class="mt-3 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                <small class="text-muted fs-8">Termasuk Super Admin & Staf</small>
                <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 fs-8">Platform</span>
            </div>
        </div>

        <!-- Admin Aktif -->
        <div class="superadmin-card">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="superadmin-badge bg-success-subtle text-success border border-success-subtle">
                        <i class="fa-solid fa-circle-check"></i> Siap Bertugas
                    </span>
                    <div class="superadmin-icon-box bg-success-subtle text-success border border-success-subtle">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold text-success mb-0 fs-3" style="letter-spacing: -0.5px;">
                    {{ $counts['active'] }} <span class="fs-7 fw-normal text-muted">Staf Aktif</span>
                </h3>
            </div>
            <div class="mt-3 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                <small class="text-muted fs-8">Akses login & operasional aktif</small>
                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fs-8">Online Ready</span>
            </div>
        </div>

        <!-- Admin Suspended -->
        <div class="superadmin-card">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="superadmin-badge bg-danger-subtle text-danger border border-danger-subtle">
                        <i class="fa-solid fa-user-slash"></i> Akses Ditangguhkan
                    </span>
                    <div class="superadmin-icon-box bg-danger-subtle text-danger border border-danger-subtle">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold text-danger mb-0 fs-3" style="letter-spacing: -0.5px;">
                    {{ $counts['suspended'] }} <span class="fs-7 fw-normal text-muted">Akun Nonaktif</span>
                </h3>
            </div>
            <div class="mt-3 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                <small class="text-muted fs-8">Staf cuti / nonaktif</small>
                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5 fs-8">Suspended</span>
            </div>
        </div>
    </div>

    <!-- 2. Filter & Search Panel -->
    <div class="superadmin-panel mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-stretch align-items-lg-center gap-3">
            <!-- Filter Pills / Tabs -->
            <div class="d-flex align-items-center gap-2 overflow-x-auto pb-1 pb-lg-0">
                <a href="{{ route('super-admin.admins.index', array_merge(request()->except('status', 'role', 'page'), [])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ !request('status') && !request('role') ? 'btn-dark' : 'btn-light border text-muted' }}">
                    Semua ({{ $counts['total'] }})
                </a>
                <a href="{{ route('super-admin.admins.index', array_merge(request()->except('status', 'role', 'page'), ['status' => 'active'])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('status') === 'active' ? 'btn-success text-white' : 'btn-light border text-success' }}">
                    <i class="fa-solid fa-circle-check me-1"></i> Aktif ({{ $counts['active'] }})
                </a>
                <a href="{{ route('super-admin.admins.index', array_merge(request()->except('status', 'role', 'page'), ['role' => 'admin'])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('role') === 'admin' ? 'btn-primary' : 'btn-light border text-primary' }}">
                    <i class="fa-solid fa-shield-halved me-1"></i> Administrator ({{ $counts['admins'] }})
                </a>
                <a href="{{ route('super-admin.admins.index', array_merge(request()->except('status', 'role', 'page'), ['role' => 'super-admin'])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-bold fs-8 {{ request('role') === 'super-admin' ? 'btn-danger' : 'btn-light border text-danger' }}">
                    <i class="fa-solid fa-crown me-1"></i> Super Admin ({{ $counts['super_admins'] }})
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('super-admin.admins.index') }}" class="d-flex align-items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0 fs-8 py-2"
                           placeholder="Cari admin1, nama staf, email...">
                </div>
                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3 py-2 fs-8 rounded-3">
                    Cari
                </button>
                @if(request()->hasAny(['q', 'status', 'role']))
                    <a href="{{ route('super-admin.admins.index') }}" class="btn btn-sm btn-light border text-muted px-2.5 py-2 rounded-3" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- 3. Admin Accounts Table Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="superadmin-icon-box bg-primary-subtle text-primary" style="width: 36px; height: 36px; min-width: 36px; font-size: 15px;">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Akun Administrator Platform</h6>
                        <small class="text-muted fs-8">Seluruh staf admin memiliki akses penuh ke menu operasional platform</small>
                    </div>
                </div>
                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fs-8">
                    Total: <strong class="text-dark">{{ $admins->total() }} Akun</strong>
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table superadmin-table align-middle mb-0 fs-7">
                <thead>
                    <tr>
                        <th class="ps-3">Nama Staf & Kontak</th>
                        <th>Peran / Hak Akses</th>
                        <th class="text-center">Status Akun</th>
                        <th>Tanggal Terdaftar</th>
                        <th>Terakhir Login</th>
                        <th class="pe-3 text-end">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        @php($isSelf = $admin->id === auth()->id())
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-2xs"
                                         style="width: 38px; height: 38px; min-width: 38px; font-size: 14px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #ffffff;">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1.5 mb-0.5">
                                            <strong class="text-dark fs-7">{{ $admin->name }}</strong>
                                            @if($isSelf)
                                                <span class="badge bg-dark text-white rounded-pill px-2 py-0.5 fs-9 fw-bold">Anda</span>
                                            @endif
                                        </div>
                                        <small class="text-muted fs-8 font-monospace">{{ $admin->email }}</small>
                                        @if($admin->phone)
                                            <small class="text-muted fs-8 d-block">&middot; {{ $admin->phone }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($admin->hasRole('super-admin'))
                                    <span class="superadmin-badge bg-danger-subtle text-danger border border-danger-subtle fs-8 fw-bold">
                                        <i class="fa-solid fa-crown"></i> Super Admin
                                    </span>
                                @elseif($admin->hasRole('admin'))
                                    <span class="superadmin-badge bg-primary-subtle text-primary border border-primary-subtle fs-8 fw-bold">
                                        <i class="fa-solid fa-shield-halved"></i> Administrator
                                    </span>
                                @elseif($admin->hasRole('dinas-supervisor'))
                                    <span class="superadmin-badge bg-info-subtle text-info-emphasis border border-info-subtle fs-8 fw-bold">
                                        <i class="fa-solid fa-building-columns"></i> Dinas Supervisor
                                    </span>
                                @else
                                    <span class="superadmin-badge bg-secondary-subtle text-secondary fs-8">
                                        {{ $admin->getRoleNames()->first() ?? 'User' }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($admin->status === 'active')
                                    <span class="superadmin-badge bg-success-subtle text-success border border-success-subtle fs-8 fw-bold">
                                        <i class="fa-solid fa-circle" style="font-size: 8px;"></i> Aktif
                                    </span>
                                @elseif($admin->status === 'suspended')
                                    <span class="superadmin-badge bg-danger-subtle text-danger border border-danger-subtle fs-8 fw-bold">
                                        <i class="fa-solid fa-ban"></i> Suspended
                                    </span>
                                @else
                                    <span class="superadmin-badge bg-warning-subtle text-warning border border-warning-subtle fs-8 fw-bold">
                                        {{ strtoupper($admin->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-dark d-block fs-8">{{ $admin->created_at?->format('d M Y') }}</span>
                                <small class="text-muted fs-8">{{ $admin->created_at?->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                @if($admin->last_login_at)
                                    <span class="text-dark d-block fs-8">{{ $admin->last_login_at->format('d M Y') }}</span>
                                    <small class="text-muted fs-8">{{ $admin->last_login_at->format('H:i') }} WIB</small>
                                @else
                                    <span class="text-muted fs-8">Belum pernah login</span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <div class="d-flex justify-content-end align-items-center gap-1.5">
                                    <!-- Edit & Reset Password Button -->
                                    <a href="{{ route('super-admin.admins.edit', $admin) }}"
                                       class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fs-8 fw-semibold d-inline-flex align-items-center gap-1.5"
                                       title="Edit Profil & Reset Password">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </a>

                                    <!-- Toggle Status Button (Exclude Self) -->
                                    @if(!$isSelf)
                                        <form method="POST" action="{{ route('super-admin.admins.toggle-status', $admin) }}" class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin {{ $admin->status === 'active' ? 'menonaktifkan (suspend)' : 'mengaktifkan kembali' }} akun {{ $admin->name }}?');">
                                            @csrf
                                            @method('PATCH')
                                            @if($admin->status === 'active')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fs-8" title="Suspend Akun">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 fs-8 fw-bold" title="Aktifkan Akun">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted fs-8">
                                <div class="superadmin-icon-box bg-light text-muted mx-auto mb-2" style="width: 48px; height: 48px; font-size: 20px;">
                                    <i class="fa-solid fa-users-gear"></i>
                                </div>
                                <p class="mb-0 fw-semibold">Tidak ada akun administrator yang cocok dengan filter saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2 bg-light bg-opacity-50">
            <small class="text-muted fs-8">
                Menampilkan {{ $admins->firstItem() ?? 0 }} - {{ $admins->lastItem() ?? 0 }} dari total {{ $admins->total() }} akun administrator
            </small>
            <div>
                {{ $admins->links() }}
            </div>
        </div>
    </div>

    <!-- 4. Information Notice Card -->
    <div class="alert alert-light border rounded-4 p-3.5 mt-4 d-flex align-items-start gap-3 bg-white shadow-2xs">
        <div class="superadmin-icon-box bg-primary-subtle text-primary mt-0.5" style="width: 36px; height: 36px; min-width: 36px; font-size: 15px;">
            <i class="fa-solid fa-shield-heart"></i>
        </div>
        <div class="fs-8 text-muted">
            <strong class="text-dark d-block mb-0.5 fs-7">Tata Kelola & Akuntabilitas Multi-Admin:</strong>
            Setiap akun administrator yang dibuat memiliki hak akses penuh ke menu moderasi katalog, verifikasi KYC, persetujuan penarikan saldo mitra, dan pengelolaan voucher. Seluruh tindakan operasional staf akan otomatis dicatat di Audit Log Keamanan dengan identitas masing-masing staf.
        </div>
    </div>
@endsection
