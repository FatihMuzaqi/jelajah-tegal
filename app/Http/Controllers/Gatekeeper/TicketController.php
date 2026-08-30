<?php

namespace App\Http\Controllers\Gatekeeper;

use App\Actions\Tickets\ValidateQrTicket;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function validateTicket(Request $request, ValidateQrTicket $action): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'token' => 'required|string|min:4',
            'device_reference' => 'nullable|string|max:100',
        ]);

        $activeMitraId = (string) $request->session()->get('active_mitra_id');

        try {
            $ticket = $action->execute(
                $data['token'],
                $request->user(),
                $activeMitraId ?: null,
                $data['device_reference'] ?? null
            );

            $serviceName = $ticket->orderItem?->item_name ?? 'Layanan Mitra';
            $holderName = $ticket->holderUser?->name ?? 'Pengunjung';
            $msg = " Tiket {$ticket->ticket_code} ({$serviceName}) BERHASIL divalidasi & check-in untuk {$holderName}!";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'ticket' => [
                        'code' => $ticket->ticket_code,
                        'service' => $serviceName,
                        'holder' => $holderName,
                        'scanned_at' => now()->translatedFormat('d F Y, H:i:s') . ' WIB',
                        'status' => 'used',
                    ],
                ]);
            }

            return back()->with('status', $msg)->with('scanned_ticket', $ticket);
        } catch (ValidationException $e) {
            $errorMsg = $e->errors()['token'][0] ?? 'Tiket gagal divalidasi.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 422);
            }

            return back()->withErrors(['token' => $errorMsg])->withInput();
        }
    }
}

