@extends('layouts.mitra')

@section('title', 'Voucher Promo')
@section('page-title', 'Voucher Promo & Diskon')
@section('page-description', 'Kelola kode kupon diskon dan promosi khusus untuk layanan Anda di Jelajah Tegal.')

@section('page-actions')
    <a class="btn btn-lokantara rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" href="{{ route('mitra.vouchers.create') }}">
        <i class="fa-solid fa-plus"></i>
        <span>Buat Voucher</span>
    </a>
@endsection

@section('content')
    <x-table-wrapper title="Daftar Voucher Promo">
        @if ($vouchers->isEmpty())
            <tbody>
                <tr>
                    <td>
                        <x-empty-state title="Belum ada voucher promo" description="Buat voucher diskon pertama untuk meningkatkan penjualan layanan Anda." compact />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Kode Kupon</th>
                    <th>Nama Program</th>
                    <th>Status</th>
                    <th>Penggunaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vouchers as $voucher)
                    <tr>
                        <td>
                            <span class="badge text-bg-light border fs-6 font-monospace">
                                <i class="fa-solid fa-ticket text-primary me-1"></i>
                                {{ $voucher->code }}
                            </span>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $voucher->name }}</strong>
                        </td>
                        <td><x-status-badge :status="$voucher->status->value" /></td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border">
                                <i class="fa-solid fa-chart-simple me-1"></i>
                                {{ $voucher->used_count }} / {{ $voucher->usage_limit ?? '∞' }} Kupon Terpakai
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $vouchers->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
