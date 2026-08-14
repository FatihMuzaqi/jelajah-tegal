@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Daftar Pengguna System')
@section('page-description', 'Kelola daftar pengguna terdaftar, peran, dan verifikasi email.')

@section('content')
    <div class="content-card">
        <div class="card-header">
            <div>
                <h2>Daftar Pengguna (Users)</h2>
                <p>Total {{ $users->total() }} akun pengguna terdaftar.</p>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="lokantara-table">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Tanggal Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td data-label="Nama Pengguna"><strong>{{ $u->name }}</strong></td>
                                <td data-label="Email">{{ $u->email }}</td>
                                <td data-label="Status">
                                    <span
                                        class="status-badge {{ $u->status === 'active' ? 'status-success' : 'status-warning' }}">
                                        {{ strtoupper($u->status) }}
                                    </span>
                                </td>
                                <td data-label="Tanggal Terdaftar">{{ $u->created_at?->format('d M Y, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state title="Belum ada pengguna" description="Tidak ada data pengguna."
                                        compact />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
