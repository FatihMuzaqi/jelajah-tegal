<!doctype html>
<html lang='id' data-theme='light'>
<head>
 <meta charset='utf-8'>
 <meta name='viewport' content='width=device-width,initial-scale=1'>
 <meta name='csrf-token' content='{{ csrf_token() }}'>
 <title>{{ trim($__env->yieldContent('title','Jelajah Tegal — Eksplorasi Potensi Lokal')) }}</title>
 <meta name='description' content='{{ trim($__env->yieldContent('meta-description','Temukan Mitra dan layanan lokal wisata, penginapan, kuliner, dan event yang telah tersedia di Jelajah Tegal.')) }}'>
 <meta name='robots' content='{{ trim($__env->yieldContent('robots','index,follow')) }}'>
 <link rel='canonical' href='{{ trim($__env->yieldContent('canonical',url()->current())) }}'>
 <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
 <meta property='og:type' content='website'>
 <meta property='og:site_name' content='Jelajah Tegal'>
 <meta property='og:title' content='{{ trim($__env->yieldContent('og-title','Jelajah Tegal — Eksplorasi Potensi Lokal')) }}'>
 <meta property='og:description' content='{{ trim($__env->yieldContent('meta-description','Temukan Mitra dan layanan lokal wisata, penginapan, kuliner, dan event yang telah tersedia di Jelajah Tegal.')) }}'>
 <meta property='og:url' content='{{ url()->current() }}'>
 <meta property='og:image' content='{{ asset('images/logo.png') }}'>
 @vite(['resources/css/app.css','resources/js/app.js'])
 @livewireStyles
</head>
<body class='public-body'>
 <a class='skip-link' href='#main-content'>Lewati ke konten</a>
 <header class='public-header'>
  <nav class='navbar navbar-expand-lg public-navbar' aria-label='Navigasi publik'>
   <div class='container public-container'>
    <a href='{{ route('home') }}' class='brand-mark public-brand d-flex align-items-center gap-2' aria-label='Jelajah Tegal beranda'>
      <img src='{{ asset('images/logo.png') }}' alt='Logo Jelajah Tegal' style='height:42px; width:auto; object-fit:contain; border-radius:8px;'>
      <span class='brand-text-title fw-bold text-dark fs-5'>Jelajah Tegal</span>
    </a>
    <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#public-navigation' aria-controls='public-navigation' aria-expanded='false' aria-label='Buka navigasi'><span class='navbar-toggler-icon'></span></button>
    <div class='collapse navbar-collapse' id='public-navigation'>
     <ul class='navbar-nav mx-auto'>
      <li class='nav-item'><a class='nav-link' href='{{ route('home') }}'>Beranda</a></li>
      <li class='nav-item'><a class='nav-link' href='{{ route('tourism.index') }}'>Wisata</a></li>
      <li class='nav-item'><a class='nav-link' href='{{ route('accommodation.index') }}'>Penginapan</a></li>
      <li class='nav-item'><a class='nav-link' href='{{ route('public.about') }}'>Tentang</a></li>
      <li class='nav-item'><a class='nav-link' href='{{ route('public.faq') }}'>FAQ</a></li>
      <li class='nav-item'><a class='nav-link' href='{{ route('public.contact') }}'>Kontak</a></li>
     </ul>
     <div class='public-nav-actions'>
      <button class='icon-button' type='button' data-theme-toggle aria-label='Ubah tema'><span aria-hidden='true'>◐</span></button>
      @guest<a class='btn btn-outline-lokantara' href='{{ route('login') }}'>Masuk</a><a class='btn btn-lokantara' href='{{ route('register') }}'>Daftar</a>@else<a class='btn btn-lokantara' href='{{ route('post-login') }}'>Dashboard</a>@endguest
     </div>
    </div>
   </div>
  </nav>
 </header>
 <main id='main-content'>@yield('content')</main>
 <footer class='public-footer'>
  <div class='container public-container footer-grid'>
   <div>
     <a href='{{ route('home') }}' class='brand-mark public-brand d-flex align-items-center gap-2 mb-2'>
       <img src='{{ asset('images/logo.png') }}' alt='Logo Jelajah Tegal' style='height:38px; width:auto; object-fit:contain; border-radius:6px;'>
       <span class='fw-bold text-dark fs-5'>Jelajah Tegal</span>
     </a>
     <p>Portal terpadu untuk menemukan layanan wisata, penginapan, kuliner, dan event lokal terbaik di wilayah Tegal.</p>
   </div>
   <div><h2>Informasi</h2><a href='{{ route('public.about') }}'>Tentang</a><a href='{{ route('public.faq') }}'>FAQ</a><a href='{{ route('public.contact') }}'>Kontak</a></div>
   <div><h2>Legal</h2><a href='{{ route('public.privacy') }}'>Kebijakan Privasi</a><a href='{{ route('public.terms') }}'>Syarat dan Ketentuan</a></div>
  </div>
  <div class='container public-container footer-bottom'><span>&copy; {{ now()->year }} Jelajah Tegal</span><span>Platform Digital Terpadu Pariwisata & Ekonomi Kreatif Tegal.</span></div>
 </footer>
 @livewireScripts
</body>
</html>
