@extends('layouts.'.$surface)
@section('title',str($surface)->headline().' Dashboard')
@section('page-title','Dashboard')
@section('page-description',$mitra ? 'Ringkasan operasional '.$mitra->display_name.'.' : 'Ringkasan data aktual sesuai akses Anda.')
@section('content')
<div class='stats-grid'>
 @foreach($stats as $stat)
  @php($statLabel=$stat['label']) @php($statValue=$stat['value']) @php($statTone=$stat['tone'])
  <x-stat-card :label='$statLabel' :value='$statValue' :tone='$statTone' />
 @endforeach
</div>
@php($chartLabels=$chart['labels'] ?? []) @php($chartSeries=$chart['series'] ?? [])
<div class='dashboard-grid'>
 <x-chart-card title='Distribusi data' chart-id='surface-overview-chart' :labels='$chartLabels' :series='$chartSeries' />
 <x-content-card title='Aktivitas terbaru' subtitle='Data aktual yang tercatat pada sistem.'>
  @if($activity->isEmpty())
   <x-empty-state title='Belum ada aktivitas' description='Aktivitas baru akan tampil otomatis di area ini.' compact />
  @else
   <x-timeline>@foreach($activity as $item)
    @php($activityTitle=$item instanceof \App\Models\DatabaseNotification ? data_get($item->data,'title','Notifikasi') : str($item->event)->replace('.',' ')->headline())
    @php($activityDescription=$item instanceof \App\Models\DatabaseNotification ? data_get($item->data,'message') : ($item->auditable_type ? class_basename($item->auditable_type) : null))
    @php($activityTime=$item->created_at?->diffForHumans())
    <x-activity-item :title='$activityTitle' :description='$activityDescription' :time='$activityTime' />
   @endforeach</x-timeline>
  @endif
 </x-content-card>
</div>
@include('dashboards.tables.'.$surface)
@if($surface==='mitra') @include('dashboards.partials.mitra-operational',['operational'=>$operational]) @endif
@endsection
