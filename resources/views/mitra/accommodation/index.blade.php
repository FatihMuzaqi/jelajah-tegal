@extends('layouts.mitra')
@section('title','Penginapan') @section('page-title','Katalog Penginapan') @section('page-description','Kelola properti, kamar, harga, kalender, dan moderasi.')
@section('content')
<div class='d-flex justify-content-end mb-3'><a class='btn btn-lokantara' href='{{ route('mitra.accommodation.create') }}'>Tambah properti</a></div>
<x-table-wrapper title='Properti'>@if($items->isEmpty())<tbody><tr><td><x-empty-state title='Belum ada properti' description='Buat draft penginapan pertama.' compact /></td></tr></tbody>@else<thead><tr><th>Nama</th><th>Status</th><th>Diperbarui</th><th></th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->name }}</td><td><x-status-badge :status='$item->status' /></td><td>{{ $item->updated_at->diffForHumans() }}</td><td><a class='btn btn-sm btn-outline-lokantara' href='{{ route('mitra.accommodation.show',$item) }}'>Kelola</a></td></tr>@endforeach</tbody>@endif<x-slot:pagination>{{ $items->links() }}</x-slot:pagination></x-table-wrapper>
@endsection
