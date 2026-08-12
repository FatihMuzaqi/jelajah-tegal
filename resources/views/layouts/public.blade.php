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
 <!-- Icon Libraries: Font Awesome 6 & Bootstrap Icons -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
 @vite(['resources/css/app.css','resources/js/app.js'])
 @livewireStyles
</head>
<body class='public-body'>
 <a class='skip-link' href='#main-content'>Lewati ke konten</a>
 <header class='public-header sticky-top bg-white border-bottom shadow-sm'>
  <nav class='navbar navbar-expand-lg public-navbar py-2' aria-label='Navigasi publik'>
   <div class='container public-container'>
    <a href='{{ route('home') }}' class='brand-mark public-brand d-flex align-items-center gap-2 text-decoration-none' aria-label='Jelajah Tegal beranda'>
      <img src='{{ asset('images/logo.png') }}' alt='Logo Jelajah Tegal' style='height:42px; width:auto; object-fit:contain; border-radius:8px;'>
      <div class="d-flex flex-column">
        <span class='brand-text-title fw-extrabold text-dark fs-5 lh-1'>Jelajah Tegal</span>
        <small class="text-muted fw-semibold" style="font-size: 10px; letter-spacing: 0.05em;">Jelajah • Nikmati • Kenali</small>
      </div>
    </a>
    <button class='navbar-toggler border-0 p-2 shadow-none' type='button' data-bs-toggle='collapse' data-bs-target='#public-navigation' aria-controls='public-navigation' aria-expanded='false' aria-label='Buka navigasi'><span class='navbar-toggler-icon'></span></button>
    <div class='collapse navbar-collapse' id='public-navigation'>
      <ul class='navbar-nav mx-auto gap-1 gap-lg-2 fw-semibold my-2 my-lg-0'>
       <li class='nav-item'>
         <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('home') ? 'active text-emerald fw-bold' : '' }}' href='{{ route('home') }}'>Beranda</a>
       </li>
       <li class='nav-item'>
         <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('tourism.*') ? 'active text-emerald fw-bold' : '' }}' href='{{ route('tourism.index') }}'>Wisata</a>
       </li>
       <li class='nav-item'>
         <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('accommodation.*') ? 'active text-emerald fw-bold' : '' }}' href='{{ route('accommodation.index') }}'>Penginapan</a>
       </li>
       <li class='nav-item'>
         <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('culinary.*') ? 'active text-emerald fw-bold' : '' }}' href='{{ route('culinary.index') }}'>Kuliner</a>
       </li>
       <li class='nav-item'>
         <a class='nav-link px-3 py-2 rounded-3 {{ request()->routeIs('event.*') ? 'active text-emerald fw-bold' : '' }}' href='{{ route('event.index') }}'>Event</a>
       </li>
       <li class='nav-item dropdown'>
         <a class='nav-link dropdown-toggle px-3 py-2 rounded-3 d-flex align-items-center gap-1' href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
             Informasi
         </a>
         <ul class='dropdown-menu border-0 shadow-sm rounded-3 mt-1'>
           <li><a class='dropdown-item py-2 fs-7' href='{{ route('public.about') }}'><i class="fa-solid fa-circle-info text-success me-2"></i>Tentang</a></li>
           <li><a class='dropdown-item py-2 fs-7' href='{{ route('public.faq') }}'><i class="fa-solid fa-circle-question text-info me-2"></i>FAQ</a></li>
           <li><a class='dropdown-item py-2 fs-7' href='{{ route('public.contact') }}'><i class="fa-solid fa-envelope text-warning me-2"></i>Kontak</a></li>
         </ul>
       </li>
      </ul>
     <div class='public-nav-actions d-flex align-items-center gap-2'>
      <div class="d-flex align-items-center justify-content-between w-100 d-lg-none mb-2">
        <span class="fs-8 text-muted fw-bold text-uppercase">Ubah Tema</span>
        <button class='icon-button' type='button' data-theme-toggle aria-label='Ubah tema'><span aria-hidden='true'>◐</span></button>
      </div>
      <button class='icon-button me-2 d-none d-lg-inline-flex' type='button' data-theme-toggle aria-label='Ubah tema'><span aria-hidden='true'>◐</span></button>
      @guest
        <a class='btn btn-outline-dark rounded-pill px-4 fw-bold text-center py-2' href='{{ route('login') }}'>
            <i class="fa-regular fa-user me-1"></i> Masuk
        </a>
        <a class='btn btn-emerald rounded-pill px-4 fw-bold text-white text-center py-2' style="background: #047857;" href='{{ route('register') }}'>Daftar</a>
      @else
        <a class='btn btn-emerald rounded-pill px-4 fw-bold text-white text-center py-2' style="background: #047857;" href='{{ route('post-login') }}'>Dashboard</a>
      @endguest
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
