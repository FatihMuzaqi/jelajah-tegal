@extends('layouts.super-admin')

@section('title', 'Daftar Permission')
@section('page-title', 'Katalog Permission System')
@section('page-description', 'Seluruh hak akses granular yang terdaftar dalam platform.')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div>
            <h2>Daftar Permission (Hak Akses)</h2>
            <p>Total {{ count($permissions) }} permission terdaftar.</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="lokantara-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Permission Key</th>
                        <th>Guard</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $index => $perm)
                        <tr>
                            <td data-label="#">{{ $index + 1 }}</td>
                            <td data-label="Permission Key"><code>{{ $perm->name }}</code></td>
                            <td data-label="Guard">{{ $perm->guard_name }}</td>
                            <td data-label="Status">
                                <span class="status-badge status-success">Aktif</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state title="Belum ada permission" description="Permission belum di-seed." compact />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
