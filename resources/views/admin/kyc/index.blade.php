@extends('layouts.admin')
@section('title', 'Review KYC') @section('page-title', 'Review KYC') @section('page-description', 'Tinjau dokumen privat
tanpa mengekspos URL storage.')
@section('content')
    <x-table-wrapper title='Antrean KYC'>
        @if ($documents->isEmpty())
            <tbody>
                <tr>
                    <td><x-empty-state title='Tidak ada antrean KYC' description='Dokumen submitted akan tampil di sini.'
                            compact /></td>
                </tr>
        </tbody>@else<thead>
                <tr>
                    <th>Mitra</th>
                    <th>Dokumen</th>
                    <th>Pengirim</th>
                    <th>Dikirim</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $document)
                    <tr>
                        <td data-label='Mitra'>{{ $document->mitra->display_name }}</td>
                        <td data-label='Dokumen'>{{ str($document->document_type)->headline() }} v{{ $document->version }}
                        </td>
                        <td data-label='Pengirim'>{{ $document->submitter->name }}</td>
                        <td data-label='Dikirim'>{{ $document->created_at->format('d M Y H:i') }}</td>
                        <td data-label='Aksi'>
                            <div class='d-flex flex-wrap gap-2'><a class='btn btn-sm btn-outline-lokantara'
                                    href='{{ route('admin.kyc.download', $document) }}'>Unduh privat</a>
                                <form method='POST' action='{{ route('admin.kyc.update', $document) }}'>@csrf
                                    @method('PATCH')<input type='hidden' name='decision' value='approved'><button
                                        class='btn btn-sm btn-success'>Setujui</button></form><button
                                    class='btn btn-sm btn-outline-danger' type='button' data-bs-toggle='modal'
                                    data-bs-target='#reject-{{ $document->id }}'>Tolak</button>
                            </div>
                            <div class='modal fade' id='reject-{{ $document->id }}' tabindex='-1'>
                                <div class='modal-dialog'>
                                    <form class='modal-content lokantara-modal' method='POST'
                                        action='{{ route('admin.kyc.update', $document) }}'>@csrf @method('PATCH')<div
                                            class='modal-header'>
                                            <h2 class='modal-title fs-5'>Tolak dokumen</h2><button class='btn-close'
                                                type='button' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'><input type='hidden' name='decision'
                                                value='rejected'><x-textarea name='reason' label='Alasan penolakan'
                                                required /></div>
                                        <div class='modal-footer'><button class='btn btn-danger'>Tolak KYC</button></div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $documents->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
