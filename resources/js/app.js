import './bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Livewire 4 owns the Alpine.js runtime; importing Alpine again would duplicate it.

const bootDashboard = () => {
    const root = document.documentElement;
    const shell = document.querySelector('[data-dashboard-shell]');
    const theme = localStorage.getItem('lokantara-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    root.dataset.theme = theme;

    document.querySelectorAll('[data-theme-toggle]:not([data-dashboard-bound])').forEach((button) => {
        button.dataset.dashboardBound = 'true';
        button.addEventListener('click', () => {
        root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('lokantara-theme', root.dataset.theme);
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
    document.querySelectorAll('[data-sidebar-open]:not([data-dashboard-bound])').forEach((button) => {
        button.dataset.dashboardBound = 'true';
        button.addEventListener('click', () => {
            shell?.classList.add('mobile-open'); button.setAttribute('aria-expanded', 'true');
        });
    });
    document.querySelectorAll('[data-sidebar-close]:not([data-dashboard-bound])').forEach((button) => {
        button.dataset.dashboardBound = 'true';
        button.addEventListener('click', () => {
            shell?.classList.remove('mobile-open'); document.querySelector('[data-sidebar-open]')?.setAttribute('aria-expanded', 'false');
        });
    });

    const charts = [...document.querySelectorAll('[data-chart]:not([data-chart-rendered])')].filter((element) => JSON.parse(element.dataset.series || '[]').length);
    if (charts.length) import('apexcharts').then(({default: ApexCharts}) => charts.forEach((element) => {
        element.dataset.chartRendered = 'true';
        const labels = JSON.parse(element.dataset.labels || '[]');
        const series = JSON.parse(element.dataset.series || '[]');
        new ApexCharts(element, {chart:{type:'donut',height:280,toolbar:{show:false}},labels,series,colors:['#1f7a5c','#2d8ca8','#f2a93b','#3b82f6','#dc4c64'],legend:{position:'bottom'},dataLabels:{enabled:false},stroke:{width:3,colors:[getComputedStyle(root).getPropertyValue('--lokantara-surface').trim()]}}).render();
    }));

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
