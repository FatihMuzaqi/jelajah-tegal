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
    @php
      $unreadNotificationsCount = auth()->user()?->notifications()->whereNull('read_at')->count() ?? 0;
      $notificationsList = auth()->user()?->notifications()->latest()->limit(10)->get() ?? collect();
    @endphp
    <div class='dropdown'>
      <button class='icon-button notification-trigger' data-bs-toggle='dropdown' aria-expanded='false' aria-label='Notifikasi' title='Notifikasi'>
        <i class="fa-regular fa-bell"></i>
        @if($unreadNotificationsCount > 0)
          <span class='notification-indicator'></span>
        @endif
      </button>
      <div class='dropdown-menu dropdown-menu-end notification-menu shadow-lg p-0 border' style="width: 360px; max-width: 92vw; border-radius: 16px; border-color: #e2e8f0 !important; overflow: hidden;">
        <!-- Header -->
        <div class='d-flex align-items-center justify-content-between px-3.5 py-3 border-bottom' style="background: #ffffff; border-color: #f1f5f9 !important;">
          <div class="d-flex align-items-center gap-2">
            <strong class="text-dark" style="font-size: 14.5px;">Notifikasi</strong>
            @if($unreadNotificationsCount > 0)
              <span class="badge rounded-pill px-2 py-0.5" style="background: #eff6ff; color: #2563eb; font-size: 11px; font-weight: 600;">
                {{ $unreadNotificationsCount }} baru
              </span>
            @endif
          </div>
          @if($notificationsList->isNotEmpty())
            <form method="POST" action="{{ route('notifications.clear-all') }}" onsubmit="return confirm('Bersihkan semua notifikasi?');">
              @csrf
              <button type="submit" class="btn btn-sm btn-link p-0 text-muted text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 11.5px;">
                <i class="fa-regular fa-trash-can" style="font-size: 11px;"></i>
                <span>Hapus Semua</span>
              </button>
            </form>
          @endif
        </div>

        <!-- Notification List -->
        <div class="notification-list-scroll" style="max-height: 380px; overflow-y: auto;">
          @forelse($notificationsList as $notification)
            <x-notification-item :notification='$notification' />
          @empty
            <div class="py-4 px-3 text-center text-muted">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 44px; height: 44px; background: #f8fafc; color: #94a3b8; font-size: 18px;">
                <i class="fa-regular fa-bell-slash"></i>
              </div>
              <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;">Tidak Ada Notifikasi</h6>
              <p class="small text-muted mb-0" style="font-size: 11.5px;">Semua informasi dan pembaruan akan tampil di sini.</p>
            </div>
          @endforelse
        </div>

        <!-- Footer -->
        @if($notificationsList->isNotEmpty())
          <div class="px-3 py-2 text-center border-top" style="background: #f8fafc; border-color: #f1f5f9 !important;">
            <span class="text-muted" style="font-size: 11px;">
              <i class="fa-solid fa-arrow-pointer me-1 text-primary"></i> Klik item untuk langsung menuju halaman & menghapus
            </span>
          </div>
        @endif
      </div>
    </div>

    <div class='dropdown'>
      <button class='profile-trigger d-flex align-items-center gap-2' data-bs-toggle='dropdown' aria-expanded='false'>
        @if(auth()->user()?->profile?->avatar)
          <img src="{{ asset('storage/' . auth()->user()->profile->avatar->object_key) }}" alt="Avatar" class="profile-avatar object-fit-cover">
        @else
          <span class='profile-avatar'>{{ str(auth()->user()?->name ?? 'U')->substr(0,1)->upper() }}</span>
        @endif
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
            <span>Profil & Keamanan Mitra</span>
          </a>
        @elseif($surface === 'consumer')
          <a class='dropdown-item d-flex align-items-center gap-2' href='{{ route('consumer.profile.edit') }}'>
            <i class="fa-solid fa-user-gear text-muted" style="width: 16px;"></i>
            <span>Profil & Keamanan</span>
          </a>
        @elseif($surface === 'gatekeeper')
          <a class='dropdown-item d-flex align-items-center gap-2' href='{{ route('gatekeeper.profile.edit') }}'>
            <i class="fa-solid fa-user-shield text-muted" style="width: 16px;"></i>
            <span>Profil & Keamanan</span>
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
