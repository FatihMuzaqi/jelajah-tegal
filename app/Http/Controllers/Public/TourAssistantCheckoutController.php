<?php

namespace App\Http\Controllers\Public;

use App\Actions\Payments\CapturePayment;
use App\Http\Controllers\Controller;
use App\Models\CatalogOffer;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\Checkout\CommercialTerms;
use App\Services\Payments\MidtransClient;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class TourAssistantCheckoutController extends Controller
{
    public function __construct(
        private MidtransClient $midtrans,
        private CommercialTerms $terms
    ) {}

    public function process(Request $request): RedirectResponse
    {
        $package = json_decode($request->input('package'), true);
        if (!$package || empty($package['items'])) {
            return back()->with('error', 'Paket itinerary tidak valid.');
        }

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melanjutkan pemesanan.');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $invoice = DB::transaction(function () use ($user, $package, $start, $end) {
            // 1. Buat Invoice Induk dengan Total yang Sesuai Rincian Paket
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . now()->format('ymd') . '-' . str()->upper(str()->random(10)),
                'user_id' => $user->id,
                'total_amount' => $package['total_cost'],
                'status' => 'pending',
                'expires_at' => now()->addMinutes(30),
            ]);

            // 2. Buat Order untuk setiap item di paket
            foreach ($package['items'] as $item) {
                $offer = CatalogOffer::with('catalogEntity.mitra')->findOrFail($item['offer']['id']);
                $entity = $offer->catalogEntity;
                $mitra = $entity->mitra;
                
                $quantity = (int) $item['quantity'];
                $itemSubtotal = (float) $item['subtotal'];
                $subtotalMinor = Money::toMinor($itemSubtotal);
                
                $bps = $this->terms->commissionBasisPoints($mitra);
                $commissionMinor = Money::basisPoints($subtotalMinor, $bps);
                $mitraNetMinor = $subtotalMinor - $commissionMinor;

                $order = Order::create([
                    'order_number' => 'ORD-' . now()->format('ymd') . '-' . str()->upper(str()->random(10)),
                    'invoice_id' => $invoice->id,
                    'user_id' => $user->id,
                    'mitra_id' => $mitra->id,
                    'currency' => 'IDR',
                    'subtotal' => Money::fromMinor($subtotalMinor),
                    'admin_fee' => '0.00',
                    'discount_amount' => '0.00',
                    'total_amount' => Money::fromMinor($subtotalMinor),
                    'commission_basis_points' => $bps,
                    'commission_amount' => Money::fromMinor($commissionMinor),
                    'mitra_net_amount' => Money::fromMinor($mitraNetMinor),
                    'status' => 'pending_payment',
                    'payment_status' => 'pending',
                    'user_snapshot' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone
                    ],
                    'mitra_snapshot' => [
                        'id' => $mitra->id,
                        'name' => $mitra->display_name,
                        'slug' => $mitra->slug
                    ],
                    'placed_at' => now(),
                    'expires_at' => now()->addMinutes(30),
                ]);

                // Order Item
                $order->items()->create([
                    'mitra_id' => $mitra->id,
                    'catalog_offer_id' => $offer->id,
                    'resource_type' => $item['type'],
                    'reference_id' => $offer->id,
                    'quantity' => $quantity,
                    'item_name' => $entity->name . ' - ' . $offer->name,
                    'sku' => $offer->sku,
                    'unit_price' => Money::fromMinor(Money::toMinor($offer->price)),
                    'subtotal' => Money::fromMinor($subtotalMinor),
                    'admin_fee' => '0.00',
                    'discount_amount' => '0.00',
                    'line_total' => Money::fromMinor($subtotalMinor),
                    'booking_date' => $start->toDateString(),
                    'starts_at' => $start->toDateTimeString(),
                    'ends_at' => $end->toDateTimeString(),
                    'details' => [
                        'type' => $item['type'],
                        'days' => $item['days'] ?? 1,
                        'quantity' => $quantity,
                        'catalog_entity_id' => $entity->id,
                    ]
                ]);

                // Payment record per order
                $order->payments()->create([
                    'mitra_id' => $mitra->id,
                    'provider' => 'midtrans',
                    'amount' => Money::fromMinor($subtotalMinor),
                    'currency' => 'IDR',
                    'status' => 'pending',
                ]);
            }

            return $invoice;
        });

        // 3. Request Token Snap dari Midtrans untuk Invoice Induk
        try {
            $snap = $this->midtrans->createSnapForInvoice($invoice);
            $invoice->update(['payment_url' => $snap['redirect_url']]);
            return redirect()->away($snap['redirect_url']);
        } catch (Throwable $e) {
            return redirect()->route('tour-assistant.invoice.show', $invoice->invoice_number);
        }
    }

    public function showInvoice(Request $request, string $invoiceNumber): View
    {
        $invoice = Invoice::with(['orders.items.offer.catalogEntity', 'orders.mitra', 'orders.payments'])
            ->where('invoice_number', $invoiceNumber)
            ->firstOrFail();

        abort_unless($invoice->user_id === $request->user()->id, 403);

        return view('public.tour-assistant.invoice', compact('invoice'));
    }

    public function confirmDirect(Request $request, string $invoiceNumber, CapturePayment $capture): RedirectResponse
    {
        $invoice = Invoice::with('orders.payments')->where('invoice_number', $invoiceNumber)->firstOrFail();
        abort_unless($invoice->user_id === $request->user()->id, 403);
        abort_if(config('midtrans.production'), 403, 'Konfirmasi manual dinonaktifkan di mode production.');

        if ($invoice->status !== 'paid') {
            DB::transaction(function () use ($invoice, $capture) {
                $invoice->update(['status' => 'paid', 'paid_at' => now()]);

                foreach ($invoice->orders as $order) {
                    $payment = $order->payments()->first();
                    if ($payment && $payment->status->value !== 'paid') {
                        $ref = 'TEST-INV-' . str()->upper(str()->random(10));
                        $capture->execute(
                            payment: $payment,
                            providerReference: $ref,
                            amount: (string) $order->total_amount,
                            currency: 'IDR',
                            provider: 'test_direct'
                        );
                    }
                }
            });
        }

        return redirect()->route('tour-assistant.invoice.show', $invoice->invoice_number)
            ->with('status', 'Pembayaran Invoice Tour Assistant berhasil dikonfirmasi!');
    }
}
