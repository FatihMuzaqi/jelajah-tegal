@extends('layouts.super-admin')

@section('title', 'Manajemen Role')
@section('page-title', 'Role & Permission Matrix')
@section('page-description', 'Daftar peran sistem dan jumlah izin (permission) yang dialokasikan.')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div>
            <h2>Daftar Peran (Roles)</h2>
            <p>Peran sistem terautentikasi via Spatie RBAC.</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="lokantara-table">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Guard Name</th>
                        <th>Jumlah Permission</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td data-label="Role Name">
                                <strong>{{ $role->name }}</strong>
                            </td>
                            <td data-label="Guard Name"><code>{{ $role->guard_name }}</code></td>
                            <td data-label="Jumlah Permission">
                                <span class="status-badge status-success">{{ $role->permissions_count }} Permissions</span>
                            </td>
                            <td data-label="Aksi">
                                <span class="text-muted fs-8">Spatie Guard Active</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state title="Belum ada role" description="Role database belum terisi." compact />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
