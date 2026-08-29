@props(['notification'])

@php
    $type = strtolower($notification->type ?? '');
    $data = $notification->data ?? [];
    
    // Icon, background color, and text color determination
    if (str_contains($type, 'kyc')) {
        $icon = 'fa-solid fa-id-card';
        $iconBg = '#eff6ff';
        $iconColor = '#2563eb';
    } elseif (str_contains($type, 'tourism') || str_contains($type, 'wisata')) {
        $icon = 'fa-solid fa-umbrella-beach';
        $iconBg = '#ecfdf5';
        $iconColor = '#059669';
    } elseif (str_contains($type, 'accommodation') || str_contains($type, 'penginapan') || str_contains($type, 'hotel')) {
        $icon = 'fa-solid fa-hotel';
        $iconBg = '#f0f9ff';
        $iconColor = '#0284c7';
    } elseif (str_contains($type, 'culinary') || str_contains($type, 'kuliner')) {
        $icon = 'fa-solid fa-utensils';
        $iconBg = '#fffbeb';
        $iconColor = '#d97706';
    } elseif (str_contains($type, 'event')) {
        $icon = 'fa-solid fa-calendar-check';
        $iconBg = '#fef2f2';
        $iconColor = '#dc2626';
    } elseif (str_contains($type, 'rental')) {
        $icon = 'fa-solid fa-car';
        $iconBg = '#f5f3ff';
        $iconColor = '#7c3aed';
    } elseif (str_contains($type, 'feature')) {
        $icon = 'fa-solid fa-wand-magic-sparkles';
        $iconBg = '#faf5ff';
        $iconColor = '#9333ea';
    } elseif (str_contains($type, 'withdrawal')) {
        $icon = 'fa-solid fa-wallet';
        $iconBg = '#ecfdf5';
        $iconColor = '#10b981';
    } elseif (str_contains($type, 'bank')) {
        $icon = 'fa-solid fa-building-columns';
        $iconBg = '#f1f5f9';
        $iconColor = '#475569';
    } elseif (str_contains($type, 'order') || str_contains($type, 'payment') || str_contains($type, 'ticket')) {
        $icon = 'fa-solid fa-receipt';
        $iconBg = '#eff6ff';
        $iconColor = '#3b82f6';
    } else {
        $icon = 'fa-solid fa-bell';
        $iconBg = '#f8fafc';
        $iconColor = '#64748b';
    }

    // Friendly Title
    $rawTitle = data_get($data, 'title');
    if ($rawTitle && !str_contains(strtolower($rawTitle), 'submitted')) {
        if (strtolower($rawTitle) === 'kyc baru') {
            $title = 'Verifikasi Dokumen KYC';
        } else {
            $title = $rawTitle;
        }
    } else {
        if (str_contains($type, 'kyc')) {
            $title = 'Verifikasi Dokumen KYC';
        } elseif (str_contains($type, 'tourism')) {
            $title = 'Pengajuan Wisata Baru';
        } elseif (str_contains($type, 'accommodation')) {
            $title = 'Pengajuan Penginapan Baru';
        } elseif (str_contains($type, 'culinary')) {
            $title = 'Pengajuan Kuliner Baru';
        } elseif (str_contains($type, 'event')) {
            $title = 'Pengajuan Event Baru';
        } elseif (str_contains($type, 'rental')) {
            $title = 'Pengajuan Rental Baru';
        } elseif (str_contains($type, 'feature')) {
            $title = 'Permintaan Fitur Baru';
        } elseif (str_contains($type, 'withdrawal')) {
            $title = 'Pengajuan Penarikan Saldo';
        } else {
            $title = str($notification->type)->replace('.', ' ')->replace('_', ' ')->headline();
        }
    }

    // Friendly Message
    $message = data_get($data, 'message');
    if (!$message) {
        if (!empty($data['name'])) {
            $message = 'Mitra mengajukan publikasi untuk "' . $data['name'] . '".';
        } elseif (str_contains($type, 'tourism')) {
            $message = 'Mitra mengajukan tinjauan destinasi wisata baru.';
        } elseif (str_contains($type, 'accommodation')) {
            $message = 'Mitra mengajukan tinjauan data penginapan/hotel.';
        } elseif (str_contains($type, 'culinary')) {
            $message = 'Mitra mengajukan tinjauan resto atau kuliner.';
        } elseif (str_contains($type, 'event')) {
            $message = 'Mitra mengajukan tinjauan agenda event wisata.';
        } elseif (str_contains($type, 'rental')) {
            $message = 'Mitra mengajukan tinjauan armada rental.';
        } elseif (str_contains($type, 'feature')) {
            $message = 'Mitra mengajukan usulan fitur tambahan.';
        } else {
            $message = 'Pembaruan sistem Jelajah Tegal tersedia.';
        }
    }
@endphp

<div class="notification-item-wrapper position-relative {{ $notification->read_at ? 'read' : 'unread' }}" style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease;">
    <a href="{{ route('notifications.read', $notification->id) }}" class="d-flex align-items-start gap-3 px-3.5 py-3 text-decoration-none text-reset notification-link w-100" style="transition: background-color 0.15s ease;">
        <!-- Notification Category Icon -->
        <div class="notification-icon-box flex-shrink-0 d-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 38px; height: 38px; background: {{ $iconBg }}; color: {{ $iconColor }}; font-size: 15px;">
            <i class="{{ $icon }}"></i>
        </div>

        <!-- Notification Content -->
        <div class="notification-content-box flex-grow-1 min-w-0 pe-2">
            <div class="d-flex align-items-center justify-content-between gap-1 mb-0.5">
                <span class="notification-title fw-bold text-dark text-truncate d-block" style="font-size: 13px; line-height: 1.3;">
                    {{ $title }}
                </span>
                @if(!$notification->read_at)
                    <span class="notification-unread-dot flex-shrink-0 rounded-circle" style="width: 7px; height: 7px; background: #0d9488;"></span>
                @endif
            </div>

            <p class="notification-desc text-secondary mb-1 line-clamp-2" style="font-size: 12px; line-height: 1.4; color: #475569; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $message }}
            </p>

            <div class="d-flex align-items-center gap-1.5 text-muted" style="font-size: 11px;">
                <i class="fa-regular fa-clock" style="font-size: 10px;"></i>
                <span>{{ $notification->created_at?->diffForHumans() }}</span>
            </div>
        </div>
    </a>

    <!-- Quick Dismiss / Delete Button -->
    <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="position-absolute" style="top: 8px; right: 10px; z-index: 5;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn p-0 border-0 bg-transparent text-muted notification-dismiss-btn" title="Hapus notifikasi" aria-label="Hapus" style="font-size: 13px; width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; opacity: 0.4; transition: all 0.15s ease;">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </form>
</div>
