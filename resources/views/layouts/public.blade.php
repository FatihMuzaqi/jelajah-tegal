<!doctype html>
<html lang='id' data-theme='light'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <meta name='csrf-token' content='{{ csrf_token() }}'>
    <title>{{ trim($__env->yieldContent('title', 'Jelajah Tegal — Eksplorasi Potensi Lokal')) }}</title>
    <meta name='description'
        content='{{ trim($__env->yieldContent('meta-description', 'Temukan Mitra dan layanan lokal wisata, penginapan, kuliner, dan event yang telah tersedia di Jelajah Tegal.')) }}'>
    <meta name='robots' content='{{ trim($__env->yieldContent('robots', 'index,follow')) }}'>
    <link rel='canonical' href='{{ trim($__env->yieldContent('canonical', url()->current())) }}'>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#15803d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Jelajah Tegal">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <meta property='og:type' content='website'>
    <meta property='og:site_name' content='Jelajah Tegal'>
    <meta property='og:title'
        content='{{ trim($__env->yieldContent('og-title', 'Jelajah Tegal — Eksplorasi Potensi Lokal')) }}'>
    <meta property='og:description'
        content='{{ trim($__env->yieldContent('meta-description', 'Temukan Mitra dan layanan lokal wisata, penginapan, kuliner, dan event yang telah tersedia di Jelajah Tegal.')) }}'>
    <meta property='og:url' content='{{ url()->current() }}'>
    <meta property='og:image' content='{{ asset('images/logo.png') }}'>
    <!-- Anti-Flicker & Theme Synchronization Script -->
    <script>
        (function() {
            const theme = localStorage.getItem('lokantara-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.dataset.theme = theme;
        })();
    </script>

    <!-- Icon Libraries: Font Awesome 6 & Bootstrap Icons -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        .public-header {
            min-height: 72px;
            background: #ffffff !important;
            contain: layout;
        }
        .public-navbar {
            min-height: 72px;
        }
        .public-navbar .nav-link {
            transition: color 0.15s ease, background-color 0.15s ease !important;
        }
    </style>
</head>

<body class='public-body'>
    <a class='skip-link' href='#main-content'>Lewati ke konten</a>
    <header class='public-header sticky-top bg-white border-bottom shadow-sm'>
        <nav class='navbar navbar-expand-lg public-navbar py-2' aria-label='Navigasi publik'>
            <div class='container public-container'>
                <a href='{{ route('home') }}'
                    class='brand-mark public-brand d-flex align-items-center gap-2 text-decoration-none'
                    aria-label='Jelajah Tegal beranda'>
                    <img src='{{ asset('images/logo.png') }}' alt='Logo Jelajah Tegal' width="42" height="42"
                        style='height:42px; width:42px; object-fit:contain; border-radius:8px;' fetchpriority="high">
                    <div class="d-flex flex-column">
                        <span class='brand-text-title fw-extrabold text-dark fs-5 lh-1'>Jelajah Tegal</span>
                        <small class="text-muted fw-semibold" style="font-size: 10px; letter-spacing: 0.05em;">Jelajah •
                            Nikmati • Kenali</small>
                    </div>
                </a>
                <button class='navbar-toggler border-0 p-2 shadow-none' type='button' data-bs-toggle='collapse'
                    data-bs-target='#public-navigation' aria-controls='public-navigation' aria-expanded='false'
                    aria-label='Buka navigasi'><span class='navbar-toggler-icon'></span></button>
                <div class='collapse navbar-collapse' id='public-navigation'>
                    <ul class='navbar-nav mx-auto gap-1 gap-lg-2 fw-semibold my-2 my-lg-0'>
                        <li class='nav-item'>
                            <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('home') ? 'active text-emerald fw-bold' : '' }}'
                                href='{{ route('home') }}'>Beranda</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('tourism.*') ? 'active text-emerald fw-bold' : '' }}'
                                href='{{ route('tourism.index') }}'>Wisata</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('accommodation.*') ? 'active text-emerald fw-bold' : '' }}'
                                href='{{ route('accommodation.index') }}'>Penginapan</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('culinary.*') ? 'active text-emerald fw-bold' : '' }}'
                                href='{{ route('culinary.index') }}'>Kuliner</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('event.*') ? 'active text-emerald fw-bold' : '' }}'
                                href='{{ route('event.index') }}'>Event</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('rental.*') ? 'active text-emerald fw-bold' : '' }}'
                                href='{{ route('rental.index') }}'>Rental</a>
                        </li>
                        <li class='nav-item dropdown'>
                            <a class='nav-link dropdown-toggle px-3 py-2 rounded-3 d-flex align-items-center gap-1'
                                href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                Informasi
                            </a>
                            <ul class='dropdown-menu border-0 shadow-sm rounded-3 mt-1'>
                                <li><a class='dropdown-item py-2 fs-7' href='{{ route('public.about') }}'><i
                                            class="fa-solid fa-circle-info text-success me-2"></i>Tentang</a></li>
                                <li><a class='dropdown-item py-2 fs-7' href='{{ route('public.faq') }}'><i
                                            class="fa-solid fa-circle-question text-info me-2"></i>FAQ</a></li>
                                <li><a class='dropdown-item py-2 fs-7' href='{{ route('public.contact') }}'><i
                                            class="fa-solid fa-envelope text-warning me-2"></i>Kontak</a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class='public-nav-actions d-flex align-items-center gap-2'>
                        <div class="d-flex align-items-center justify-content-between w-100 d-lg-none mb-2">
                            <span class="fs-8 text-muted fw-bold text-uppercase">Ubah Tema</span>
                            <button class='icon-button' type='button' data-theme-toggle aria-label='Ubah tema'><span
                                    aria-hidden='true'>◐</span></button>
                        </div>
                        <button class='icon-button me-2 d-none d-lg-inline-flex' type='button' data-theme-toggle
                            aria-label='Ubah tema'><span aria-hidden='true'>◐</span></button>
                        @guest
                            <a class='btn btn-outline-dark rounded-pill px-4 fw-bold text-center py-2'
                                href='{{ route('login') }}'>
                                <i class="fa-regular fa-user me-1"></i> Masuk
                            </a>
                            <a class='btn btn-emerald rounded-pill px-4 fw-bold text-white text-center py-2'
                                style="background: #047857;" href='{{ route('register') }}'>Daftar</a>
                        @else
                            <div class="dropdown">
                                <button
                                    class="d-flex align-items-center justify-content-center p-0 rounded-circle border-0 bg-transparent"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                    title="{{ auth()->user()->name }}" aria-label="Menu Akun {{ auth()->user()->name }}">
                                    <span
                                        class="d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                        style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #047857 0%, #10b981 100%); font-size: 15px; border: 2px solid #ffffff; box-shadow: 0 2px 8px rgba(4,120,87,0.25);">
                                        {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2"
                                    style="border-radius: 18px; min-width: 240px; font-size: 13px; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;">
                                    <li class="px-3 py-2.5 mb-1 rounded-3" style="background: #f8fafc;">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-flex align-items-center justify-content-center text-white fw-bold rounded-circle flex-shrink-0"
                                                style="width: 32px; height: 32px; background: #047857; font-size: 13px;">
                                                {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                                            </span>
                                            <div class="text-truncate">
                                                <strong class="d-block text-dark text-truncate" style="font-size: 13px;">{{ auth()->user()->name }}</strong>
                                                <small class="text-muted d-block text-truncate" style="font-size: 11px;">{{ auth()->user()->email }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center gap-2" href="{{ route('consumer.trip-navigator.index') }}">
                                            <i class="fa-solid fa-map-location-dot" style="width: 16px; color: #4f46e5;"></i>
                                            <span>Rute Destinasi Terbayar</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center gap-2" href="{{ route('consumer.orders.index') }}">
                                            <i class="fa-solid fa-ticket text-emerald" style="width: 16px;"></i>
                                            <span>Pesanan & Tiket Saya</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center gap-2" href="{{ route('post-login') }}">
                                            <i class="fa-solid fa-chart-pie text-primary" style="width: 16px;"></i>
                                            <span>Dashboard Portal</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center gap-2" href="{{ route('surfaces.select') }}">
                                            <i class="fa-solid fa-arrows-split-up-and-left text-warning" style="width: 16px;"></i>
                                            <span>Ganti Portal</span>
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-2 px-3 rounded-2 text-danger fw-bold d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-right-from-bracket" style="width: 16px;"></i>
                                                <span>Keluar</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>
    </header>

    {{-- Floating Toast Notification (Tidak Mengganggu Navbar) --}}
    @if (session('status') || session('success') || session('error') || session('info'))
        <div class="position-fixed jt-floating-toast-wrap">
            <div class="toast show border-0 shadow-lg rounded-4 overflow-hidden w-100" role="alert" aria-live="assertive" aria-atomic="true" id="floating-alert-toast" style="background: #ffffff;">
                <div class="d-flex align-items-center p-3 border-start border-4 {{ session('error') ? 'border-danger' : 'border-success' }}">
                    <div class="me-3 fs-4 {{ session('error') ? 'text-danger' : 'text-success' }}">
                        <i class="fa-solid {{ session('error') ? 'fa-circle-exclamation' : 'fa-circle-check' }}"></i>
                    </div>
                    <div class="flex-grow-1 pe-2">
                        <strong class="d-block text-dark fs-7" style="font-weight: 700;">
                            {{ session('error') ? 'Pemberitahuan' : 'Berhasil' }}
                        </strong>
                        <div class="text-muted fs-8" style="line-height: 1.4;">
                            {{ session('status') ?? session('success') ?? session('error') ?? session('info') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close ms-auto flex-shrink-0" data-bs-dismiss="toast" aria-label="Close" style="font-size: 10px;"></button>
                </div>
            </div>
        </div>
        <style>
            .jt-floating-toast-wrap {
                top: 85px;
                right: 24px;
                z-index: 1060;
                max-width: 420px;
                animation: slideInToast 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            }
            @media (max-width: 576px) {
                .jt-floating-toast-wrap {
                    top: 70px;
                    right: 12px;
                    left: 12px;
                    max-width: none;
                }
            }
            @keyframes slideInToast {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        </style>
        <script>
            setTimeout(function() {
                const toastEl = document.getElementById('floating-alert-toast');
                if (toastEl) {
                    toastEl.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    toastEl.style.opacity = '0';
                    toastEl.style.transform = 'translateY(-10px)';
                    setTimeout(() => toastEl.remove(), 500);
                }
            }, 4500);
        </script>
    @endif
    <main id='main-content'>@yield('content')</main>
    <footer class='public-footer'>
        <div class='container public-container footer-grid'>
            <div>
                <a href='{{ route('home') }}' class='brand-mark public-brand d-flex align-items-center gap-2 mb-2'>
                    <img src='{{ asset('images/logo.png') }}' alt='Logo Jelajah Tegal'
                        style='height:38px; width:auto; object-fit:contain; border-radius:6px;'>
                    <span class='fw-bold text-dark fs-5'>Jelajah Tegal</span>
                </a>
                <p>Portal terpadu untuk menemukan layanan wisata, penginapan, kuliner, dan event lokal terbaik di
                    wilayah Tegal.</p>
            </div>
            <div>
                <h2>Informasi</h2><a href='{{ route('public.mitra.index') }}'>Direktori Mitra</a><a href='{{ route('public.about') }}'>Tentang</a><a
                    href='{{ route('public.faq') }}'>FAQ</a><a href='{{ route('public.contact') }}'>Kontak</a>
            </div>
            <div>
                <h2>Legal</h2><a href='{{ route('public.privacy') }}'>Kebijakan Privasi</a><a
                    href='{{ route('public.terms') }}'>Syarat dan Ketentuan</a>
            </div>
        </div>
        <div class='container public-container footer-bottom'><span>&copy; {{ now()->year }} Jelajah
                Tegal</span><span>Platform Digital Terpadu Pariwisata & Ekonomi Kreatif Tegal.</span></div>
    </footer>
    <x-chatbot-widget />
    <x-consumer-bottom-nav />
    <x-pwa-install-banner />
    @stack('modals')
    @stack('scripts')
    @livewireScripts
</body>

</html>
