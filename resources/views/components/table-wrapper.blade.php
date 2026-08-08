@props(['title'=>null])
<x-content-card :title='$title' {{ $attributes }}>@isset($filters)<div class='table-filters'>{{ $filters }}</div>@endisset<div class='table-responsive'><table class='table lokantara-table'>{{ $slot }}</table></div>@isset($pagination)<div class='table-pagination'>{{ $pagination }}</div>@endisset</x-content-card>
