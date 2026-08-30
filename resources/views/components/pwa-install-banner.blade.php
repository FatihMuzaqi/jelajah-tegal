<!-- PWA Android / Chromium / Desktop Install Prompt Banner -->
<div id="pwa-install-prompt" class="pwa-install-card d-none" role="dialog" aria-modal="true" aria-label="Pasang Aplikasi Jelajah Tegal">
    <div class="pwa-card-content">
        <div class="pwa-icon-box">
            <img src="{{ asset('images/icon-192.png') }}" alt="Logo Jelajah Tegal" class="pwa-app-logo">
        </div>
        <div class="pwa-text-box">
            <strong class="pwa-title">Pasang Aplikasi Jelajah Tegal</strong>
            <p class="pwa-desc">Akses e-tiket & peta wisata lebih cepat langsung dari layar utama HP Anda.</p>
        </div>
        <button type="button" class="pwa-close-btn" id="pwa-dismiss-btn" aria-label="Tutup Banner">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="pwa-actions-row">
        <button type="button" class="pwa-btn-later" id="pwa-later-btn">Nanti</button>
        <button type="button" class="pwa-btn-install" id="pwa-install-btn">
            <i class="fa-solid fa-download me-1"></i> Pasang
        </button>
    </div>
</div>

<!-- iOS Safari Manual Install Prompt Banner -->
<div id="pwa-ios-prompt" class="pwa-install-card d-none" role="dialog" aria-modal="true" aria-label="Pasang Aplikasi di iPhone">
    <div class="pwa-card-content">
        <div class="pwa-icon-box">
            <img src="{{ asset('images/icon-192.png') }}" alt="Logo Jelajah Tegal" class="pwa-app-logo">
        </div>
        <div class="pwa-text-box">
            <strong class="pwa-title">Pasang di iPhone</strong>
            <p class="pwa-desc">Tekan tombol <i class="fa-solid fa-arrow-up-from-bracket text-primary"></i> <strong>Share</strong> di Safari, lalu pilih <strong>"Tambah ke Layar Utama"</strong>.</p>
        </div>
        <button type="button" class="pwa-close-btn" id="pwa-ios-dismiss-btn" aria-label="Tutup Banner">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>

<style>
/* PWA Install Banner Styling */
.pwa-install-card {
    position: fixed;
    bottom: calc(76px + env(safe-area-inset-bottom, 8px));
    left: 16px;
    right: 16px;
    max-width: 480px;
    margin: 0 auto;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 14px 16px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.14), 0 4px 6px rgba(0, 0, 0, 0.04);
    z-index: 1050;
    animation: pwaSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes pwaSlideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pwa-card-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    position: relative;
}

.pwa-icon-box {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 12px;
    overflow: hidden;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    flex-shrink: 0;
}

.pwa-app-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.pwa-text-box {
    flex: 1;
    padding-right: 20px;
}

.pwa-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    display: block;
    line-height: 1.3;
    margin-bottom: 2px;
}

.pwa-desc {
    font-size: 11.5px;
    color: #64748b;
    margin: 0;
    line-height: 1.4;
}

.pwa-close-btn {
    position: absolute;
    top: -2px;
    right: -2px;
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 14px;
    cursor: pointer;
    padding: 4px;
}
.pwa-close-btn:hover {
    color: #0f172a;
}

.pwa-actions-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
}

.pwa-btn-later {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 8px;
    cursor: pointer;
}

.pwa-btn-install {
    background: #15803d;
    color: #ffffff;
    border: none;
    font-size: 12.5px;
    font-weight: 700;
    padding: 6px 16px;
    border-radius: 99px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(21, 128, 61, 0.3);
    transition: background 0.15s ease;
}

.pwa-btn-install:hover {
    background: #166534;
}
</style>

<script>
(function() {
    let deferredPrompt = null;
    const banner = document.getElementById('pwa-install-prompt');
    const iosBanner = document.getElementById('pwa-ios-prompt');
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');
    const laterBtn = document.getElementById('pwa-later-btn');
    const iosDismissBtn = document.getElementById('pwa-ios-dismiss-btn');

    // Check if user is ALREADY in installed PWA standalone mode
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                         window.navigator.standalone === true || 
                         document.referrer.includes('android-app://');

    if (isStandalone) {
        // App is already installed and running in standalone window -> do NOT show banner
        return;
    }

    // Check if dismissed within the last 24 hours
    const dismissedAt = localStorage.getItem('jt-pwa-dismissed-at');
    const isDismissedRecently = dismissedAt && (Date.now() - parseInt(dismissedAt, 10)) < (24 * 60 * 60 * 1000);

    // Detect iOS Safari
    const isIos = () => {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return /iphone|ipad|ipod/.test(userAgent);
    };

    const isSafari = () => {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return isIos() && userAgent.includes('safari') && !userAgent.includes('crios');
    };

    // 1. Capture Chromium beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;

        if (!isDismissedRecently && banner) {
            banner.classList.remove('d-none');
        }
    });

    // 2. Reliable Auto-Display for Mobile Browsers
    window.addEventListener('DOMContentLoaded', () => {
        if (isDismissedRecently) return;

        setTimeout(() => {
            if (isSafari()) {
                if (iosBanner) iosBanner.classList.remove('d-none');
            } else {
                if (banner && banner.classList.contains('d-none')) {
                    banner.classList.remove('d-none');
                }
            }
        }, 2000);
    });

    function dismissBanner() {
        if (banner) banner.classList.add('d-none');
        if (iosBanner) iosBanner.classList.add('d-none');
        localStorage.setItem('jt-pwa-dismissed-at', Date.now().toString());
    }

    if (dismissBtn) dismissBtn.addEventListener('click', dismissBanner);
    if (laterBtn) laterBtn.addEventListener('click', dismissBanner);
    if (iosDismissBtn) iosDismissBtn.addEventListener('click', dismissBanner);

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                banner.classList.add('d-none');
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    localStorage.removeItem('jt-pwa-dismissed-at');
                }
                deferredPrompt = null;
            } else {
                // Friendly guide if native prompt is deferred by browser
                alert(' Cara Pasang Aplikasi:\n1. Tekan tombol menu titik tiga (⋮) di kanan atas browser Chrome.\n2. Pilih "Pasang aplikasi" atau "Tambahkan ke Layar Utama".');
            }
        });
    }

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((reg) => {
                    console.log('Jelajah Tegal PWA SW active:', reg.scope);
                })
                .catch((err) => {
                    console.warn('SW registration error:', err);
                });
        });
    }
})();
</script>
