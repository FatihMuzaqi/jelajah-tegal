@props(['title' => 'Belum ada data', 'description' => 'Data akan tampil ketika tersedia.', 'compact' => false, 'icon' => 'fa-regular fa-folder-open'])

<div class='empty-state {{ $compact ? 'compact' : '' }}'>
    <div class='state-illustration' aria-hidden='true'>
        <i class="{{ $icon }}" style="font-size: {{ $compact ? '22px' : '36px' }}; opacity: 0.65; color: var(--lokantara-muted);"></i>
    </div>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
    @if(trim($slot))
        <div class='state-action'>{{ $slot }}</div>
    @endif
</div>
