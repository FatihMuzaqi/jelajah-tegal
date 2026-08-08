<x-content-card title='Operasional bisnis' subtitle='Nilai hanya ditampilkan ketika sumber data domain sudah tersedia.' class='mt-3'>
 <div class='stats-grid'>@foreach($operational as $item)<div class='stat-card tone-muted'><div class='stat-content'><span class='stat-label'>{{ $item['label'] }}</span>@if($item['available'] && $item['value']!==null)<strong class='stat-value'>{{ $item['value'] }}</strong>@else<span class='text-muted small'>Modul belum tersedia</span>@endif</div></div>@endforeach</div>
</x-content-card>
