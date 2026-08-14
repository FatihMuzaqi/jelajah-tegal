@props(['items' => []])
<nav aria-label='Breadcrumb' class='breadcrumb-wrap'>
    <ol class='breadcrumb'>
        @foreach ($items as $label => $url)
            <li class='breadcrumb-item {{ $loop->last ? 'active' : '' }}'>
                @if (!$loop->last && $url)
                    <a href='{{ $url }}'>{{ $label }}</a>@else{{ $label }}
                @endif
            </li>
        @endforeach
    </ol>
</nav>
