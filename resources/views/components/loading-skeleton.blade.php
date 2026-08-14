@props(['rows' => 3])
<div class='loading-skeleton' role='status' aria-label='Memuat data'>
    @for ($i = 0; $i < $rows; $i++)
        <div class='skeleton-row'><span></span><span></span><span></span></div>
    @endfor
    <span class='visually-hidden'>Memuat…</span>
</div>
