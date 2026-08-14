@props(['item'])
@php($href = isset($item['route']) ? route($item['route']) : $item['href'] ?? '#')
@php($active = isset($item['route']) && (request()->routeIs($item['route']) || (isset($item['active_pattern']) && request()->is($item['active_pattern']))))

<a href="{{ $href }}" class="sidebar-link {{ $active ? 'active' : '' }}"
    @if ($active) aria-current="page" @endif>
    <span class="menu-icon" aria-hidden="true">
        @if (!empty($item['icon']))
            <i class="{{ $item['icon'] }}"></i>
        @else
            {{ strtoupper(substr($item['label'], 0, 1)) }}
        @endif
    </span>
    <span class="menu-label">{{ $item['label'] }}</span>
    @if (!empty($item['badge']))
        <span class="badge rounded-pill ms-auto bg-primary" style="font-size: 10px;">{{ $item['badge'] }}</span>
    @endif
</a>
