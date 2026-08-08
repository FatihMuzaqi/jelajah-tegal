<!doctype html>
<html lang='id' data-theme='light'>
<head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'><meta name='csrf-token' content='{{ csrf_token() }}'><title>@yield('title','Dashboard') · Lokantara</title>@vite(['resources/css/app.css','resources/js/app.js'])@livewireStyles</head>
<body class='dashboard-body' data-surface='{{ $surface }}'>
<div class='mobile-backdrop' data-sidebar-close></div>
<div class='dashboard-shell' data-dashboard-shell>
 <x-sidebar :surface='$surface' />
 <div class='dashboard-main'>
  <x-topbar :surface='$surface' />
  <main class='dashboard-content' id='main-content'>
   @php($breadcrumbItems=$breadcrumbs ?? ['Dashboard'=>null])
   <x-breadcrumb :items='$breadcrumbItems' />
   <div class='page-heading'><div><p class='page-eyebrow'>{{ $surfaceLabel ?? str($surface)->headline() }}</p><h1>@yield('page-title','Dashboard')</h1><p class='page-description'>@yield('page-description','Ringkasan aktivitas dan status terbaru Lokantara.')</p></div><div class='page-actions'>@yield('page-actions')</div></div>
   @yield('content')
  </main>
 </div>
</div>
<x-toast />
<x-confirm-modal />
@stack('modals')@stack('scripts')@livewireScripts
</body></html>
