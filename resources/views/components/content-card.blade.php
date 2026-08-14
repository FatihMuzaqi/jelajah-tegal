@props(['title' => null, 'subtitle' => null])
<section {{ $attributes->class(['content-card']) }}>
    @if ($title || isset($actions))
        <header class='card-header'>
            <div>
                <h2>{{ $title }}</h2>
                @if ($subtitle)
                    <p>{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div>{{ $actions }}</div>
            @endisset
        </header>
    @endif
    <div class='card-body'>{{ $slot }}</div>
</section>
