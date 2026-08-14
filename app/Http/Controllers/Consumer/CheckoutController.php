<?php

namespace App\Http\Controllers\Consumer;

use App\Actions\Checkout\CreateCheckout;
use App\Actions\Payments\CapturePayment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Consumer\CheckoutRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function store(CheckoutRequest $r, CreateCheckout $action): RedirectResponse
    {
        $order = $action->execute($r->user(), $r->validated());
        return redirect()->route('consumer.orders.show', $order)->with('status', 'Checkout ' . $order->order_number . ' berhasil dibuat.');
    }

    public function index(Request $r): View
    {
        $orders = Order::where('user_id', $r->user()->id)->with('items')->latest()->paginate(15);
        return view('consumer.orders.index', compact('orders'));
    }

    public function show(Request $r, Order $order): View
    {
        abort_unless($order->user_id === $r->user()->id, 403);
        return view('consumer.orders.show', [
            'order' => $order->load(['items.tickets', 'payments', 'voucher'])
        ]);
    }

    public function confirmDirect(Request $request, Order $order, CapturePayment $capture): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status->value === 'pending_payment') {
            $payment = $order->payments()->first();
            if ($payment) {
                $ref = 'LOKET-' . str()->upper(str()->random(10));
                $capture->execute(
                    payment: $payment,
                    providerReference: $ref,
                    amount: (string) $order->total_amount,
                    currency: 'IDR',
                    provider: 'loket_direct'
                );
            }
        }

        return redirect()->route('consumer.orders.show', $order)->with('status', 'Pemesanan berhasil dikonfirmasi! Tiket & QR Code telah terbit.');
    }
}
