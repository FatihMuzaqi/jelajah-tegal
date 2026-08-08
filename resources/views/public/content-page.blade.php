@extends('layouts.public')
@section('title',$title.' — Lokantara')
@section('meta-description',$content['summary'] ?? $emptyMessage)
@section('content')
@php($emptyTitle=$title.' belum tersedia')
<section class='public-page-hero'>
 <div class='container public-container'><p class='public-eyebrow'>Lokantara</p><h1>{{ $title }}</h1>@if($content && ! empty($content['summary']))<p>{{ $content['summary'] }}</p>@endif</div>
</section>
<section class='public-section'>
 <div class='container public-container public-document'>
  @if(! $content)
   <x-empty-state :title='$emptyTitle' :description='$emptyMessage' />
  @elseif(! empty($content['items']))
   <div class='document-list'>@foreach($content['items'] as $item)<article><h2>{{ $item['title'] ?? $item['question'] ?? '' }}</h2><p>{{ $item['body'] ?? $item['answer'] ?? '' }}</p></article>@endforeach</div>
  @elseif(! empty($content['paragraphs']))
   <div class='document-list'>@foreach($content['paragraphs'] as $paragraph)<p>{{ $paragraph }}</p>@endforeach</div>
  @else
   <x-empty-state :title='$emptyTitle' description='Konten published belum memiliki isi yang dapat ditampilkan.' />
  @endif
 </div>
</section>
@endsection
