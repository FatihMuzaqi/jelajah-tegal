@extends('layouts.consumer')

@section('title', 'Rute & Panduan Destinasi')
@section('page-title', 'Panduan Rute Perjalanan')
@section('page-description', 'Peta interaktif penunjuk jalan menuju destinasi wisata, hotel, kuliner, dan event yang telah Anda bayar.')

@section('content')
<!-- Leaflet & Font Awesome Assets -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* 1. Main Grid Layout */
    .trip-navigator-container {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 20px;
        min-height: calc(100vh - 220px);
        margin-bottom: 24px;
    }

    @media (max-width: 991.98px) {
        .trip-navigator-container {
            display: flex;
            flex-direction: column-reverse;
            gap: 16px;
            min-height: auto;
            margin-bottom: 20px;
        }
    }

    /* 2. Sidebar Styles (Destination List) */
    .navigator-sidebar {
        background: #ffffff;
        border: 1px solid var(--lokantara-border, #e2e8f0);
        border-radius: 20px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        height: 720px;
        max-height: calc(100vh - 180px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    @media (max-width: 991.98px) {
        .navigator-sidebar {
            height: auto;
            max-height: none;
            padding: 16px;
            border-radius: 16px;
        }
    }

    .destinations-scroll-list {
        overflow-y: auto;
        flex: 1;
        padding-right: 4px;
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    @media (max-width: 991.98px) {
        .destinations-scroll-list {
            max-height: 380px;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    .destinations-scroll-list::-webkit-scrollbar {
        width: 5px;
    }
    .destinations-scroll-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    /* Destination Card Styles */
    .dest-item-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px 16px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }
    .dest-item-card:hover {
        border-color: #94a3b8;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.05);
    }
    .dest-item-card:active {
        transform: scale(0.98);
    }
    .dest-item-card.active-card {
        border-color: #4f46e5 !important;
        background: #f8faff !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
    }

    /* Category Icon Box */
    .dest-icon-box {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 12px;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 17px !important;
        line-height: 1 !important;
        flex-shrink: 0;
    }
    .dest-icon-box i {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
    }

    /* 3. Map Box & Responsive Heights */
    .navigator-map-wrapper {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--lokantara-border, #e2e8f0);
        height: 720px;
        max-height: calc(100vh - 180px);
        min-height: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    @media (max-width: 991.98px) {
        .navigator-map-wrapper {
            height: clamp(340px, 48vh, 460px);
            min-height: 320px;
            max-height: 52vh;
            border-radius: 16px;
        }
    }

    #navigator-map {
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    /* 4. Floating Route Info Bar over Map */
    .floating-route-bar {
        position: absolute;
        bottom: 18px;
        left: 18px;
        right: 18px;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 18px;
        padding: 14px 18px;
        z-index: 1000;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.14);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        animation: slideUpRouteBar 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes slideUpRouteBar {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 768px) {
        .floating-route-bar {
            flex-direction: column;
            align-items: stretch;
            bottom: 8px;
            left: 8px;
            right: 8px;
            padding: 12px;
            border-radius: 14px;
            gap: 10px;
        }
    }

    /* Pulse GPS Icon */
    .pulse-dot {
        width: 10px;
        height: 10px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseGps 1.6s infinite;
        flex-shrink: 0;
    }
    @keyframes pulseGps {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Custom Leaflet Pulsing User Marker */
    .user-gps-marker {
        width: 18px;
        height: 18px;
        background: #3b82f6;
        border: 3px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 0 6px rgba(59, 130, 246, 0.35);
    }

    /* Custom Leaflet Pin Styling */
    .custom-leaflet-pin {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="trip-navigator-container">
    <!-- 1. LEFT / BOTTOM SIDEBAR: LIST OF PAID DESTINATIONS -->
    <div class="navigator-sidebar">
        <!-- Sidebar Header -->
        <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h5 class="fw-bold text-dark mb-0 fs-6">Rute Destinasi</h5>
            </div>
            <span class="badge rounded-pill fw-bold px-2.5 py-1" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 11px;">
                {{ $totalPaid }} Terbayar
            </span>
        </div>
        <p class="text-muted mb-2.5" style="font-size: 12px;">
            Pilih tempat untuk melihat panduan rute jalan & estimasi waktu dari lokasi Anda.
        </p>

        <!-- GPS Connection Status Card -->
        <div id="gps-status-pill" class="p-2.5 rounded-3 d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2" 
             style="background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 11.5px; color: #166534;">
            <div class="d-flex align-items-center gap-2">
                <span class="pulse-dot"></span>
                <span id="gps-status-text" class="fw-semibold">GPS Terhubung (Lokasi Terdeteksi)</span>
            </div>
            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" style="font-size: 11px; color: #047857;" onclick="requestUserGPS(true)">
                <i class="fa-solid fa-arrows-rotate me-0.5"></i> Refresh
            </button>
        </div>

        <!-- Scrollable Destinations List -->
        <div class="destinations-scroll-list" id="destinations-list-container">
            @forelse($destinations as $index => $dest)
                <div class="dest-item-card {{ $index === 0 ? 'active-card' : '' }}" 
                     id="dest-card-{{ $dest['id'] }}" 
                     onclick="selectDestination('{{ $dest['id'] }}')">
                    
                    <!-- Icon based on domain -->
                    <div class="dest-icon-box" style="background: {{ $dest['badge_color'] }}18; color: {{ $dest['badge_color'] }};">
                        <i class="{{ $dest['icon'] }}"></i>
                    </div>

                    <!-- Destination Info -->
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                            <span class="text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 0.05em; color: {{ $dest['badge_color'] }};">
                                {{ $dest['service_label'] }}
                            </span>
                            <span class="badge bg-success-subtle text-success px-2 py-0.5 rounded-pill fw-bold" style="font-size: 10px;">
                                Lunas
                            </span>
                        </div>
                        <strong class="d-block text-dark text-truncate mb-0.5" style="font-size: 13.5px;">
                            {{ $dest['name'] }}
                        </strong>
                        <p class="text-muted text-truncate mb-1" style="font-size: 11px;">
                            {{ $dest['address'] }}
                        </p>
                        
                        <div class="d-flex align-items-center justify-content-between pt-1 border-top mt-1" style="font-size: 11px;">
                            <span class="text-muted">
                                <i class="fa-regular fa-calendar me-1"></i> {{ $dest['booking_date'] }}
                            </span>
                            <span class="fw-bold text-primary dest-distance-label" id="distance-label-{{ $dest['id'] }}" style="color: #4f46e5 !important;">
                                Menghitung...
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State jika belum ada pesanan terbayar -->
                <div class="text-center p-4 my-auto">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-map-location-dot text-muted fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Destinasi Terbayar</h6>
                    <p class="text-muted small mb-3">Pesan tiket wisata, hotel, kuliner, atau event untuk melihat panduan rute navigasi di sini.</p>
                    <a href="{{ route('tourism.index') }}" class="btn btn-sm btn-lokantara fw-bold w-100 mb-2">
                        Jelajahi Wisata Tegal
                    </a>
                    <a href="{{ route('tour-assistant.index') }}" class="btn btn-sm btn-outline-lokantara w-100 fw-bold">
                        <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Rencanakan dengan AI
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2. RIGHT / TOP AREA: FULL INTERACTIVE MAP & ROUTE NAVIGATOR -->
    <div class="navigator-map-wrapper" id="navigator-map-wrapper">
        <div id="navigator-map"></div>

        <!-- Floating Route Action Bar (Muncul saat destinasi aktif) -->
        <div class="floating-route-bar" id="floating-route-bar" style="display: none;">
            <div class="d-flex align-items-center gap-2.5 min-w-0">
                <div class="dest-icon-box" id="floating-icon-box" style="background: #eef2ff; color: #4f46e5;">
                    <i class="fa-solid fa-location-dot" id="floating-icon"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-light text-dark border fw-bold" id="floating-category" style="font-size: 10px;">WISATA</span>
                        <strong class="text-dark text-truncate" id="floating-title" style="font-size: 13.5px;">Nama Destinasi</strong>
                    </div>
                    <div class="d-flex align-items-center gap-3 text-muted mt-0.5 flex-wrap" style="font-size: 11.5px;">
                        <span><i class="fa-solid fa-road text-primary me-1"></i> <b id="floating-distance" class="text-dark">-</b></span>
                        <span><i class="fa-solid fa-car text-success me-1"></i> <b id="floating-duration" class="text-dark">-</b></span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center gap-2 flex-shrink-0 w-100-mobile">
                <a id="floating-ticket-btn" href="#" class="btn btn-sm btn-outline-dark fw-bold px-3 d-none rounded-pill" style="font-size: 12px;">
                    <i class="fa-solid fa-qrcode me-1 text-primary"></i> E-Tiket
                </a>
                <a id="floating-gmaps-btn" href="#" target="_blank" rel="noopener noreferrer" 
                   class="btn btn-sm btn-primary fw-bold px-3.5 py-2 shadow-sm d-flex align-items-center justify-content-center gap-1.5 rounded-pill flex-grow-1"
                   style="background: #047857; border: none; font-size: 12.5px;">
                    <i class="fa-solid fa-diamond-turn-right text-warning"></i>
                    <span>Mulai Navigasi (Google Maps)</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Trip Navigator JavaScript Engine -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const destinations = {!! $destinationsJson !!};
    
    // Default Map Center: Tegal City / Slawi
    let defaultCenter = [-6.8797000, 109.1256000];
    let userLocation = null;
    let activeDestination = null;
    let routePolyline = null;
    let destinationMarkers = {};
    let userMarker = null;

    // 1. Inisialisasi Peta Leaflet
    const map = L.map('navigator-map', {
        zoomControl: true,
        attributionControl: false
    }).setView(defaultCenter, 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    // Reposition zoom control to top-right on mobile to prevent collision with bottom bar
    if (window.innerWidth < 992) {
        map.zoomControl.setPosition('topright');
    }

    // Invalidate size on initial load and resize
    setTimeout(() => {
        map.invalidateSize();
    }, 250);

    window.addEventListener('resize', () => {
        map.invalidateSize();
    });

    // Custom Icon Generator
    function createCustomPin(iconClass, bgColor) {
        return L.divIcon({
            className: 'custom-leaflet-pin',
            html: `<div style="width: 36px; height: 36px; background: ${bgColor}; border: 2.5px solid #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
                    <i class="${iconClass}"></i>
                   </div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -18]
        });
    }

    // 2. Render Markers Destinasi di Peta
    destinations.forEach(dest => {
        const marker = L.marker([dest.latitude, dest.longitude], {
            icon: createCustomPin(dest.icon, dest.badge_color)
        }).addTo(map);

        marker.bindPopup(`
            <div style="min-width: 170px; font-family: system-ui, -apple-system, sans-serif;">
                <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: ${dest.badge_color};">${dest.service_label}</span>
                <h6 style="margin: 2px 0 4px; font-size: 13px; font-weight: 700; color: #0f172a;">${dest.name}</h6>
                <p style="margin: 0 0 8px; font-size: 11px; color: #64748b;">${dest.address}</p>
                <button class="btn btn-sm btn-primary w-100 rounded-pill" style="font-size: 11px; padding: 5px 10px; background: ${dest.badge_color}; border: none;" onclick="selectDestination('${dest.id}')">
                    📍 Panduan Rute ke Sini
                </button>
            </div>
        `);

        destinationMarkers[dest.id] = marker;
    });

    // 3. Deteksi GPS Pengguna Real-time
    function requestUserGPS(isManualRefresh = false) {
        const statusPill = document.getElementById('gps-status-pill');
        const statusText = document.getElementById('gps-status-text');

        if (!navigator.geolocation) {
            statusText.textContent = "Browser tidak mendukung GPS";
            statusPill.style.background = "#fef2f2";
            statusPill.style.color = "#991b1b";
            return;
        }

        statusText.textContent = "Mendeteksi posisi GPS...";

        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLocation = [position.coords.latitude, position.coords.longitude];
                
                statusText.textContent = "GPS Terhubung (Lokasi Terdeteksi)";
                statusPill.style.background = "#f0fdf4";
                statusPill.style.color = "#166534";

                // Update / Buat User Marker
                if (userMarker) {
                    userMarker.setLatLng(userLocation);
                } else {
                    const userIcon = L.divIcon({
                        className: 'user-gps-pin',
                        html: `<div class="user-gps-marker"></div>`,
                        iconSize: [18, 18],
                        iconAnchor: [9, 9]
                    });
                    userMarker = L.marker(userLocation, { icon: userIcon, zIndexOffset: 1000 }).addTo(map);
                    userMarker.bindPopup("<b>📍 Lokasi Anda Saat Ini</b>");
                }

                // Hitung jarak ke seluruh destinasi
                calculateAllDistances();

                // Jika ada destinasi pertama, otomatis gambarkan rute
                if (destinations.length > 0) {
                    if (!activeDestination) {
                        selectDestination(destinations[0].id);
                    } else {
                        drawRoute(activeDestination);
                    }
                } else {
                    map.setView(userLocation, 14);
                }
            },
            function(error) {
                // Fallback default jika izin GPS ditolak
                userLocation = defaultCenter;
                statusText.textContent = "GPS dinonaktifkan (Gunakan Default Tegal)";
                statusPill.style.background = "#fffbeb";
                statusPill.style.color = "#92400e";

                if (destinations.length > 0) {
                    selectDestination(destinations[0].id);
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    // 4. Hitung Jarak Garis Lurus (Haversine Formula)
    function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius Bumi km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return (R * c).toFixed(1);
    }

    function calculateAllDistances() {
        if (!userLocation) return;
        destinations.forEach(dest => {
            const dist = calculateHaversineDistance(userLocation[0], userLocation[1], dest.latitude, dest.longitude);
            const labelEl = document.getElementById(`distance-label-${dest.id}`);
            if (labelEl) {
                labelEl.textContent = `📍 ${dist} km`;
            }
        });
    }

    // 5. Pilih Destinasi & Gambar Rute Navigasi Jalan (OSRM)
    window.selectDestination = function(destId) {
        const dest = destinations.find(d => d.id === destId);
        if (!dest) return;

        activeDestination = dest;

        // Highlight Active Card di Sidebar
        document.querySelectorAll('.dest-item-card').forEach(card => card.classList.remove('active-card'));
        const selectedCard = document.getElementById(`dest-card-${destId}`);
        if (selectedCard) {
            selectedCard.classList.add('active-card');
        }

        // Invalidate map size and center
        map.invalidateSize();

        // Tampilkan Floating Info Bar
        updateFloatingRouteBar(dest);

        // Gambar Rute Jalan Raya
        drawRoute(dest);

        // Scroll to map on mobile if clicked from list
        if (window.innerWidth < 992) {
            const mapWrapper = document.getElementById('navigator-map-wrapper');
            if (mapWrapper && window.scrollY > mapWrapper.offsetTop + 200) {
                mapWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    };

    function updateFloatingRouteBar(dest) {
        const bar = document.getElementById('floating-route-bar');
        const iconBox = document.getElementById('floating-icon-box');
        const icon = document.getElementById('floating-icon');
        const category = document.getElementById('floating-category');
        const title = document.getElementById('floating-title');
        const gmapsBtn = document.getElementById('floating-gmaps-btn');
        const ticketBtn = document.getElementById('floating-ticket-btn');

        bar.style.display = 'flex';
        iconBox.style.background = `${dest.badge_color}18`;
        iconBox.style.color = dest.badge_color;
        icon.className = dest.icon;
        category.textContent = dest.service_label;
        title.textContent = dest.name;

        // Google Maps Turn-by-Turn Intent URL
        const userLat = userLocation ? userLocation[0] : '';
        const userLng = userLocation ? userLocation[1] : '';
        gmapsBtn.href = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${dest.latitude},${dest.longitude}&travelmode=driving`;

        // E-Tiket Link
        if (dest.ticket_qr_url) {
            ticketBtn.href = dest.ticket_qr_url;
            ticketBtn.classList.remove('d-none');
        } else {
            ticketBtn.classList.add('d-none');
        }
    }

    function drawRoute(dest) {
        if (!userLocation) {
            userLocation = defaultCenter;
        }

        const startLng = userLocation[1];
        const startLat = userLocation[0];
        const endLng = dest.longitude;
        const endLat = dest.latitude;

        const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${startLng},${startLat};${endLng},${endLat}?overview=full&geometries=geojson`;

        fetch(osrmUrl)
            .then(res => res.json())
            .then(data => {
                if (data.routes && data.routes.length > 0) {
                    const route = data.routes[0];
                    const coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);

                    // Hapus rute lama
                    if (routePolyline) {
                        map.removeLayer(routePolyline);
                    }

                    // Gambar garis rute baru dengan visual modern
                    routePolyline = L.polyline(coordinates, {
                        color: '#4f46e5',
                        weight: 5,
                        opacity: 0.9,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).addTo(map);

                    // Update info jarak dan waktu
                    const distanceKm = (route.distance / 1000).toFixed(1);
                    const durationMins = Math.round(route.duration / 60);

                    document.getElementById('floating-distance').textContent = `${distanceKm} km`;
                    document.getElementById('floating-duration').textContent = `${durationMins} menit`;

                    // Zoom pas ke rute dengan padding aman
                    const topPadding = window.innerWidth < 768 ? 20 : 40;
                    const bottomPadding = window.innerWidth < 768 ? 130 : 90;
                    map.fitBounds(routePolyline.getBounds(), { 
                        paddingTopLeft: [20, topPadding],
                        paddingBottomRight: [20, bottomPadding]
                    });
                } else {
                    fallbackDirectRoute(dest);
                }
            })
            .catch(err => {
                fallbackDirectRoute(dest);
            });
    }

    function fallbackDirectRoute(dest) {
        if (routePolyline) {
            map.removeLayer(routePolyline);
        }
        const coords = [userLocation, [dest.latitude, dest.longitude]];
        routePolyline = L.polyline(coords, {
            color: '#4f46e5',
            weight: 4,
            dashArray: '8, 8',
            opacity: 0.8
        }).addTo(map);

        const dist = calculateHaversineDistance(userLocation[0], userLocation[1], dest.latitude, dest.longitude);
        document.getElementById('floating-distance').textContent = `± ${dist} km`;
        document.getElementById('floating-duration').textContent = `± ${Math.round(dist * 2.5)} menit`;

        const topPadding = window.innerWidth < 768 ? 20 : 40;
        const bottomPadding = window.innerWidth < 768 ? 130 : 90;
        map.fitBounds(routePolyline.getBounds(), { 
            paddingTopLeft: [20, topPadding],
            paddingBottomRight: [20, bottomPadding]
        });
    }

    window.requestUserGPS = requestUserGPS;

    // Start GPS detection on load
    requestUserGPS();
});
</script>
@endsection
