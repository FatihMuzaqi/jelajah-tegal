@php
    $isHome = request()->routeIs('home');
    $isMap = request()->routeIs('consumer.trip-navigator.*', 'tourism.*', 'accommodation.*', 'culinary.*', 'event.*', 'rental.*');
    $isAiTrip = request()->routeIs('tour-assistant.*');
    $isTickets = request()->routeIs('consumer.orders.*', 'consumer.itineraries.*', 'consumer.tickets.*');
    $isAccount = request()->routeIs('consumer.dashboard', 'consumer.profile.*', 'consumer.renter-documents.*', 'login', 'register');
@endphp

<nav class="consumer-bottom-nav d-lg-none" aria-label="Navigasi Bawah Mobile">
    <div class="bottom-nav-container">
        <!-- 1. Beranda -->
        <a href="{{ route('home') }}" class="bottom-nav-item {{ $isHome ? 'active' : '' }}" aria-label="Beranda">
            <div class="nav-icon-wrap">
                <i class="fa-solid fa-house"></i>
            </div>
            <span class="nav-label">Beranda</span>
        </a>

        <!-- 2. Peta Destinasi -->
        <a href="{{ auth()->check() ? route('consumer.trip-navigator.index') : route('tourism.index') }}" class="bottom-nav-item {{ $isMap ? 'active' : '' }}" aria-label="Peta Destinasi">
            <div class="nav-icon-wrap">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <span class="nav-label">Peta Destinasi</span>
        </a>

        <!-- 3. AI Planner (Center Highlight) -->
        <a href="{{ route('tour-assistant.index') }}" class="bottom-nav-item nav-center-highlight {{ $isAiTrip ? 'active' : '' }}" aria-label="AI Planner">
            <div class="center-icon-btn">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <span class="nav-label">AI Trip</span>
        </a>

        <!-- 4. Tiket Saya -->
        <a href="{{ auth()->check() ? route('consumer.orders.index') : route('login') }}" class="bottom-nav-item {{ $isTickets ? 'active' : '' }}" aria-label="Tiket Saya">
            <div class="nav-icon-wrap">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <span class="nav-label">Tiket Saya</span>
        </a>

        <!-- 5. Akun / Dashboard -->
        <a href="{{ auth()->check() ? route('consumer.dashboard') : route('login') }}" class="bottom-nav-item {{ $isAccount ? 'active' : '' }}" aria-label="Akun Saya">
            <div class="nav-icon-wrap">
                <i class="fa-solid fa-user"></i>
            </div>
            <span class="nav-label">Akun</span>
        </a>
    </div>
</nav>

<style>
/* Consumer Mobile Bottom Navigation Bar */
.consumer-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1040;
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-top: 1px solid rgba(226, 232, 240, 0.85);
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
    padding-bottom: max(env(safe-area-inset-bottom, 8px), 8px);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.bottom-nav-container {
    display: flex;
    align-items: center;
    justify-content: space-around;
    height: 60px;
    max-width: 600px;
    margin: 0 auto;
    padding: 0 8px;
}

.bottom-nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #64748b;
    padding: 4px 0;
    transition: all 0.15s ease;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    position: relative;
}

.bottom-nav-item:active {
    transform: scale(0.92);
}

.nav-icon-wrap {
    font-size: 19px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s ease, transform 0.15s ease;
}

.nav-label {
    font-size: 10px;
    font-weight: 600;
    margin-top: 2px;
    letter-spacing: -0.01em;
    line-height: 1.2;
    transition: color 0.15s ease;
    white-space: nowrap;
    text-align: center;
}

/* Active State */
.bottom-nav-item.active {
    color: #15803d;
}

.bottom-nav-item.active .nav-icon-wrap {
    color: #15803d;
    transform: translateY(-1px);
}

.bottom-nav-item.active .nav-label {
    color: #15803d;
    font-weight: 700;
}

.bottom-nav-item.active::after {
    content: '';
    position: absolute;
    top: 2px;
    width: 16px;
    height: 3px;
    border-radius: 99px;
    background: #15803d;
}

/* Center Highlight Button (AI Planner) */
.nav-center-highlight {
    position: relative;
    top: -12px;
}

.center-icon-btn {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
    border: 3px solid #ffffff;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
}

.nav-center-highlight:active .center-icon-btn {
    transform: scale(0.92);
}

.nav-center-highlight.active .center-icon-btn {
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.55);
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.nav-center-highlight .nav-label {
    margin-top: 2px;
    font-weight: 700;
    color: #059669;
}

.nav-center-highlight::after {
    display: none !important;
}

/* Add bottom padding to body on mobile so content is not obscured */
@media (max-width: 991px) {
    body {
        padding-bottom: calc(72px + env(safe-area-inset-bottom, 0px)) !important;
    }
}
</style>
