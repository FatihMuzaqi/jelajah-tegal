@php($surface = 'consumer')
@php($surfaceLabel = 'Portal Consumer')
<!doctype html>
<html lang='id' data-theme='light'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <meta name='csrf-token' content='{{ csrf_token() }}'>
    <title>@yield('title', 'Consumer Dashboard') · Jelajah Tegal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#15803d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Jelajah Tegal">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <script>
        (function() {
            const theme = localStorage.getItem('lokantara-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.dataset.theme = theme;
        })();
    </script>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Mobile & Desktop UX Enhancements for Consumer Panel */
        @media (max-width: 991.98px) {
            .dashboard-content {
                padding-bottom: 96px !important; /* Ensures fixed bottom-nav never overlaps cards, forms, or buttons */
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
            .page-heading {
                margin-bottom: 16px !important;
            }
            .page-heading h1 {
                font-size: 1.45rem !important;
            }
            .page-description {
                font-size: 12px !important;
            }
        }
        @media (min-width: 992px) {
            .dashboard-content {
                padding: 28px 36px 48px !important;
            }
            .page-heading {
                margin-bottom: 22px;
            }
        }
    </style>
</head>
<body class='dashboard-body' data-surface='{{ $surface }}'>
    <div class='mobile-backdrop' data-sidebar-close></div>
    <div class='dashboard-shell' data-dashboard-shell>
        <x-sidebar :surface='$surface' />
        <div class='dashboard-main'>
            <x-topbar :surface='$surface' />
            <main class='dashboard-content' id='main-content'>
                @if(!request()->routeIs('consumer.dashboard'))
                    @php($breadcrumbItems = $breadcrumbs ?? ['Dashboard' => null])
                    <x-breadcrumb :items='$breadcrumbItems' />
                    <div class='page-heading'>
                        <div>
                            <p class='page-eyebrow'>{{ $surfaceLabel ?? str($surface)->headline() }}</p>
                            <h1>@yield('page-title', 'Dashboard')</h1>
                            <p class='page-description'>@yield('page-description', 'Ringkasan aktivitas dan status terbaru Lokantara.')</p>
                        </div>
                        <div class='page-actions'>@yield('page-actions')</div>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <x-toast />
    <x-confirm-modal />
    <x-consumer-bottom-nav />
    <x-pwa-install-banner />
    @stack('modals')@stack('scripts')@livewireScripts
</body>
</html>
