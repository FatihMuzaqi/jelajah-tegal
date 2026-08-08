@extends('layouts.public')
@section('title','Lokantara — Temukan Potensi Lokal')
@section('meta-description','Cari Mitra aktif, lokasi, dan layanan lokal yang tersedia di Lokantara.')
@if(request()->hasAny(['q','region','service'])) @section('robots','noindex,follow') @endif
@section('content')
<section class='landing-hero'>
 <div class='hero-orb hero-orb-one'></div><div class='hero-orb hero-orb-two'></div>
 <div class='container public-container hero-content'>
  <p class='public-eyebrow'>Jelajahi potensi lokal Indonesia</p>
  <h1>Temukan pengalaman lokal dalam satu tempat.</h1>
  <p class='hero-lead'>Cari Mitra, lokasi, dan layanan yang benar-benar tersedia pada platform Lokantara.</p>
  <form class='public-search' method='GET' action='{{ route('home') }}' role='search'>
   <div class='search-field search-query'><label for='public-search'>Apa yang Anda cari?</label><input id='public-search' name='q' value='{{ $filters['q'] ?? '' }}' maxlength='100' placeholder='Nama Mitra atau lokasi'></div>
   <div class='search-field'><label for='public-region'>Lokasi</label><select id='public-region' name='region'><option value=''>Semua lokasi</option>@foreach($regions as $region)<option value='{{ $region->id }}' @selected(($filters['region'] ?? null)==$region->id)>{{ $region->name }}</option>@endforeach</select></div>
   <div class='search-field'><label for='public-service'>Layanan</label><select id='public-service' name='service'><option value=''>Semua layanan</option>@foreach($services as $service)<option value='{{ $service->code }}' @selected(($filters['service'] ?? null)===$service->code)>{{ $service->name }}</option>@endforeach</select></div>
   <button class='btn btn-lokantara search-submit' type='submit'>Cari sekarang</button>
  </form>
  @if($errors->any())<div class='search-error' role='alert'>Filter tidak valid. Periksa kembali pilihan pencarian Anda.</div>@endif
 </div>
</section>

<section class='public-section service-section' aria-labelledby='service-heading'>
 <div class='container public-container'>
  <div class='section-heading'><div><p class='public-eyebrow'>Kategori layanan</p><h2 id='service-heading'>Mulai dari yang Anda butuhkan</h2></div></div>
  @if($services->isEmpty())
   <x-empty-state title='Kategori belum tersedia' description='Kategori layanan akan tampil setelah master data diterbitkan.' />
  @else
   <div class='service-grid'>@foreach($services as $service)<a class='service-tile' href='{{ route('home',['service'=>$service->code]) }}'><span class='service-monogram'>{{ str($service->name)->substr(0,1) }}</span><strong>{{ $service->name }}</strong><small>{{ $categories->where('service_type_id',$service->id)->count() }} kategori aktif</small></a>@endforeach</div>
  @endif
 </div>
</section>

<section class='public-section search-results' aria-labelledby='result-heading'>
 <div class='container public-container'>
  <div class='section-heading'><div><p class='public-eyebrow'>Direktori publik</p><h2 id='result-heading'>{{ request()->hasAny(['q','region','service']) ? 'Hasil pencarian' : 'Mitra yang tersedia' }}</h2></div><span class='result-count'>{{ $mitras->total() }} hasil</span></div>
  @if($mitras->isEmpty())
   <x-empty-state title='Belum ada hasil' description='Tidak ada Mitra aktif dan disetujui yang cocok dengan filter saat ini.' />
  @else
   <div class='public-card-grid'>
    @foreach($mitras as $mitra)
     <article class='mitra-card'>
      <div class='mitra-cover'><span>{{ str($mitra->display_name)->substr(0,1)->upper() }}</span></div>
      <div class='mitra-card-body'><div class='card-meta'><span>{{ $mitra->region?->name ?? 'Lokasi belum dicantumkan' }}</span><span class='verified-label'>Aktif</span></div><h3>{{ $mitra->display_name }}</h3><p>{{ str($mitra->description ?: 'Profil layanan sedang dilengkapi.')->limit(120) }}</p><div class='tag-row'>@forelse($mitra->features as $feature)<span>{{ $feature->serviceType->name }}</span>@empty<span>Layanan belum dicantumkan</span>@endforelse</div></div>
     </article>
    @endforeach
   </div>
   <div class='public-pagination'>{{ $mitras->links() }}</div>
  @endif
 </div>
</section>

@include('public.partials.catalog-sections')
@include('public.partials.platform-sections')
@endsection
