import './bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Livewire 4 owns the Alpine.js runtime; importing Alpine again would duplicate it.

const updateThemeIcons = (currentTheme) => {
    const isDark = currentTheme === 'dark';
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        const moonIcon = btn.querySelector('.theme-icon-moon');
        const sunIcon = btn.querySelector('.theme-icon-sun');
        const label = btn.querySelector('.theme-label-text');
        
        if (moonIcon) moonIcon.classList.toggle('d-none', isDark);
        if (sunIcon) sunIcon.classList.toggle('d-none', !isDark);
        if (label) label.textContent = isDark ? 'Terang' : 'Gelap';
    });
};

const bootDashboard = () => {
    const root = document.documentElement;
    const shell = document.querySelector('[data-dashboard-shell]');
    const theme = localStorage.getItem('lokantara-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    root.dataset.theme = theme;
    updateThemeIcons(theme);

    document.querySelectorAll('[data-theme-toggle]:not([data-dashboard-bound])').forEach((button) => {
        button.dataset.dashboardBound = 'true';
        button.addEventListener('click', () => {
            const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.dataset.theme = nextTheme;
            localStorage.setItem('lokantara-theme', nextTheme);
            updateThemeIcons(nextTheme);
        });
    });

    if (shell && localStorage.getItem('lokantara-sidebar') === 'collapsed') shell.classList.add('sidebar-collapsed');
    document.querySelectorAll('[data-sidebar-collapse]:not([data-dashboard-bound])').forEach((button) => {
        button.dataset.dashboardBound = 'true';
        button.addEventListener('click', () => {
            shell?.classList.toggle('sidebar-collapsed');
            localStorage.setItem('lokantara-sidebar', shell?.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded');
        });
    });
    const closeSidebarMobile = () => {
        shell?.classList.remove('mobile-open');
        document.body.classList.remove('sidebar-mobile-open');
        document.querySelectorAll('[data-sidebar-open]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));
    };

    const openSidebarMobile = () => {
        shell?.classList.add('mobile-open');
        document.body.classList.add('sidebar-mobile-open');
        document.querySelectorAll('[data-sidebar-open]').forEach(btn => btn.setAttribute('aria-expanded', 'true'));
    };

    document.querySelectorAll('[data-sidebar-open]:not([data-dashboard-bound])').forEach((button) => {
        button.dataset.dashboardBound = 'true';
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            if (shell?.classList.contains('mobile-open')) {
                closeSidebarMobile();
            } else {
                openSidebarMobile();
            }
        });
    });

    document.querySelectorAll('[data-sidebar-close]:not([data-dashboard-bound])').forEach((button) => {
        button.dataset.dashboardBound = 'true';
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            closeSidebarMobile();
        });
    });

    // Close mobile sidebar on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && shell?.classList.contains('mobile-open')) {
            closeSidebarMobile();
        }
    });

    const charts = [...document.querySelectorAll('[data-chart]:not([data-chart-rendered])')].filter((element) => JSON.parse(element.dataset.series || '[]').length);
    if (charts.length) {
        import('apexcharts').then(({default: ApexCharts}) => {
            requestAnimationFrame(() => {
                charts.forEach((element) => {
                    element.dataset.chartRendered = 'true';
                    const labels = JSON.parse(element.dataset.labels || '[]');
                    const series = JSON.parse(element.dataset.series || '[]');
                    new ApexCharts(element, {chart:{type:'donut',height:280,toolbar:{show:false}},labels,series,colors:['#1f7a5c','#2d8ca8','#f2a93b','#3b82f6','#dc4c64'],legend:{position:'bottom'},dataLabels:{enabled:false},stroke:{width:3,colors:[getComputedStyle(root).getPropertyValue('--lokantara-surface').trim()]}}).render();
                });
            });
        });
    }

    document.querySelectorAll('[data-file-uploader] input:not([data-dashboard-bound])').forEach((input) => {
        input.dataset.dashboardBound = 'true';
        input.addEventListener('change', () => {
            const label = input.closest('[data-file-uploader]')?.querySelector('[data-file-name]');
            if (label) label.textContent = input.files?.[0]?.name || 'Belum ada file dipilih';
        });
    });
    document.querySelectorAll('[data-confirm]:not([data-dashboard-bound])').forEach((trigger) => {
        trigger.dataset.dashboardBound = 'true';
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            const modalElement = document.getElementById('confirm-modal');
            if (!modalElement) return;
            modalElement.querySelector('[data-confirm-message]').textContent = trigger.dataset.confirm || 'Tindakan ini memerlukan konfirmasi.';
            modalElement.querySelector('[data-confirm-accept]').onclick = () => trigger.closest('form')?.requestSubmit();
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        });
    });
    document.querySelectorAll('.toast').forEach((toast) => bootstrap.Toast.getOrCreateInstance(toast).show());
};

document.addEventListener('DOMContentLoaded', bootDashboard);
document.addEventListener('livewire:navigated', bootDashboard);

// Dynamic Map Initializer using Code Splitting
window.initLokantaraMap = async (containerId, lat, lng, title, address, type = 'tourism') => {
    // Dynamically import Leaflet and its CSS (Vite will chunk this)
    const L = (await import('leaflet')).default;
    await import('leaflet/dist/leaflet.css');

    const map = L.map(containerId, {
        center: [lat, lng],
        zoom: 15,
        zoomControl: true,
        scrollWheelZoom: false,
        attributionControl: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    let iconHtml = '📍';
    let iconColors = ['#1f7a5c', '#13352c'];
    
    if (type === 'tourism') {
        iconHtml = '🏖️';
    } else if (type === 'hotel') {
        iconHtml = '🏨';
        iconColors = ['#1b634b', '#0d261e'];
    }

    const customIcon = L.divIcon({
        className: 'custom-map-pin',
        html: `<div style="background:linear-gradient(135deg,${iconColors[0]},${iconColors[1]});width:38px;height:38px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 4px 15px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;"><span style="transform:rotate(45deg);font-size:16px;color:#fff;">${iconHtml}</span></div>`,
        iconSize: [38, 38],
        iconAnchor: [19, 38],
        popupAnchor: [0, -38]
    });

    const popupContent = `<div style="font-family:inherit;padding:4px"><strong style="font-size:14px;color:${iconColors[0]};display:block;margin-bottom:4px">${title}</strong><p style="font-size:12px;color:#4a5568;margin:0 0 8px">${address}</p><a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" style="display:inline-block;background:${iconColors[0]};color:#fff;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:bold;text-decoration:none">Petunjuk Arah &rarr;</a></div>`;

    const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
    marker.bindPopup(popupContent).openPopup();
    
    setTimeout(() => map.invalidateSize(), 400);
};
