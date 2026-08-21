@props(['surface'])
<aside class='dashboard-sidebar' id='dashboard-sidebar' aria-label='Navigasi utama'>
  <div class='sidebar-brand'>
    <a href='{{ route('post-login') }}' class='brand-mark d-flex align-items-center gap-2 text-decoration-none' aria-label='Jelajah Tegal'>
      <img src='{{ asset('images/logo.png') }}' alt='Logo Jelajah Tegal' style='height:36px; width:auto; border-radius:8px;'>
      <div class="d-flex flex-column">
        <span class='brand-text fw-bold text-dark lh-sm' style="font-size: 15px;">Jelajah Tegal</span>
        <small class="text-muted" style="font-size: 10px; font-weight: 500;">Multi-Service Platform</small>
      </div>
    </a>
    <button class='icon-button sidebar-collapse d-none d-lg-flex' type='button' data-sidebar-collapse aria-label='Ciutkan sidebar'>‹</button>
    <button class='icon-button sidebar-mobile-close d-lg-none' type='button' data-sidebar-close aria-label='Tutup navigasi'>
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>
  
  <div class='sidebar-context'>
    <span class='context-dot'></span>
    <div>
      <small>Surface aktif</small>
      <strong>{{ str($surface)->headline() }}</strong>
    </div>
  </div>

  <nav class='sidebar-nav' data-mobile-navigation>
    @foreach(config('navigation.'.$surface, []) as $section)
      @if(isset($section['category']))
        <div class="sidebar-category-header">
          <span class="sidebar-category-label">{{ $section['category'] }}</span>
        </div>
        @foreach($section['items'] as $item)
          @can($item['permission'] ?? 'access.'.$surface)
            <x-menu-item :item='$item' />
          @endcan
        @endforeach
      @else
        @can($section['permission'] ?? 'access.'.$surface)
          <x-menu-item :item='$section' />
        @endcan
      @endif
    @endforeach
  </nav>

  <div class='sidebar-footer'>
    <span class='status-dot'></span>
    <div>
      <strong>Sistem aktif</strong>
      <small>Jelajah Tegal &middot; Monolith v2.0</small>
    </div>
  </div>
</aside>
