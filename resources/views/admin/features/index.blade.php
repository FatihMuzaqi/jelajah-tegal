@extends('layouts.admin')
@section('title', 'Request Fitur') @section('page-title', 'Permintaan Fitur Mitra') @section('page-description',
'Setujui atau tolak aktivasi layanan dengan alasan keputusan dan audit.')
@section('content')
    <x-table-wrapper title='Permintaan fitur'>
        @if ($requests->isEmpty())
            <tbody>
                <tr>
                    <td><x-empty-state title='Belum ada permintaan'
                            description='Permintaan fitur dari Mitra akan tampil di sini.' compact /></td>
                </tr>
        </tbody>@else<thead>
                <tr>
                    <th>Mitra</th>
                    <th>Fitur</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $item)
                    <tr>
                        <td data-label='Mitra'>{{ $item->mitra->display_name }}</td>
                        <td data-label='Fitur'>{{ $item->serviceType->name }}</td>
                        <td data-label='Alasan'>{{ str($item->reason)->limit(120) }}</td>
                        <td data-label='Status'><x-status-badge :status='$item->status' /></td>
                        <td data-label='Aksi'>
                            @if ($item->status === 'requested')
                                <div class='d-flex flex-wrap gap-2'>
                                    <form method='POST' action='{{ route('admin.features.update', $item) }}'>@csrf
                                        @method('PATCH')<input type='hidden' name='decision' value='approved'><button
                                            class='btn btn-sm btn-success'>Setujui</button></form><button
                                        class='btn btn-sm btn-outline-danger' data-bs-toggle='modal'
                                        data-bs-target='#feature-reject-{{ $item->id }}'>Tolak</button>
                                </div>
                                <div class='modal fade' id='feature-reject-{{ $item->id }}' tabindex='-1'>
                                    <div class='modal-dialog'>
                                        <form class='modal-content lokantara-modal' method='POST'
                                            action='{{ route('admin.features.update', $item) }}'>@csrf @method('PATCH')<div
                                                class='modal-header'>
                                                <h2 class='modal-title fs-5'>Tolak fitur</h2><button class='btn-close'
                                                    type='button' data-bs-dismiss='modal'></button>
                                            </div>
                                            <div class='modal-body'><input type='hidden' name='decision'
                                                    value='rejected'><x-textarea name='reason' label='Alasan keputusan'
                                                    required /></div>
                                            <div class='modal-footer'><button class='btn btn-danger'>Tolak
                                                    permintaan</button></div>
                                        </form>
                                    </div>
                                </div>@else{{ $item->review_note ?? 'Sudah diputuskan' }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $requests->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
