@props(['title'=>'Belum ada data','description'=>'Data akan tampil ketika tersedia.','compact'=>false])
<div class='empty-state {{ $compact ? 'compact' : '' }}'><div class='state-illustration' aria-hidden='true'>◇</div><h3>{{ $title }}</h3><p>{{ $description }}</p>@if(trim($slot))<div class='state-action'>{{ $slot }}</div>@endif</div>
