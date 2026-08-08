@extends('layouts.public')
@section('title',$title.' — Lokantara') @section('meta-description','Cari '.$title.' terpublikasi dari Mitra aktif Lokantara.')
@if(request()->query()) @section('robots','noindex,follow') @endif
@section('content')
<section class="public-section"><div class="container public-container"><div class="section-heading"><div><p class="public-eyebrow">{{ $title }}</p><h1>Jelajahi {{ strtolower($title) }}</h1></div><span class="result-count">{{ $items->total() }} hasil</span></div>
<form class="public-search mb-4" method="GET" action="{{ route($routePrefix.'.index') }}"><div class="search-field"><label>Cari</label><input name="q" value="{{ request('q') }}" placeholder="Nama atau deskripsi"></div><button class="btn btn-lokantara">Cari</button></form>
@if($items->isEmpty())<x-empty-state :title="$title.' belum tersedia'" description="Belum ada konten published yang sesuai filter." />@else<div class="public-card-grid">@foreach($items as $item)<article class="mitra-card"><div class="mitra-cover"><span>{{ str($item->name)->substr(0,1) }}</span></div><div class="mitra-card-body"><div class="card-meta"><span>{{ $item->region?->name }}</span><span>{{ $item->category?->name }}</span></div><h2><a href="{{ route($routePrefix.'.show',$item->slug) }}">{{ $item->name }}</a></h2><p>{{ str($item->description)->limit(120) }}</p><div class="tag-row"><span>{{ $item->rating_average }}/5</span></div></div></article>@endforeach</div><div class="public-pagination">{{ $items->links() }}</div>@endif</div></section>
@endsection
