@extends('layouts.admin') @section('title', 'Voucher') @section('page-title', 'Voucher Platform') @section('page-description',
'Atur sponsor, limit, periode, dan applicability voucher.') @section('content')<div
        class="d-flex justify-content-end mb-3">
        <a class="btn btn-lokantara" href="{{ route('admin.vouchers.create') }}">Buat voucher</a>
    </div><x-table-wrapper title="Voucher">
        @if ($vouchers->isEmpty())
            <tbody>
                <tr>
                    <td><x-empty-state title="Belum ada voucher" compact /></td>
                </tr>
        </tbody>@else<tbody>
                @foreach ($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->code }}</td>
                        <td>{{ $voucher->name }}</td>
                        <td><x-status-badge :status="$voucher->status->value" /></td>
                        <td>{{ $voucher->used_count }}/{{ $voucher->usage_limit ?? '∞' }}</td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $vouchers->links() }}</x-slot:pagination>
</x-table-wrapper>@endsection
