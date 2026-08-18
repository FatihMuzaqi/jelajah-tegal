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

    public function index(Request $r): View|RedirectResponse
    {
        if ($orderNumber = $r->query('order_id')) {
            $matched = Order::where('order_number', $orderNumber)->orWhere('id', $orderNumber)->first();
            if ($matched && $matched->user_id === $r->user()->id) {
                return redirect()->route('consumer.orders.show', $matched);
            }
        }

        $user = $r->user();
        $invoices = \App\Models\Invoice::where('user_id', $user->id)
            ->with(['orders.items.tickets', 'orders.mitra'])
            ->latest()
            ->get();

        $standaloneOrders = Order::where('user_id', $user->id)
            ->whereNull('invoice_id')
            ->with(['items.tickets', 'mitra'])
            ->latest()
            ->get();

        return view('consumer.orders.index', compact('invoices', 'standaloneOrders'));
    }

    public function show(Request $r, Order $order, \App\Services\Payments\MidtransClient $midtrans, \App\Actions\Payments\ProcessMidtransNotification $orderProcessor): View
    {
        abort_unless($order->user_id === $r->user()->id, 403);

        if ($order->status->value === 'pending_payment') {
            try {
                $statusPayload = $midtrans->status($order->order_number);
                $status = strtolower((string) ($statusPayload['transaction_status'] ?? ''));
                $fraud = strtolower((string) ($statusPayload['fraud_status'] ?? 'accept'));
                if ((in_array($status, ['settlement', 'capture']) && $fraud === 'accept') || in_array($status, ['expire', 'cancel', 'deny'])) {
                    $orderProcessor->execute($statusPayload, 'view_sync', false);
                    $order->refresh();
                }
            } catch (\Throwable $e) {
                // proceed
            }
        }

        return view('consumer.orders.show', [
            'order' => $order->load(['items.tickets', 'payments', 'voucher'])
        ]);
    }

    public function confirmDirect(Request $request, Order $order, CapturePayment $capture): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_if(config('midtrans.production'), 403, 'Konfirmasi manual dinonaktifkan di mode production.');

        if ($order->status->value === 'pending_payment') {
            $payment = $order->payments()->first();
            if ($payment) {
                $ref = 'TEST-' . str()->upper(str()->random(10));
                $capture->execute(
                    payment: $payment,
                    providerReference: $ref,
                    amount: (string) $order->total_amount,
                    currency: 'IDR',
                    provider: 'test_direct'
                );
            }
        }

        return redirect()->route('consumer.orders.show', $order)->with('status', 'Pemesanan berhasil dikonfirmasi! Tiket & QR Code telah terbit.');
    }
}
