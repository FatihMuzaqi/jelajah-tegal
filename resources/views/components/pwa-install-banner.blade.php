<div id="pwa-install-prompt" class="pwa-install-card d-none" role="dialog" aria-modal="true" aria-label="Pasang Aplikasi Jelajah Tegal">
    <div class="pwa-card-content">
        <div class="pwa-icon-box">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="pwa-app-logo">
        </div>
        <div class="pwa-text-box">
            <strong class="pwa-title">Pasang Aplikasi Jelajah Tegal</strong>
            <p class="pwa-desc">Akses tiket & panduan wisata offline lebih cepat langsung dari layar utama HP Anda.</p>
        </div>
        <button type="button" class="pwa-close-btn" id="pwa-dismiss-btn" aria-label="Tutup Banner">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="pwa-actions-row">
        <button type="button" class="pwa-btn-later" id="pwa-later-btn">Nanti Saja</button>
        <button type="button" class="pwa-btn-install" id="pwa-install-btn">
            <i class="fa-solid fa-download me-1"></i> Pasang Aplikasi
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
    padding: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 4px 6px rgba(0, 0, 0, 0.04);
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
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
}

.pwa-app-logo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pwa-text-box {
    flex: 1;
    padding-right: 24px;
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

.pwa-actions-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
}

.pwa-btn-later {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 12.5px;
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
    padding: 7px 16px;
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
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');
    const laterBtn = document.getElementById('pwa-later-btn');

    // Check if user previously dismissed within 7 days
    const dismissedAt = localStorage.getItem('jt-pwa-dismissed-at');
    const isDismissedRecently = dismissedAt && (Date.now() - parseInt(dismissedAt, 10)) < (7 * 24 * 60 * 60 * 1000);

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;

        // Check if not standalone and not recently dismissed
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        if (!isStandalone && !isDismissedRecently && banner) {
            setTimeout(() => {
                banner.classList.remove('d-none');
            }, 3000); // Display 3 seconds after page load for smooth UX
        }
    });

    function dismissBanner() {
        if (banner) {
            banner.classList.add('d-none');
            localStorage.setItem('jt-pwa-dismissed-at', Date.now().toString());
        }
    }

    if (dismissBtn) dismissBtn.addEventListener('click', dismissBanner);
    if (laterBtn) laterBtn.addEventListener('click', dismissBanner);

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            banner.classList.add('d-none');
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                localStorage.removeItem('jt-pwa-dismissed-at');
            }
            deferredPrompt = null;
        });
    }

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((reg) => {
                    console.log('Jelajah Tegal PWA ServiceWorker registered with scope:', reg.scope);
                })
                .catch((err) => {
                    console.warn('ServiceWorker registration failed:', err);
                });
        });
    }
})();
</script>
