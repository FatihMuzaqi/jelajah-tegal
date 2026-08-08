@extends('layouts.admin')
@section('title','Mitra') @section('page-title','Mitra') @section('page-description','Onboarding dan status tenant Lokantara.')
@section('page-actions')<a class='btn btn-lokantara' href='{{ route('admin.mitras.create') }}'>Buat Mitra</a>@endsection
@section('content')
<x-table-wrapper title='Daftar Mitra'>
 @if($mitras->isEmpty())<tbody><tr><td><x-empty-state title='Belum ada Mitra' description='Mitra baru dapat dibuat melalui tombol Buat Mitra.' compact /></td></tr></tbody>@else
 <thead><tr><th>Nama</th><th>Lokasi</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead><tbody>@foreach($mitras as $mitra)<tr><td data-label='Nama'><strong>{{ $mitra->display_name }}</strong><small class='d-block text-muted'>{{ $mitra->legal_name }}</small></td><td data-label='Lokasi'>{{ $mitra->region?->name ?? '—' }}</td><td data-label='Status'><x-status-badge :status='$mitra->status' /></td><td data-label='Dibuat'>{{ $mitra->created_at->format('d M Y') }}</td><td data-label='Aksi'><div class='d-flex gap-2'>@if($mitra->status!=='active')<form method='POST' action='{{ route('admin.mitras.status',$mitra) }}'>@csrf @method('PATCH')<input type='hidden' name='status' value='active'><button class='btn btn-sm btn-success'>Aktifkan</button></form>@else<button class='btn btn-sm btn-outline-danger' data-bs-toggle='modal' data-bs-target='#suspend-{{ $mitra->id }}'>Suspend</button>@endif</div><div class='modal fade' id='suspend-{{ $mitra->id }}' tabindex='-1'><div class='modal-dialog'><form class='modal-content lokantara-modal' method='POST' action='{{ route('admin.mitras.status',$mitra) }}'>@csrf @method('PATCH')<div class='modal-header'><h2 class='modal-title fs-5'>Suspend Mitra</h2><button class='btn-close' type='button' data-bs-dismiss='modal'></button></div><div class='modal-body'><input type='hidden' name='status' value='suspended'><x-textarea name='reason' label='Alasan suspend' required /></div><div class='modal-footer'><button class='btn btn-danger'>Suspend</button></div></form></div></div></td></tr>@endforeach</tbody>
 @endif
 <x-slot:pagination>{{ $mitras->links() }}</x-slot:pagination>
</x-table-wrapper>
@endsection
