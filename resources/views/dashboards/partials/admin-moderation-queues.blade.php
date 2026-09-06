@php
    $queues = $moderationQueues ?? [];
    $totalPending = $totalModerationPending ?? 0;
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
    <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2.5">
            <div class="p-2 rounded-3 bg-warning-subtle text-warning fs-6">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6">Pusat Antrean Moderasi &amp; Verifikasi Layanan</h5>
                <small class="text-muted fs-8">Ringkasan status antrean yang memerlukan persetujuan dan tinjauan administrator</small>
            </div>
        </div>
        <div>
            @if ($totalPending > 0)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1.5 fw-bold fs-8">
                    <i class="fa-solid fa-bell me-1 animate-pulse"></i> {{ $totalPending }} Total Antrean Memerlukan Tindakan
                </span>
            @else
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold fs-8">
                    <i class="fa-solid fa-circle-check me-1"></i> Semua Antrean Bersih (0 Pending)
                </span>
            @endif
        </div>
    </div>

    <div class="card-body p-4 bg-light">
        <div class="row g-3">
            @foreach ($queues as $key => $queue)
                @php
                    $pendingCount = ($queue['items'] ?? 0) + ($queue['reviews'] ?? 0);
                    $hasPending = $pendingCount > 0;
                    $borderClass = $hasPending ? 'border-warning-subtle shadow-xs' : 'border-0';
                    $badgeBg = $hasPending ? 'bg-danger text-white' : 'bg-secondary-subtle text-secondary';
                @endphp
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <a href="{{ route($queue['route']) }}" class="text-decoration-none d-block h-100">
                        <div class="card {{ $borderClass }} rounded-4 p-3 bg-white h-100 transition-all hover-shadow d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="p-2 rounded-3 bg-{{ $queue['color'] }}-subtle text-{{ $queue['color'] }} d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 16px;">
                                        <i class="{{ $queue['icon'] }}"></i>
                                    </div>
                                    <span class="badge {{ $badgeBg }} rounded-pill px-2.5 py-1 fs-8 fw-bold">
                                        {{ $pendingCount }} Antrean
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 fs-7">{{ $queue['label'] }}</h6>
                                <p class="text-muted fs-9 mb-0">
                                    @if(isset($queue['reviews']) && $queue['reviews'] > 0)
                                        {{ $queue['items'] }} Katalog &middot; {{ $queue['reviews'] }} Ulasan
                                    @else
                                        Menunggu tinjauan berkas
                                    @endif
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top fs-8 text-{{ $queue['color'] }} fw-semibold">
                                <span>Buka Antrean</span>
                                <i class="fa-solid fa-arrow-right fs-9"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
