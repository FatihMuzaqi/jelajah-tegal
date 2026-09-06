@extends('layouts.admin')

@section('title', 'Audit Log')
@section('page-title', 'Catatan Audit Aktivitas')
@section('page-description', 'Riwayat lengkap jejak audit tindakan pengguna dan sistem platform Jelajah Tegal.')

@section('content')
    <!-- Search & Filter Toolbar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mb-3">
        <div>
            <span class="fs-8 text-muted fw-semibold">Total Terdata: <strong class="text-dark">{{ number_format($logs->total(), 0, ',', '.') }}</strong> Log Aktivitas</span>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.audit.index') }}" class="d-flex align-items-center gap-2">
            @if (request('event'))
                <input type="hidden" name="event" value="{{ request('event') }}">
            @endif
            <div class="input-group input-group-sm" style="min-width: 280px;">
                <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                    class="form-control bg-light border-start-0 fs-8" placeholder="Cari event, model, user, IP...">
            </div>
            @if (request('q') || request('event'))
                <a href="{{ route('admin.audit.index') }}" class="btn btn-sm btn-light border text-muted"
                    title="Reset Filter">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <x-table-wrapper title="Riwayat Jejak Audit Sistem">
        @if ($logs->isEmpty())
            <tbody>
                <tr>
                    <td colspan="6">
                        <x-empty-state title="Tidak ada catatan audit"
                            description="Belum ada catatan aktivitas yang sesuai dengan filter atau pencarian Anda." compact />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th style="min-width: 170px;">Waktu &amp; IP</th>
                    <th style="min-width: 180px;">Pelaku / User</th>
                    <th style="min-width: 160px;">Mitra Terkait</th>
                    <th style="min-width: 150px;">Peristiwa (Event)</th>
                    <th style="min-width: 160px;">Target Entitas</th>
                    <th class="text-end" style="min-width: 90px;">Rincian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <!-- 1. Waktu & IP -->
                        <td data-label="Waktu">
                            <div class="fw-semibold text-dark fs-8">
                                <i class="fa-regular fa-clock text-muted me-1"></i>{{ $log->created_at?->format('d M Y, H:i:s') ?? '-' }}
                            </div>
                            @if ($log->ip_address)
                                <small class="text-muted fs-9 d-block mt-0.5">
                                    <i class="fa-solid fa-network-wired me-1"></i>{{ $log->ip_address }}
                                </small>
                            @endif
                        </td>

                        <!-- 2. Pelaku / User -->
                        <td data-label="Pelaku">
                            @if ($log->actor)
                                <div class="fw-bold text-dark fs-8">{{ $log->actor->name }}</div>
                                <small class="text-muted fs-9 d-block">{{ $log->actor->email }}</small>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-0.5 fs-8">
                                    <i class="fa-solid fa-robot me-1"></i> Sistem
                                </span>
                            @endif
                        </td>

                        <!-- 3. Mitra Terkait -->
                        <td data-label="Mitra">
                            @if ($log->mitra)
                                <a href="{{ route('admin.mitras.show', $log->mitra) }}" class="text-dark fw-semibold fs-8 text-decoration-none hover-primary">
                                    {{ $log->mitra->display_name }}
                                </a>
                            @else
                                <span class="text-muted fs-8">—</span>
                            @endif
                        </td>

                        <!-- 4. Peristiwa / Event -->
                        <td data-label="Event">
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-8">
                                {{ $log->event }}
                            </span>
                        </td>

                        <!-- 5. Target Entitas -->
                        <td data-label="Target">
                            @if ($log->auditable_type)
                                <div class="fw-semibold text-dark fs-8">
                                    {{ class_basename($log->auditable_type) }}
                                </div>
                                @if ($log->auditable_id)
                                    <small class="text-muted font-monospace fs-9 d-block">
                                        ID: {{ Str::limit($log->auditable_id, 12) }}
                                    </small>
                                @endif
                            @else
                                <span class="text-muted fs-8">—</span>
                            @endif
                        </td>

                        <!-- 6. Rincian Modal -->
                        <td data-label="Rincian" class="text-end">
                            @if (!empty($log->before_values) || !empty($log->after_values) || !empty($log->metadata) || $log->user_agent)
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 fs-8 text-secondary"
                                        data-bs-toggle="modal" data-bs-target="#auditModal-{{ $log->id }}" title="Lihat rincian payload data">
                                    <i class="fa-solid fa-code"></i>
                                </button>

                                <!-- Modal Detail Audit Log -->
                                <div class="modal fade text-start" id="auditModal-{{ $log->id }}" tabindex="-1" aria-labelledby="auditModalLabel-{{ $log->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                            <div class="modal-header bg-dark text-white py-3 px-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-shield-halved text-info fs-5"></i>
                                                    <div>
                                                        <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="auditModalLabel-{{ $log->id }}">
                                                            Rincian Jejak Audit: {{ $log->event }}
                                                        </h5>
                                                        <small class="text-white-50">
                                                            {{ $log->created_at?->format('d M Y, H:i:s') }} &middot; ID: {{ $log->id }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 bg-light">
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <div class="card p-3 border rounded-3 bg-white h-100">
                                                            <div class="fs-8 text-muted mb-1">Pelaku / Aktor:</div>
                                                            <div class="fw-bold text-dark fs-7">{{ $log->actor?->name ?? 'Sistem Otomatis' }}</div>
                                                            <div class="fs-8 text-muted">{{ $log->actor?->email ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card p-3 border rounded-3 bg-white h-100">
                                                            <div class="fs-8 text-muted mb-1">Target / Konteks:</div>
                                                            <div class="fw-bold text-dark fs-7">{{ $log->auditable_type ?? 'None' }}</div>
                                                            <div class="fs-8 text-muted font-monospace">ID: {{ $log->auditable_id ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($log->user_agent)
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-dark fs-8 mb-1">User Agent:</label>
                                                        <div class="p-2 rounded bg-white border text-muted fs-8 font-monospace text-break">
                                                            {{ $log->user_agent }}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($log->before_values))
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-danger fs-8 mb-1">Data Sebelum Perubahan (Before):</label>
                                                        <pre class="p-2.5 rounded bg-dark text-warning fs-9 mb-0 overflow-auto" style="max-height: 200px;"><code>{{ json_encode($log->before_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                    </div>
                                                @endif

                                                @if (!empty($log->after_values))
                                                    <div class="mb-3">
                                                        <label class="fw-bold text-success fs-8 mb-1">Data Setelah Perubahan (After):</label>
                                                        <pre class="p-2.5 rounded bg-dark text-success fs-9 mb-0 overflow-auto" style="max-height: 200px;"><code>{{ json_encode($log->after_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                    </div>
                                                @endif

                                                @if (!empty($log->metadata))
                                                    <div>
                                                        <label class="fw-bold text-primary fs-8 mb-1">Metadata Tambahan:</label>
                                                        <pre class="p-2.5 rounded bg-dark text-info fs-9 mb-0 overflow-auto" style="max-height: 200px;"><code>{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer bg-white border-top py-2.5 px-4">
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted fs-8">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $logs->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection

