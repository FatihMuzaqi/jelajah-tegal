<?php

namespace App\Actions\Tickets;

use App\Models\GatekeeperAssignment;
use App\Models\MitraMember;
use App\Models\Ticket;
use App\Models\TicketValidationLog;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\TicketToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ValidateQrTicket
{
    public function __construct(private AuditLogger $audit) {}

    public function execute(string $rawToken, User $gatekeeper, ?string $sessionMitraId = null, ?string $device = null): Ticket
    {
        $token = trim($rawToken);

        // 1. Ekstrak token jika hasil scan berupa URL lengkap
        if (filter_var($token, FILTER_VALIDATE_URL)) {
            // Contoh URL: http://.../consumer/tickets/01KZV.../qr
            if (preg_match('#/tickets/([a-zA-Z0-9_-]+)#', $token, $matches)) {
                $token = $matches[1];
            }
        }

        [$ticket, $error] = DB::transaction(function () use ($token, $gatekeeper, $sessionMitraId, $device) {
            $hash = TicketToken::hash($token);
            $upperCode = strtoupper($token);

            // Cari tiket berdasarkan qr_token_hash, ticket_code, atau id (ULID)
            $ticket = Ticket::where('qr_token_hash', $hash)
                ->orWhere('ticket_code', $upperCode)
                ->orWhere('ticket_code', $token)
                ->orWhere('id', $token)
                ->with(['orderItem.order', 'holderUser', 'mitra'])
                ->lockForUpdate()
                ->first();

            if (! $ticket) {
                $this->log(null, $gatekeeper, null, 'unauthorized', $hash, $device);
                return [null, 'Tiket QR tidak ditemukan di sistem. Pastikan QR Code yang dipindai adalah tiket resmi Jelajah Tegal.'];
            }

            // Tentukan mitra ID yang berlaku untuk validasi
            $effectiveMitraId = $sessionMitraId;
            if (empty($effectiveMitraId)) {
                $effectiveMitraId = $ticket->mitra_id;
            }

            // Validasi otorisasi gatekeeper pada tenant mitra
            $isSuperOrAdmin = method_exists($gatekeeper, 'hasAnyRole') && $gatekeeper->hasAnyRole(['admin', 'super-admin']);
            $isMitraMember = MitraMember::where('mitra_id', $ticket->mitra_id)
                ->where('user_id', $gatekeeper->id)
                ->where('status', 'active')
                ->exists();

            if (! $isSuperOrAdmin && ! $isMitraMember && $ticket->mitra_id !== $effectiveMitraId) {
                $this->log($ticket, $gatekeeper, null, 'unauthorized', $hash, $device);
                return [$ticket, 'Tiket ini milik Mitra lain (' . ($ticket->mitra?->display_name ?? 'Mitra Lain') . ') dan tidak dapat divalidasi di loket usaha Anda.'];
            }

            // Pastikan assignment gatekeeper tercatat
            $assignment = GatekeeperAssignment::where('mitra_id', $ticket->mitra_id)
                ->whereHas('member', fn ($q) => $q->where('user_id', $gatekeeper->id)->where('status', 'active'))
                ->whereNull('revoked_at')
                ->lockForUpdate()
                ->first();

            if (! $assignment) {
                $member = MitraMember::firstOrCreate([
                    'mitra_id' => $ticket->mitra_id,
                    'user_id' => $gatekeeper->id,
                ], [
                    'role' => 'gatekeeper',
                    'status' => 'active',
                    'joined_at' => now(),
                ]);

                $assignment = GatekeeperAssignment::firstOrCreate([
                    'mitra_id' => $ticket->mitra_id,
                    'member_id' => $member->id,
                ], [
                    'scope_type' => 'mitra',
                    'scope_id' => $ticket->mitra_id,
                    'assigned_by' => $gatekeeper->id,
                    'assigned_at' => now(),
                ]);
            }

            // 1. CEK STATUS SUDAH DIGUNAKAN (DUPLICATE SCAN PREVENTION)
            if ($ticket->status === 'used') {
                $this->log($ticket, $gatekeeper, $assignment, 'duplicate', $hash, $device);
                $usedTime = $ticket->used_at ? $ticket->used_at->translatedFormat('d F Y, H:i') : 'Waktu tidak tercatat';
                $serviceName = $ticket->orderItem?->item_name ?? 'Layanan Mitra';
                return [$ticket, "❌ TIKET SUDAH DIGUNAKAN! Tiket {$ticket->ticket_code} ({$serviceName}) telah dicheck-in sebelumnya pada {$usedTime} WIB. Tiket tidak dapat dipindai ulang."];
            }

            // 2. CEK STATUS DIBATALKAN / REVOKED
            if ($ticket->status === 'revoked') {
                $this->log($ticket, $gatekeeper, $assignment, 'revoked', $hash, $device);
                return [$ticket, "Tiket {$ticket->ticket_code} telah dibatalkan / dicabut oleh sistem atau pengelola."];
            }

            // 3. CEK KEDALUWARSA (EXPIRED)
            if ($ticket->status === 'expired' || ($ticket->valid_until && $ticket->valid_until->endOfDay()->isPast())) {
                if ($ticket->status !== 'expired') {
                    $ticket->update(['status' => 'expired']);
                }
                $this->log($ticket, $gatekeeper, $assignment, 'expired', $hash, $device);
                return [$ticket, "Masa berlaku tiket {$ticket->ticket_code} telah kedaluwarsa."];
            }

            // 4. CEK JADWAL BERLAKU
            if (! in_array($ticket->status, ['unused', 'active'], true) || ($ticket->valid_from && $ticket->valid_from->startOfDay()->isFuture())) {
                $this->log($ticket, $gatekeeper, $assignment, 'not_valid_now', $hash, $device);
                $validDate = $ticket->valid_from ? $ticket->valid_from->translatedFormat('d F Y') : '-';
                return [$ticket, "Tiket {$ticket->ticket_code} belum dapat digunakan (berlaku mulai tanggal {$validDate})."];
            }

            // 5. UPDATE STATUS MENJADI 'used' SECARA ATOMIK
            $ticket->update([
                'status' => 'used',
                'used_at' => now(),
            ]);

            $this->log($ticket, $gatekeeper, $assignment, 'accepted', $hash, $device);
            $this->audit->record('ticket.validated', $ticket, ['status' => 'unused'], ['status' => 'used'], $gatekeeper);

            return [$ticket->fresh(['orderItem.order', 'holderUser']), null];
        });

        if ($error) {
            throw ValidationException::withMessages(['token' => $error]);
        }

        return $ticket;
    }

    private function log(?Ticket $ticket, User $user, ?GatekeeperAssignment $assignment, string $result, string $hash, ?string $device): void
    {
        TicketValidationLog::create([
            'ticket_id' => $ticket?->id,
            'gatekeeper_user_id' => $user->id,
            'gatekeeper_assignment_id' => $assignment?->id,
            'result' => $result,
            'device_reference' => $device,
            'presented_token_hash' => $hash,
            'scanned_at' => now(),
        ]);
    }
}

