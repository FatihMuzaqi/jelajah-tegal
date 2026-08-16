@props(['surface'])
<header class='dashboard-topbar'>
  <div class='topbar-start'>
    <button class='icon-button mobile-menu-button' type='button' data-sidebar-open aria-controls='dashboard-sidebar' aria-expanded='false' aria-label='Buka navigasi'>
      <i class="fa-solid fa-bars"></i>
    </button>
    <button class='icon-button desktop-menu-button' type='button' data-sidebar-collapse aria-label='Ubah ukuran sidebar'>
      <i class="fa-solid fa-bars-staggered"></i>
    </button>
    <div class='topbar-search'>
      <span class="d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-magnifying-glass" style="font-size: 13px;"></i></span>
      <input type='search' placeholder='Cari menu atau fitur…' aria-label='Cari menu'>
    </div>
  </div>

  <div class='topbar-actions'>
    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex align-items-center gap-2" target="_blank" title="Lihat Landing Page Jelajah Tegal">
      <i class="fa-solid fa-globe"></i>
      <span>Lihat Web</span>
    </a>

    <button class='icon-button' type='button' data-theme-toggle aria-label='Ubah tema' title='Mode Tampilan'>
      <i class="fa-solid fa-circle-half-stroke"></i>
    </button>

    @auth
    <div class='dropdown'>
      <button class='icon-button notification-trigger' data-bs-toggle='dropdown' aria-expanded='false' aria-label='Notifikasi' title='Notifikasi'>
        <i class="fa-regular fa-bell"></i>
        @if(auth()->user()?->notifications()->whereNull('read_at')->exists())
          <span class='notification-indicator'></span>
        @endif
      </button>
      <div class='dropdown-menu dropdown-menu-end notification-menu shadow-sm'>
        <div class='dropdown-heading d-flex align-items-center justify-content-between px-3 py-2'>
          <strong>Notifikasi</strong>
          <span class="badge bg-primary rounded-pill">{{ auth()->user()?->notifications()->whereNull('read_at')->count() ?? 0 }} baru</span>
        </div>
        <div class="dropdown-divider my-0"></div>
        @forelse(auth()->user()?->notifications()->latest()->limit(4)->get() ?? [] as $notification)
          <x-notification-item :notification='$notification' />
        @empty
          <div class="p-3 text-center text-muted">
            <i class="fa-regular fa-bell-slash fs-4 mb-2 d-block opacity-50"></i>
            <small>Belum ada notifikasi baru.</small>
          </div>
        @endforelse
      </div>
    </div>

    <div class='dropdown'>
      <button class='profile-trigger d-flex align-items-center gap-2' data-bs-toggle='dropdown' aria-expanded='false'>
        <span class='profile-avatar'>{{ str(auth()->user()?->name ?? 'U')->substr(0,1)->upper() }}</span>
        <span class='profile-copy text-start d-none d-sm-flex'>
          <strong>{{ auth()->user()?->name ?? 'Pengguna' }}</strong>
          <small>{{ str($surface)->headline() }}</small>
        </span>
        <i class="fa-solid fa-chevron-down ms-1 text-muted" style="font-size: 11px;"></i>
      </button>
      <div class='dropdown-menu dropdown-menu-end profile-menu shadow-sm'>
        <div class='px-3 py-2'>
          <div class="fw-bold text-dark">{{ auth()->user()?->name ?? 'Pengguna' }}</div>
          <small class='d-block text-muted text-truncate' style="max-width: 200px;">{{ auth()->user()?->email ?? '' }}</small>
          <span class="badge bg-success-subtle text-success mt-1" style="font-size: 10px;">{{ str($surface)->headline() }}</span>
        </div>
        <div class='dropdown-divider my-1'></div>
        <a class='dropdown-item d-flex align-items-center gap-2' href='{{ route('surfaces.select') }}'>
          <i class="fa-solid fa-arrows-split-up-and-left text-primary" style="width: 16px;"></i>
          <span>Ganti Surface Portal</span>
        </a>
        @if($surface === 'mitra')
          <a class='dropdown-item d-flex align-items-center gap-2' href='{{ route('mitra.profile.edit') }}'>
            <i class="fa-solid fa-store text-muted" style="width: 16px;"></i>
            <span>Profil Mitra</span>
          </a>
        @endif
        <div class='dropdown-divider my-1'></div>
        <form method='POST' action='{{ route('logout') }}'>
          @csrf
          <button class='dropdown-item text-danger d-flex align-items-center gap-2'>
            <i class="fa-solid fa-right-from-bracket" style="width: 16px;"></i>
            <span>Keluar / Logout</span>
          </button>
        </form>
      </div>
    </div>
    @endauth
  </div>
</header>
