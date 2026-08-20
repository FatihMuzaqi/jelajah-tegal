<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItineraryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = $request->query('status', 'paid');

        $query = Invoice::query()
            ->where('user_id', $user->id)
            ->whereNotNull('metadata->days')
            ->with(['orders.mitra', 'orders.items.tickets'])
            ->latest();

        if ($status === 'paid') {
            $query->where('status', 'paid');
        } elseif ($status === 'pending') {
            $query->where('status', 'pending');
        }

        $itineraries = $query->paginate(10)->withQueryString();

        $stats = [
            'total_paid' => Invoice::where('user_id', $user->id)->where('status', 'paid')->whereNotNull('metadata->days')->count(),
            'total_pending' => Invoice::where('user_id', $user->id)->where('status', 'pending')->whereNotNull('metadata->days')->count(),
            'total_all' => Invoice::where('user_id', $user->id)->whereNotNull('metadata->days')->count(),
        ];

        return view('consumer.itineraries.index', compact('itineraries', 'stats', 'status'));
    }

    public function show(Request $request, Invoice $invoice): View
    {
        $user = $request->user();
        abort_unless($invoice->user_id === $user->id, 403, 'Akses tidak diizinkan.');
        abort_unless($invoice->isItinerary(), 404, 'Data rencana perjalanan tidak ditemukan pada invoice ini.');

        $invoice->load(['orders.mitra', 'orders.items.tickets', 'orders.payments']);

        $itinerary = $invoice->metadata;

        return view('consumer.itineraries.show', compact('invoice', 'itinerary'));
    }

    public function pdf(Request $request, Invoice $invoice): View
    {
        $user = $request->user();
        abort_unless($invoice->user_id === $user->id, 403, 'Akses tidak diizinkan.');
        abort_unless($invoice->isItinerary(), 404, 'Data rencana perjalanan tidak ditemukan pada invoice ini.');

        $invoice->load(['orders.mitra', 'orders.items.tickets', 'orders.payments', 'user']);

        $itinerary = $invoice->metadata;

        // Generate QR Code data URI for verification
        $qrDataUri = null;
        try {
            $verificationUrl = route('consumer.invoices.show', $invoice->invoice_number);
            $qrCode = new QrCode($verificationUrl);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrDataUri = $result->getDataUri();
        } catch (\Throwable $e) {
            // Fallback gracefully if QR generation fails
            $qrDataUri = null;
        }

        return view('consumer.itineraries.pdf', compact('invoice', 'itinerary', 'qrDataUri'));
    }
}
