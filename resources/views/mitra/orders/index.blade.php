@extends('layouts.mitra')

@section('title', 'Pesanan Masuk')
@section('page-title', 'Pesanan Masuk & Transaksi')
@section('page-description', 'Pantau seluruh transaksi pesanan tiket, reservasi, dan sewa layanan dari wisatawan.')

@section('content')
    <x-table-wrapper title="Daftar Transaksi Pesanan">
        @if ($orders->isEmpty())
            <tbody>
                <tr>
                    <td><x-empty-state title="Belum ada pesanan masuk" description="Pesanan dari wisatawan akan muncul secara realtime di tabel ini." compact /></td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Layanan & Item</th>
                    <th>Total Pembayaran</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('mitra.orders.show', $order) }}" class="fw-bold text-decoration-none">
                                <i class="fa-solid fa-receipt text-primary me-1"></i>
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $order->items->first()?->item_name ?: 'Layanan Wisata' }}</strong>
                            @if($order->items->count() > 1)
                                <small class="text-muted d-block">+{{ $order->items->count() - 1 }} item lainnya</small>
                            @endif
                        </td>
                        <td class="fw-bold text-success">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td><x-status-badge :status="$order->status->value" /></td>
                        <td>
                            <small class="text-muted d-inline-flex align-items-center gap-1">
                                <i class="fa-regular fa-clock" style="font-size: 11px;"></i>
                                {{ $order->created_at?->diffForHumans() }}
                            </small>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('mitra.orders.show', $order) }}" class="btn btn-sm btn-outline-lokantara rounded-pill px-3 py-1 fw-semibold" style="font-size: 12px;">
                                <i class="fa-solid fa-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $orders->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
