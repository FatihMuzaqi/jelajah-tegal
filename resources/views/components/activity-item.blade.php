@props(['title', 'description' => null, 'time' => null, 'tone' => 'primary'])
<article class='activity-item'>
    <span class='activity-marker tone-{{ $tone }}'></span>
    <div>
        <div class='activity-heading'><strong>{{ $title }}</strong>
            @if ($time)
                <time>{{ $time }}</time>
            @endif
        </div>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </div>
</article>
