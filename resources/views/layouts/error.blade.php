<!doctype html>
<html lang="id" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Terjadi Kesalahan') — Jelajah Tegal</title>
    <meta name="robots" content="noindex,nofollow">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; font-family: 'Inter', sans-serif;">
    <div style="width: 100%; max-width: 520px; text-align: center;">
        <div class="mb-4">
            <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Jelajah Tegal" style="width: 44px; height: 44px; object-fit: contain;">
                <span class="fw-extrabold text-dark fs-4 font-outfit" style="letter-spacing: -0.5px;">JELAJAH TEGAL</span>
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white text-center">
            @yield('content')
        </div>
    </div>
</body>

</html>
