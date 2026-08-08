@extends('layouts.admin')

@section('title', 'Audit Log')
@section('page-title', 'Catatan Audit Aktivitas')
@section('page-description', 'Riwayat lengkap jejak audit tindakan pengguna dan sistem.')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div>
            <h2>Riwayat Audit Log</h2>
            <p>Jejak audit peristiwa sistem.</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="lokantara-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Target Model</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td data-label="Waktu">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                            <td data-label="User">{{ $log->user?->name ?? 'System' }}</td>
                            <td data-label="Event"><code>{{ $log->event }}</code></td>
                            <td data-label="Target Model">{{ $log->auditable_type ? class_basename($log->auditable_type) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state title="Belum ada audit log" description="Catatan audit akan tampil di sini." compact />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
