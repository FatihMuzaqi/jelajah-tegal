@php($surface = 'gatekeeper')
@php($surfaceLabel = 'Portal Gatekeeper')
<!doctype html>
<html lang='id' data-theme='light'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <meta name='csrf-token' content='{{ csrf_token() }}'>
    <title>@yield('title', 'Gatekeeper Dashboard') · Jelajah Tegal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class='dashboard-body' data-surface='{{ $surface }}'>
    <div class='mobile-backdrop' data-sidebar-close></div>
    <div class='dashboard-shell' data-dashboard-shell>
        <x-sidebar :surface='$surface' />
        <div class='dashboard-main'>
            <x-topbar :surface='$surface' />
            <main class='dashboard-content' id='main-content'>
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
                @yield('content')
            </main>
        </div>
    </div>
    <x-toast />
    <x-confirm-modal />
    @stack('modals')@stack('scripts')@livewireScripts
</body>
</html>
