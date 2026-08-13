@props(['label', 'value' => null, 'tone' => 'primary', 'caption' => null, 'icon' => null])

@php
$autoIcon = match(strtolower(trim($label))) {
    'kelengkapan profil' => 'fa-solid fa-id-badge',
    'status kyc' => 'fa-solid fa-shield-check',
    'fitur aktif' => 'fa-solid fa-layer-group',
    'anggota aktif' => 'fa-solid fa-users',
    'pengguna' => 'fa-solid fa-users-gear',
    'mitra aktif' => 'fa-solid fa-handshake',
    'kyc menunggu' => 'fa-solid fa-clock-rotate-left',
    'request fitur' => 'fa-solid fa-sliders',
    'role' => 'fa-solid fa-user-shield',
    'permission' => 'fa-solid fa-key',
    'feature flag aktif' => 'fa-solid fa-toggle-on',
    'mitra' => 'fa-solid fa-store',
    'assignment aktif' => 'fa-solid fa-qrcode',
    'pesanan', 'pesanan aktif' => 'fa-solid fa-receipt',
    'total saldo', 'saldo' => 'fa-solid fa-wallet',
    default => null
};
$finalIcon = $icon ?? $autoIcon;
@endphp

<article class="stat-card tone-{{ $tone }}">
    <div class="stat-icon" aria-hidden="true">
        @if($finalIcon)
            <i class="{{ $finalIcon }}"></i>
        @else
            {{ strtoupper(substr($label, 0, 1)) }}
        @endif
    </div>
    <div class="stat-copy">
        <span>{{ $label }}</span>
        @if($value === null)
            <strong>—</strong>
        @else
            <strong>{{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}</strong>
        @endif
        @if($caption)
            <small>{{ $caption }}</small>
        @endif
    </div>
</article>
