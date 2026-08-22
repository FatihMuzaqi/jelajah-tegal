@extends('layouts.super-admin')

@section('title', 'Manajemen Akun Administrator')
@section('page-title', 'Manajemen Akun Administrator')
@section('page-description', 'Kelola peran staf, hak akses sistem operasional, dan riwayat aktivitas login.')

@section('page-actions')
    <a href="{{ route('super-admin.admins.create') }}"
        class="btn btn-primary fw-semibold d-inline-flex align-items-center gap-1.5 px-3.5 py-2 rounded-3 shadow-xs"
        style="background: #2563eb; border: none; font-size: 13.5px;">
        <i class="fa-solid fa-plus fs-8"></i>
        <span>Tambah Akun</span>
    </a>
@endsection

@section('content')
<style>
/* Clean Administrator Page Styling with CSS Variables */
.stat-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 16px;
    padding: 20px 22px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    transition: background-color var(--transition), border-color var(--transition);
}
.stat-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: var(--lokantara-muted);
    text-transform: uppercase;
}
.stat-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
.stat-icon-blue {
    background: rgba(59, 130, 246, 0.12);
    color: #3b82f6;
}
.stat-icon-green {
    background: rgba(34, 197, 94, 0.12);
    color: #22c55e;
}
.stat-icon-gray {
    background: rgba(148, 163, 184, 0.12);
    color: #94a3b8;
}
.stat-main {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin: 8px 0 4px;
}
.stat-value {
    font-size: 26px;
    font-weight: 800;
    color: var(--lokantara-text);
    line-height: 1.1;
}
.stat-sublabel {
    font-size: 13.5px;
    color: var(--lokantara-muted);
    font-weight: 500;
}
.stat-footer {
    font-size: 12px;
    color: var(--lokantara-muted);
}

/* Main Card */
.main-card {
    background: var(--lokantara-surface);
    border: 1px solid var(--lokantara-border);
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
    transition: background-color var(--transition), border-color var(--transition);
}

/* Filter Tabs */
.filter-tabs-wrapper {
    background: var(--lokantara-background);
    border: 1px solid var(--lokantara-border);
    border-radius: 12px;
    padding: 4px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    overflow-x: auto;
    max-width: 100%;
}
.filter-tab {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--lokantara-muted);
    text-decoration: none;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.filter-tab:hover {
    color: var(--lokantara-text);
}
.filter-tab.active {
    background: var(--lokantara-surface);
    color: var(--lokantara-text);
    font-weight: 700;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
.tab-badge {
    background: var(--lokantara-border);
    color: var(--lokantara-muted);
    font-size: 11px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 6px;
}
.filter-tab.active .tab-badge {
    background: rgba(37, 99, 235, 0.12);
    color: #3b82f6;
}

/* Search Box */
.search-input-wrap {
    position: relative;
    width: 240px;
}
.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--lokantara-muted);
    font-size: 12px;
}
.search-input {
    width: 100%;
    padding: 7px 12px 7px 32px;
    font-size: 12.5px;
    border: 1px solid var(--lokantara-border);
    border-radius: 99px;
    background: var(--lokantara-surface);
    color: var(--lokantara-text);
    outline: none;
    transition: border-color 0.15s ease, background-color var(--transition);
}
.search-input:focus {
    border-color: #3b82f6;
}

/* Clean Table */
.clean-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 10px;
}
.clean-table th {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: var(--lokantara-muted);
    padding: 12px 14px;
    border-bottom: 1px solid var(--lokantara-border);
    white-space: nowrap;
}
.clean-table td {
    padding: 14px;
    border-bottom: 1px solid var(--lokantara-border);
    vertical-align: middle;
    color: var(--lokantara-text);
}
.clean-table tr:hover td {
    background-color: rgba(255, 255, 255, 0.03);
}
[data-theme=light] .clean-table tr:hover td {
    background-color: #fafbfd;
}

/* Staff Avatar */
.staff-avatar {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 50%;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.staff-name {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--lokantara-text);
    line-height: 1.3;
}
.staff-email {
    font-size: 12px;
    color: var(--lokantara-muted);
}
.badge-self {
    background: #2563eb;
    color: #ffffff;
    font-size: 9.5px;
    padding: 1px 6px;
    border-radius: 99px;
    font-weight: 700;
    margin-left: 4px;
    vertical-align: middle;
}

/* Role Badges */
.role-badge-super {
    background: rgba(217, 119, 6, 0.15);
    color: #f59e0b;
    font-size: 11.5px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-block;
}
.role-badge-admin {
    background: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-block;
}
.role-badge-dinas {
    background: rgba(8, 145, 178, 0.15);
    color: #22d3ee;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-block;
}

/* Status Indicator */
.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
}
.status-active {
    color: #22c55e;
}
.status-suspended {
    color: #ef4444;
}
.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.dot-green {
    background: #22c55e;
}
.dot-red {
    background: #ef4444;
}

/* Date format */
.date-main {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--lokantara-text);
}
.date-sub {
    font-size: 11.5px;
    color: var(--lokantara-muted);
}
.login-text {
    font-size: 12.5px;
    color: var(--lokantara-text);
}

/* Action Buttons */
.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid var(--lokantara-border);
    background: var(--lokantara-surface);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: var(--lokantara-muted);
    transition: all 0.15s ease;
    cursor: pointer;
    text-decoration: none;
    padding: 0;
}
.btn-action:hover {
    background: var(--lokantara-background);
    border-color: #3b82f6;
    color: var(--lokantara-text);
}

/* Footer */
.table-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 18px;
    margin-top: 8px;
    border-top: 1px solid var(--lokantara-border);
    flex-wrap: wrap;
    gap: 12px;
}
.footer-info {
    font-size: 12px;
    color: var(--lokantara-muted);
}

/* Modal Dark Mode Overrides */
[data-theme=dark] .modal-content {
    background-color: var(--lokantara-surface) !important;
    border: 1px solid var(--lokantara-border) !important;
    color: var(--lokantara-text) !important;
}
[data-theme=dark] .modal-header,
[data-theme=dark] .modal-footer {
    background-color: var(--lokantara-surface) !important;
    border-color: var(--lokantara-border) !important;
}
[data-theme=dark] .modal-title {
    color: var(--lokantara-text) !important;
}
[data-theme=dark] .modal-body {
    background-color: var(--lokantara-surface) !important;
}
[data-theme=dark] .modal-body .p-3.rounded-3 {
    background-color: var(--lokantara-background) !important;
    color: var(--lokantara-text) !important;
    border: 1px solid var(--lokantara-border);
}
[data-theme=dark] .modal-body .form-label {
    color: var(--lokantara-text) !important;
}
[data-theme=dark] .modal-body .form-control {
    background-color: var(--lokantara-background) !important;
    border-color: var(--lokantara-border) !important;
    color: var(--lokantara-text) !important;
}
[data-theme=dark] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
</style>

<!-- 1. KPI Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Total Akun -->
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">TOTAL AKUN</span>
                <div class="stat-icon stat-icon-blue">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="stat-main">
                <span class="stat-value">{{ $counts['total'] }}</span>
                <span class="stat-sublabel">Akun Terdaftar</span>
            </div>
            <div class="stat-footer">
                {{ $counts['super_admins'] }} Super Admin &bull; {{ $counts['admins'] }} Administrator
            </div>
        </div>
    </div>

    <!-- Staf Aktif -->
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">STAF AKTIF</span>
                <div class="stat-icon stat-icon-green">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>
            <div class="stat-main">
                <span class="stat-value">{{ $counts['active'] }}</span>
                <span class="stat-sublabel">Siap Bertugas</span>
            </div>
            <div class="stat-footer">
                Akses login operasional aktif
            </div>
        </div>
    </div>

    <!-- Nonaktif / Cuti -->
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">NONAKTIF / CUTI</span>
                <div class="stat-icon stat-icon-gray">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>
            <div class="stat-main">
                <span class="stat-value">{{ $counts['suspended'] }}</span>
                <span class="stat-sublabel">Ditangguhkan</span>
            </div>
            <div class="stat-footer">
                {{ $counts['suspended'] > 0 ? $counts['suspended'] . ' akun dinonaktifkan' : 'Tidak ada akun dibekukan' }}
            </div>
        </div>
    </div>
</div>

<!-- 2. Main Card (Filter Bar + Table) -->
<div class="main-card">
    <!-- Filter & Search Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mb-3">
        <!-- Segmented Tabs -->
        <div class="filter-tabs-wrapper">
            <a href="{{ route('super-admin.admins.index', request()->only('q')) }}" 
               class="filter-tab {{ !request('status') && !request('role') ? 'active' : '' }}">
                Semua <span class="tab-badge">{{ $counts['total'] }}</span>
            </a>
            <a href="{{ route('super-admin.admins.index', array_merge(request()->only('q'), ['status' => 'active'])) }}" 
               class="filter-tab {{ request('status') === 'active' ? 'active' : '' }}">
                Aktif <span class="tab-badge">{{ $counts['active'] }}</span>
            </a>
            <a href="{{ route('super-admin.admins.index', array_merge(request()->only('q'), ['role' => 'admin'])) }}" 
               class="filter-tab {{ request('role') === 'admin' ? 'active' : '' }}">
                Administrator <span class="tab-badge">{{ $counts['admins'] }}</span>
            </a>
            <a href="{{ route('super-admin.admins.index', array_merge(request()->only('q'), ['role' => 'super-admin'])) }}" 
               class="filter-tab {{ request('role') === 'super-admin' ? 'active' : '' }}">
                Super Admin <span class="tab-badge">{{ $counts['super_admins'] }}</span>
            </a>
        </div>

        <!-- Search Box -->
        <form method="GET" action="{{ route('super-admin.admins.index') }}" class="m-0">
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            @if(request('role')) <input type="hidden" name="role" value="{{ request('role') }}"> @endif
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="q" value="{{ request('q') }}" class="search-input" placeholder="Cari nama, email...">
            </div>
        </form>
    </div>

    <!-- Clean Table -->
    <div class="table-responsive">
        <table class="clean-table">
            <thead>
                <tr>
                    <th>NAMA STAF & KONTAK</th>
                    <th>PERAN / AKSES</th>
                    <th>STATUS</th>
                    <th>TANGGAL TERDAFTAR</th>
                    <th>TERAKHIR LOGIN</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $palette = ['#d97706', '#2563eb', '#7c3aed', '#0d9488', '#dc2626', '#4f46e5', '#059669'];
                @endphp
                @forelse($admins as $index => $admin)
                    @php
                        $isSelf = $admin->id === auth()->id();
                        $bgColor = $palette[$index % count($palette)];
                        
                        // Extract initials (2 letters)
                        $cleanName = preg_replace('/\(.*?\)/', '', $admin->name);
                        $parts = array_filter(explode(' ', trim($cleanName)));
                        if (count($parts) >= 2) {
                            $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
                        } else {
                            $initials = strtoupper(substr($admin->name, 0, 2));
                        }
                        
                        // Last login formatting
                        if ($admin->last_login_at) {
                            if ($admin->last_login_at->isToday()) {
                                $loginStr = 'Hari ini, ' . $admin->last_login_at->format('H:i');
                            } elseif ($admin->last_login_at->isYesterday()) {
                                $loginStr = 'Kemarin, ' . $admin->last_login_at->format('H:i');
                            } else {
                                $loginStr = $admin->last_login_at->diffForHumans();
                            }
                        } else {
                            $loginStr = 'Belum login';
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="staff-avatar" style="background-color: {{ $bgColor }};">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <div class="staff-name">
                                        {{ $admin->name }}
                                        @if($isSelf)
                                            <span class="badge-self">Anda</span>
                                        @endif
                                    </div>
                                    <div class="staff-email">{{ $admin->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($admin->hasRole('super-admin'))
                                <span class="role-badge-super">
                                    ★ Super Admin
                                </span>
                            @elseif($admin->hasRole('admin'))
                                <span class="role-badge-admin">
                                    Administrator
                                </span>
                            @elseif($admin->hasRole('dinas-supervisor'))
                                <span class="role-badge-dinas">
                                    Dinas Supervisor
                                </span>
                            @else
                                <span class="role-badge-admin">
                                    {{ $admin->getRoleNames()->first() ?? 'Staff' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($admin->status === 'active')
                                <span class="status-indicator status-active">
                                    <span class="status-dot dot-green"></span> Aktif
                                </span>
                            @else
                                <span class="status-indicator status-suspended">
                                    <span class="status-dot dot-red"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="date-main">{{ $admin->created_at?->translatedFormat('d M Y') }}</div>
                            <div class="date-sub">{{ $admin->created_at?->format('H:i') }} WIB</div>
                        </td>
                        <td>
                            <div class="login-text {{ $admin->last_login_at ? 'text-dark' : 'text-muted' }}">{{ $loginStr }}</div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1.5">
                                <!-- Key / Reset Password -->
                                <button type="button" class="btn-action" data-bs-toggle="modal" data-bs-target="#reset-admin-pw-{{ $admin->id }}" title="Reset Password">
                                    <i class="fa-solid fa-key text-warning"></i>
                                </button>
                                
                                <!-- Edit -->
                                <a href="{{ route('super-admin.admins.edit', $admin) }}" class="btn-action" title="Edit Data">
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </a>

                                <!-- Toggle / Suspend -->
                                @if(!$isSelf)
                                    <form method="POST" action="{{ route('super-admin.admins.toggle-status', $admin) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin {{ $admin->status === 'active' ? 'menonaktifkan' : 'mengaktifkan' }} akun {{ $admin->name }}?');">
                                        @csrf
                                        @method('PATCH')
                                        @if($admin->status === 'active')
                                            <button type="submit" class="btn-action" title="Nonaktifkan Akun">
                                                <i class="fa-solid fa-ban text-danger"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn-action" title="Aktifkan Akun">
                                                <i class="fa-solid fa-check text-success"></i>
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </div>

                            <!-- Modal Reset Password Admin -->
                            <div class="modal fade text-start" id="reset-admin-pw-{{ $admin->id }}"
                                tabindex="-1" aria-labelledby="resetAdminPwLabel{{ $admin->id }}"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <form method="POST" action="{{ route('super-admin.admins.reset-password', $admin) }}">
                                            @csrf
                                            <div class="modal-header border-bottom py-3 px-4">
                                                <h6 class="modal-title fw-bold text-dark" id="resetAdminPwLabel{{ $admin->id }}">
                                                    <i class="fa-solid fa-key text-primary me-1"></i> Reset Password Administrator
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="p-3 rounded-3 mb-3" style="font-size: 13px;">
                                                    <div class="mb-1"><strong>Nama Admin:</strong> {{ $admin->name }}</div>
                                                    <div><strong>Email Login:</strong> <code>{{ $admin->email }}</code></div>
                                                    <div class="mt-1"><span class="badge bg-primary-subtle text-primary">{{ $admin->getRoleNames()->first() ?? 'Admin' }}</span></div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label fw-bold text-dark mb-0" style="font-size: 13px;">
                                                            Kata Sandi Baru <span class="text-danger">*</span>
                                                        </label>
                                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 11px;" onclick="generateAdminPwd('new_admin_pwd_{{ $admin->id }}', 'conf_admin_pwd_{{ $admin->id }}')">
                                                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Buat Acak
                                                        </button>
                                                    </div>
                                                    <input type="text" name="password" id="new_admin_pwd_{{ $admin->id }}" class="form-control font-monospace" placeholder="Minimal 8 karakter" required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label fw-bold text-dark" style="font-size: 13px;">
                                                        Ulangi Kata Sandi Baru <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="password_confirmation" id="conf_admin_pwd_{{ $admin->id }}" class="form-control font-monospace" placeholder="Ulangi kata sandi baru" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top py-2.5 px-4">
                                                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold" style="background: #2563eb;">
                                                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Kata Sandi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-5 text-center text-muted fs-8">
                            <p class="mb-0 fw-semibold">Tidak ada akun administrator yang cocok dengan filter saat ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="table-footer">
        <div class="footer-info">
            Menampilkan {{ $admins->firstItem() ?? 0 }} - {{ $admins->lastItem() ?? 0 }} dari {{ $admins->total() }} data staf
        </div>
        <div class="footer-pagination">
            {{ $admins->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function generateAdminPwd(newId, confId) {
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
