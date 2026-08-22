<div class="d-flex flex-column h-100 bg-white" style="box-shadow: -4px 0 15px rgba(0,0,0,0.05); z-index: 10;">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between p-4 border-bottom">
        <h2 class="fs-4 fw-bold mb-0 text-dark">Layanan Terdekat</h2>
    </div>

    <!-- Filters -->
    <div class="px-4 pt-3 pb-2 border-bottom" style="background: #fafafa;">
        <div class="d-flex flex-wrap gap-2">
            <button wire:click="setFilter('all')" class="btn btn-sm rounded-pill fw-bold {{ $filterType === 'all' ? 'btn-emerald text-white' : 'btn-outline-secondary' }}" style="{{ $filterType === 'all' ? 'background-color: #10b981; border-color: #10b981;' : 'font-size: 0.75rem;' }}">Semua</button>
            <button wire:click="setFilter('tourism')" class="btn btn-sm rounded-pill fw-bold {{ $filterType === 'tourism' ? 'btn-emerald text-white' : 'btn-outline-secondary' }}" style="{{ $filterType === 'tourism' ? 'background-color: #10b981; border-color: #10b981;' : 'font-size: 0.75rem;' }}">Wisata</button>
            <button wire:click="setFilter('accommodation')" class="btn btn-sm rounded-pill fw-bold {{ $filterType === 'accommodation' ? 'btn-emerald text-white' : 'btn-outline-secondary' }}" style="{{ $filterType === 'accommodation' ? 'background-color: #10b981; border-color: #10b981;' : 'font-size: 0.75rem;' }}">Penginapan</button>
            <button wire:click="setFilter('culinary')" class="btn btn-sm rounded-pill fw-bold {{ $filterType === 'culinary' ? 'btn-emerald text-white' : 'btn-outline-secondary' }}" style="{{ $filterType === 'culinary' ? 'background-color: #10b981; border-color: #10b981;' : 'font-size: 0.75rem;' }}">Kuliner</button>
            <button wire:click="setFilter('event')" class="btn btn-sm rounded-pill fw-bold {{ $filterType === 'event' ? 'btn-emerald text-white' : 'btn-outline-secondary' }}" style="{{ $filterType === 'event' ? 'background-color: #10b981; border-color: #10b981;' : 'font-size: 0.75rem;' }}">Event & Acara</button>
            <button wire:click="setFilter('rental')" class="btn btn-sm rounded-pill fw-bold {{ $filterType === 'rental' ? 'btn-emerald text-white' : 'btn-outline-secondary' }}" style="{{ $filterType === 'rental' ? 'background-color: #10b981; border-color: #10b981;' : 'font-size: 0.75rem;' }}">Rental Kendaraan</button>
        </div>
    </div>

    <!-- List of Services -->
    <div class="flex-grow-1 overflow-auto p-3 position-relative" style="overflow-y: auto;">
        <div wire:loading.flex class="position-absolute w-100 h-100 justify-content-center pt-5 bg-white" style="z-index: 5; opacity: 0.8; top: 0; left: 0;">
            <div class="spinner-border text-emerald mt-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="d-flex flex-column gap-3">
            @forelse($services as $service)
                @php
                    $cover = $service->media->first();
                    $coverUrl = $cover ? asset('storage/' . $cover->object_key) : asset('images/placeholder.jpg');
                    $route = route($service->serviceType->code . '.show', $service->slug);
                @endphp
                <a href="{{ $route }}" class="text-decoration-none text-dark d-block">
                    <div class="d-flex gap-3 align-items-start p-2 rounded-3" style="transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc';" onmouseout="this.style.backgroundColor='transparent';">
                        <!-- Thumbnail -->
                        <div class="flex-shrink-0">
                            <img src="{{ $coverUrl }}" alt="{{ $service->name }}" class="rounded-3 object-fit-cover shadow-sm" style="width: 75px; height: 75px;">
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-grow-1">
                            <h3 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">{{ $service->name }}</h3>
                            <p class="text-muted mb-1" style="font-size: 0.75rem; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $service->description ? strip_tags($service->description) : 'Menyediakan fasilitas dan layanan berkualitas tinggi.' }}
                            </p>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-light text-secondary border fw-semibold" style="font-size: 0.65rem;">
                                    {{ ucfirst($service->serviceType->code) }}
                                </span>
                                <span class="text-emerald fw-bold" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-location-dot"></i> {{ number_format($service->distance, 1) }} km
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-box-open fs-2 mb-2 opacity-50"></i>
                    <p class="fs-7">Tidak ada layanan dalam radius ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
