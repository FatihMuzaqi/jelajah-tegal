<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\Tickets\QrTicketRenderer;
use App\Support\TicketToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
    /**
     * Render Single-Use QR Code SVG for ticket.
     * Enforces strict authorization and paid payment status check.
     */
    public function qr(Request $request, Ticket $ticket, QrTicketRenderer $renderer): Response
    {
        // 1. Pastikan pengguna adalah pemegang tiket yang sah
        abort_unless($ticket->holder_user_id === $request->user()->id, 403, 'Anda tidak memiliki hak akses untuk tiket ini.');

        // 2. Pastikan pesanan sudah lunas (Paid) sebelum QR Code dapat digenerate/diakses
        $order = $ticket->orderItem?->order;
        if (! $order || $order->status->value !== 'paid' || $order->payment_status->value !== 'paid') {
            abort(403, 'Tiket QR Code belum dapat dibuat sebelum status pembayaran Lunas (Paid).');
        }

        // 3. Pastikan status tiket masih aktif/belum digunakan
        abort_unless(in_array($ticket->status, ['unused', 'active'], true), 410, 'Status tiket tidak valid atau sudah digunakan.');

        // 4. Buat dan validasi token QR
        $token = TicketToken::for($ticket->id, $ticket->token_version);
        abort_unless(hash_equals($ticket->qr_token_hash, TicketToken::hash($token)), 410, 'Token QR tiket tidak valid.');

        // 5. Render SVG QR Code
        return response($renderer->svg($token), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}

