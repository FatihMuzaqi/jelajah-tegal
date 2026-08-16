<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransClient
{
    public function __construct(private MidtransConfiguration $configuration) {}

    public function createSnap(Order $order): array
    {
        $response = $this->http()->post($this->configuration->snapBaseUrl().'/snap/v1/transactions', [
            'transaction_details' => ['order_id' => $order->order_number, 'gross_amount' => $this->wholeRupiah($order->total_amount)],
            'item_details' => [[
                'id' => $order->order_number,
                'price' => $this->wholeRupiah($order->total_amount),
                'quantity' => 1,
                'name' => 'Order '.$order->order_number,
            ]],
            'customer_details' => [
                'first_name' => $order->user_snapshot['name'] ?? 'Customer',
                'email' => $order->user_snapshot['email'] ?? null,
                'phone' => $order->user_snapshot['phone'] ?? null,
            ],
            'expiry' => ['unit' => 'minute', 'duration' => (int) max(1, (int) ceil(now()->diffInMinutes($order->expires_at, false)))],
            'callbacks' => [
                'finish' => route('consumer.orders.show', $order->order_number),
                'unfinish' => route('consumer.orders.show', $order->order_number),
                'error' => route('consumer.orders.show', $order->order_number),
            ],
        ]);
        $response->throw();
        $data = $response->json();
        if (blank($data['token'] ?? null) || blank($data['redirect_url'] ?? null)) {
            throw new RuntimeException('Respons Snap tidak memiliki token atau redirect_url.');
        }
        return $data;
    }

    public function createSnapForInvoice(\App\Models\Invoice $invoice): array
    {
        $response = $this->http()->post($this->configuration->snapBaseUrl().'/snap/v1/transactions', [
            'transaction_details' => ['order_id' => $invoice->invoice_number, 'gross_amount' => $this->wholeRupiah($invoice->total_amount)],
            'item_details' => [[
                'id' => $invoice->invoice_number,
                'price' => $this->wholeRupiah($invoice->total_amount),
                'quantity' => 1,
                'name' => 'Tour Assistant Invoice '.$invoice->invoice_number,
            ]],
            'customer_details' => [
                'first_name' => $invoice->user->name ?? 'Customer',
                'email' => $invoice->user->email ?? null,
                'phone' => $invoice->user->phone ?? null,
            ],
            'expiry' => ['unit' => 'minute', 'duration' => (int) max(1, (int) ceil(now()->diffInMinutes($invoice->expires_at, false)))],
            'callbacks' => [
                'finish' => url('/tour-assistant/invoice/'.$invoice->invoice_number),
                'unfinish' => url('/tour-assistant/invoice/'.$invoice->invoice_number),
                'error' => url('/tour-assistant/invoice/'.$invoice->invoice_number),
            ],
        ]);
        $response->throw();
        $data = $response->json();
        if (blank($data['token'] ?? null) || blank($data['redirect_url'] ?? null)) {
            throw new RuntimeException('Respons Snap tidak memiliki token atau redirect_url.');
        }
        return $data;
    }

    public function status(string $orderNumber): array
    {
        $response = $this->http()->get($this->configuration->apiBaseUrl().'/v2/'.rawurlencode($orderNumber).'/status');
        $response->throw();
        return $response->json();
    }

    private function http(): PendingRequest
    {
        return Http::withBasicAuth($this->configuration->serverKey(), '')
            ->acceptJson()
            ->asJson()
            ->withOptions([
                'verify' => app()->environment('production'),
            ])
            ->timeout((int) config('midtrans.timeout_seconds', 15))
            ->retry(2, 200, throw: false);
    }

    private function wholeRupiah(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '00');
        if ((int) str_pad($fraction, 2, '0') !== 0) {
            throw new RuntimeException('Midtrans IDR membutuhkan nominal Rupiah tanpa pecahan.');
        }
        return (int) $whole;
    }
}
