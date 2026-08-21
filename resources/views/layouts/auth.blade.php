<!doctype html>
<html lang="id" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim($__env->yieldContent('title', 'Lokantara — Portal Akses')) }}</title>
    <meta name="description"
        content="{{ trim($__env->yieldContent('meta-description', 'Masuk atau daftarkan akun Anda pada ekosistem Lokantara.')) }}">
    <meta name="robots" content="{{ trim($__env->yieldContent('robots', 'noindex,nofollow')) }}">
    <link rel="canonical" href="{{ trim($__env->yieldContent('canonical', url()->current())) }}">

    <link rel="icon" type="image/png" href="{{ asset('images/icon-192.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#15803d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Jelajah Tegal">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="auth-body">
    <main id="main-auth">
        @yield('content')
    </main>
    @livewireScripts
</body>

</html>
