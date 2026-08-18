@extends('layouts.consumer')

@section('title', 'Rute & Panduan Destinasi')
@section('page-title', 'Panduan Rute Perjalanan')
@section('page-description', 'Peta interaktif penunjuk jalan menuju destinasi wisata, hotel, kuliner, dan event yang telah Anda bayar.')

@section('content')
<!-- Leaflet & Font Awesome Assets -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    .trip-navigator-container {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 20px;
        min-height: calc(100vh - 220px);
        margin-bottom: 24px;
    }
    @media (max-width: 991.98px) {
        .trip-navigator-container {
            grid-template-columns: 1fr;
            min-height: auto;
        }
    }

    /* Sidebar Styles */
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
    .destinations-scroll-list {
        overflow-y: auto;
        flex: 1;
        padding-right: 4px;
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .destinations-scroll-list::-webkit-scrollbar {
        width: 5px;
    }
    .destinations-scroll-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    /* Destination Card Styles matching Mockup */
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
    }
    .dest-item-card:hover {
        border-color: #94a3b8;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.05);
    }
    .dest-item-card.active-card {
        border-color: #6366f1 !important;
        background: #f8faff !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
    }

    /* Category Icon Box */
    .dest-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    /* Map Box */
    .navigator-map-wrapper {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--lokantara-border, #e2e8f0);
        height: 720px;
        max-height: calc(100vh - 180px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    #navigator-map {
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    /* Floating Route Info Bar over Map */
    .floating-route-bar {
        position: absolute;
        bottom: 20px;
        left: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 18px;
        padding: 16px 20px;
        z-index: 1000;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        animation: slideUpRouteBar 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes slideUpRouteBar {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @media (max-width: 768px) {
        .floating-route-bar {
            flex-direction: column;
            align-items: stretch;
            bottom: 12px;
            left: 12px;
            right: 12px;
            padding: 14px;
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
</style>

<div class="trip-navigator-container">
    <!-- 1. LEFT SIDEBAR: LIST OF PAID DESTINATIONS -->
    <div class="navigator-sidebar">
        <!-- Sidebar Header -->
        <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-map-location-dot text-primary fs-5" style="color: #4f46e5 !important;"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Rute Destinasi</h5>
            </div>
            <span class="badge rounded-pill fw-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                {{ $totalPaid }} Terbayar
            </span>
        </div>
        <p class="text-muted mb-3" style="font-size: 12px;">
            Pilih tempat untuk melihat panduan rute jalan dari lokasi Anda.
        </p>

        <!-- GPS Connection Status Card -->
        <div id="gps-status-pill" class="p-2.5 rounded-3 d-flex align-items-center justify-content-between mb-2" 
             style="background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 12px; color: #166534;">
            <div class="d-flex align-items-center gap-2">
                <span class="pulse-dot"></span>
                <span id="gps-status-text" class="fw-semibold">GPS Terhubung (Lokasi Terdeteksi)</span>
            </div>
            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" style="font-size: 11px; color: #047857;" onclick="requestUserGPS(true)">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh
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
                        <strong class="d-block text-dark text-truncate mb-1" style="font-size: 14px;">
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

    <!-- 2. RIGHT AREA: FULL INTERACTIVE MAP & ROUTE NAVIGATOR -->
    <div class="navigator-map-wrapper">
        <div id="navigator-map"></div>

        <!-- Floating Route Action Bar (Muncul saat destinasi aktif) -->
        <div class="floating-route-bar" id="floating-route-bar" style="display: none;">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <div class="dest-icon-box" id="floating-icon-box" style="background: #eef2ff; color: #4f46e5;">
                    <i class="fa-solid fa-location-dot" id="floating-icon"></i>
                </div>
                <div class="min-w-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border fw-bold" id="floating-category" style="font-size: 10px;">WISATA</span>
                        <strong class="text-dark text-truncate" id="floating-title" style="font-size: 14px;">Nama Destinasi</strong>
                    </div>
                    <div class="d-flex align-items-center gap-3 text-muted mt-1" style="font-size: 12px;">
                        <span><i class="fa-solid fa-road text-primary me-1"></i> <b id="floating-distance" class="text-dark">-</b></span>
                        <span><i class="fa-solid fa-car text-success me-1"></i> <b id="floating-duration" class="text-dark">-</b></span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <a id="floating-ticket-btn" href="#" class="btn btn-sm btn-outline-dark fw-bold px-3 d-none">
                    <i class="fa-solid fa-qrcode me-1 text-primary"></i> E-Tiket
                </a>
                <a id="floating-gmaps-btn" href="#" target="_blank" rel="noopener noreferrer" 
                   class="btn btn-sm btn-primary fw-bold px-3.5 py-2 shadow-sm d-flex align-items-center gap-1.5"
                   style="background: #047857; border: none;">
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

    // Custom Icon Generator
    function createCustomPin(iconClass, bgColor) {
        return L.divIcon({
            className: 'custom-leaflet-pin',
            html: `<div style="width: 38px; height: 38px; background: ${bgColor}; border: 3px solid #ffffff; border-radius: 50%; display: grid; place-items: center; color: #ffffff; font-size: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
                    <i class="${iconClass}"></i>
                   </div>`,
            iconSize: [38, 38],
            iconAnchor: [19, 19],
            popupAnchor: [0, -20]
        });
    }

    // 2. Render Markers Destinasi di Peta
    destinations.forEach(dest => {
        const marker = L.marker([dest.latitude, dest.longitude], {
            icon: createCustomPin(dest.icon, dest.badge_color)
        }).addTo(map);

        marker.bindPopup(`
            <div style="min-width: 180px; font-family: system-ui, -apple-system, sans-serif;">
                <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: ${dest.badge_color};">${dest.service_label}</span>
                <h6 style="margin: 2px 0 4px; font-size: 14px; font-weight: 700;">${dest.name}</h6>
                <p style="margin: 0 0 8px; font-size: 11px; color: #64748b;">${dest.address}</p>
                <button class="btn btn-sm btn-primary w-100" style="font-size: 11px; padding: 4px 8px; background: ${dest.badge_color}; border: none;" onclick="selectDestination('${dest.id}')">
                    📍 Lihat Rute ke Sini
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
            selectedCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Tampilkan Floating Info Bar
        updateFloatingRouteBar(dest);

        // Gambar Rute Jalan Raya
        drawRoute(dest);
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

                    // Zoom pas ke rute
                    map.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });
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

        map.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });
    }

    window.requestUserGPS = requestUserGPS;

    // Start GPS detection on load
    requestUserGPS();
});
</script>
@endsection
