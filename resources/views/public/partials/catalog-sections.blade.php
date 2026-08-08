@php
$catalogSections = [
 ['Destinasi unggulan','tourism','Konten wisata unggulan akan tampil setelah domain Tourism dan proses publikasinya tersedia.'],
 ['Hidden gems','tourism','Hidden gems tidak diisi secara statis; kurasi menunggu katalog published.'],
 ['Event terdekat','event','Event published yang masih berlangsung akan tampil di sini.'],
 ['Penginapan','accommodation','Penginapan published dari Mitra aktif akan tampil di sini.'],
 ['Kuliner','culinary','Venue kuliner published akan tampil di sini.'],
 ['Rental','rental','Rental berada pada scope Version 2 dan belum diaktifkan.'],
 ['Produk lokal','marketplace','Marketplace berada pada scope Version 2 dan belum diaktifkan.'],
];
@endphp
@foreach($catalogSections as [$sectionTitle,$domainKey,$description])
 @php($emptyTitle=$sectionTitle.' belum tersedia')
 <section class='public-section catalog-placeholder' id='{{ str($sectionTitle)->slug() }}' aria-labelledby='heading-{{ $domainKey }}-{{ $loop->index }}'>
  <div class='container public-container'>
   <div class='section-heading'><div><p class='public-eyebrow'>Jelajahi Lokantara</p><h2 id='heading-{{ $domainKey }}-{{ $loop->index }}'>{{ $sectionTitle }}</h2></div></div>
   @if($domainKey==='accommodation' && $featuredAccommodations->isNotEmpty())
    <div class='public-card-grid'>@foreach($featuredAccommodations as $item)<article class='mitra-card'><div class='mitra-cover'><span>{{ str($item->name)->substr(0,1) }}</span></div><div class='mitra-card-body'><div class='card-meta'><span>{{ $item->region?->name }}</span><span>{{ str($item->accommodation?->property_type)->headline() }}</span></div><h3><a href='{{ route('accommodation.show',$item->slug) }}'>{{ $item->name }}</a></h3><p>{{ str($item->description)->limit(110) }}</p></div></article>@endforeach</div>
   @else
    <div class='content-card'><x-empty-state :title='$emptyTitle' :description='$description' compact /></div>
   @endif
  </div>
 </section>
@endforeach

@if($aiPlannerEnabled)
<section class='public-section'>
 <div class='container public-container'><div class='ai-cta'><div><p class='public-eyebrow'>AI Planner</p><h2>Susun perjalanan sesuai kebutuhan Anda.</h2><p>Fitur ini aktif secara terbatas berdasarkan konfigurasi platform.</p></div><a class='btn btn-light' href='{{ route('login') }}'>Mulai merencanakan</a></div></div>
</section>
@endif
