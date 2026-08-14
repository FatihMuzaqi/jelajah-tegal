@extends('layouts.consumer')
@section('title', $order->order_number)
@section('page-title', $order->order_number)
@section('page-description', 'Snapshot transaksi yang tidak berubah mengikuti harga katalog.')
@section('content')
    <div class="content-card">
        <dl class="row">
            <dt class="col-4">Status</dt>
            <dd class="col-8"><x-status-badge :status="$order->status->value" /></dd>
            <dt class="col-4">Subtotal</dt>
            <dd class="col-8">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</dd>
            <dt class="col-4">Diskon</dt>
            <dd class="col-8">Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</dd>
            <dt class="col-4">Admin fee</dt>
            <dd class="col-8">Rp {{ number_format($order->admin_fee, 0, ',', '.') }}</dd>
            <dt class="col-4">Total</dt>
            <dd class="col-8"><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></dd>
        </dl>
        @if ($order->status->value === 'pending_payment' && config('midtrans.enabled'))
            <form method="POST" action="{{ route('consumer.orders.payment.snap', $order) }}">@csrf<button
                    class="btn btn-lokantara">Bayar dengan Midtrans</button></form>
        @elseif($order->status->value === 'pending_payment')
            <div class="alert alert-warning">Payment online sedang dinonaktifkan.</div>
        @endif
    </div>
    @foreach ($order->items as $item)
        <div class="content-card mt-3">
            <h2>{{ $item->item_name }}</h2>
            <p>{{ $item->resource_type }} · {{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}
            </p>
            @foreach ($item->tickets as $ticket)
                <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                    <span>{{ $ticket->ticket_code }} · <x-status-badge :status="$ticket->status" /></span>
                    @if (in_array($ticket->status, ['unused', 'active'], true))
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('consumer.tickets.qr', $ticket) }}"
                            target="_blank">Tampilkan QR</a>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
