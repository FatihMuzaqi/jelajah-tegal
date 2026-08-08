@extends('layouts.mitra')
@section('title','Wisata') @section('page-title','Katalog Wisata') @section('page-description','Kelola destinasi, publikasi, tiket, dan riwayat moderasi.')
@section('content')
<div class='d-flex justify-content-end mb-3'>@can('tourism.manage')<a class='btn btn-lokantara' href='{{ route('mitra.tourism.create') }}'>Tambah wisata</a>@endcan</div>
<x-table-wrapper title='Destinasi wisata'>@if($items->isEmpty())<tbody><tr><td><x-empty-state title='Belum ada destinasi' description='Buat draft destinasi pertama.' compact /></td></tr></tbody>@else<thead><tr><th>Nama</th><th>Status</th><th>Diperbarui</th><th></th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->name }}</td><td><x-status-badge :status='$item->status' /></td><td>{{ $item->updated_at->diffForHumans() }}</td><td><a class='btn btn-sm btn-outline-lokantara' href='{{ route('mitra.tourism.show',$item) }}'>Kelola</a></td></tr>@endforeach</tbody>@endif<x-slot:pagination>{{ $items->links() }}</x-slot:pagination></x-table-wrapper>
@endsection
