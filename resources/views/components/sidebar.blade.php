@props(['surface'])
<aside class='dashboard-sidebar' id='dashboard-sidebar' aria-label='Navigasi utama'>
 <div class='sidebar-brand'><a href='{{ route('post-login') }}' class='brand-mark d-flex align-items-center gap-2' aria-label='Jelajah Tegal'><img src='{{ asset('images/logo.png') }}' alt='Logo Jelajah Tegal' style='height:34px; width:auto; border-radius:6px;'><span class='brand-text fw-bold'>Jelajah Tegal</span></a><button class='icon-button sidebar-collapse' type='button' data-sidebar-collapse aria-label='Ciutkan sidebar'>‹</button></div>
 <div class='sidebar-context'><span class='context-dot'></span><div><small>Surface aktif</small><strong>{{ str($surface)->headline() }}</strong></div></div>
 <nav class='sidebar-nav' data-mobile-navigation>
  <p class='sidebar-label'>Menu utama</p>
  @foreach(config('navigation.'.$surface,[]) as $item)
   @can($item['permission'])<x-menu-item :item='$item' />@endcan
  @endforeach
 </nav>
 <div class='sidebar-footer'><span class='status-dot'></span><div><strong>Sistem aktif</strong><small>Laravel {{ app()->version() }}</small></div></div>
</aside>
