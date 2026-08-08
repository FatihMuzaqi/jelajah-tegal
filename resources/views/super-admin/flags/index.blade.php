@extends('layouts.super-admin')

@section('title', 'Feature Flags')
@section('page-title', 'Kelola Feature Flags')
@section('page-description', 'Kontrol saklar fitur global platform (enabled / disabled).')

@section('content')
@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="content-card">
    <div class="card-header">
        <div>
            <h2>Status Feature Flags System</h2>
            <p>Fitur fail-closed proteksi transaksi dan eksperimen.</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="lokantara-table">
                <thead>
                    <tr>
                        <th>Feature Key</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi Switch</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flags as $flag)
                        <tr>
                            <td data-label="Feature Key"><code>{{ $flag->key }}</code></td>
                            <td data-label="Deskripsi">{{ $flag->description ?? 'Tidak ada deskripsi' }}</td>
                            <td data-label="Status">
                                <span class="status-badge {{ $flag->status === 'enabled' ? 'status-success' : 'status-warning' }}">
                                    {{ strtoupper($flag->status) }}
                                </span>
                            </td>
                            <td data-label="Aksi Switch">
                                <form method="POST" action="{{ route('super-admin.flags.toggle', $flag->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $flag->status === 'enabled' ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $flag->status === 'enabled' ? 'Matikan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state title="Belum ada Feature Flag" description="Tabel feature_flags kosong." compact />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
