<?php

namespace App\Actions\Tickets;

use App\Models\GatekeeperAssignment;
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

    public function execute(string $token, User $gatekeeper, string $mitraId, ?string $device = null): Ticket
    {
        [$ticket, $error] = DB::transaction(function () use ($token, $gatekeeper, $mitraId, $device) {
            $hash = TicketToken::hash($token);
            $ticket = Ticket::where('qr_token_hash', $hash)
                ->orWhere('ticket_code', $token)
                ->orWhere('id', $token)
                ->lockForUpdate()
                ->first();

            $assignment = GatekeeperAssignment::where('mitra_id', $mitraId)
                ->whereHas('member', fn ($q) => $q->where('user_id', $gatekeeper->id)->where('status', 'active'))
                ->whereNull('revoked_at')
                ->where(fn ($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()))
                ->where(fn ($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()))
                ->lockForUpdate()->first();

            if (! $assignment) {
                $member = \App\Models\MitraMember::where('mitra_id', $mitraId)
                    ->where('user_id', $gatekeeper->id)
                    ->where('status', 'active')
                    ->first();

                if (! $member && (method_exists($gatekeeper, 'hasAnyRole') && $gatekeeper->hasAnyRole(['admin', 'super-admin']))) {
                    $member = \App\Models\MitraMember::firstOrCreate([
                        'mitra_id' => $mitraId,
                        'user_id' => $gatekeeper->id,
                    ], [
                        'role' => 'gatekeeper',
                        'status' => 'active',
                        'joined_at' => now(),
                    ]);
                }

                if ($member) {
                    $assignment = GatekeeperAssignment::firstOrCreate([
                        'mitra_id' => $mitraId,
                        'member_id' => $member->id,
                    ], [
                        'scope_type' => 'mitra',
                        'scope_id' => $mitraId,
                        'assigned_by' => $gatekeeper->id,
                        'assigned_at' => now(),
                    ]);
                }
            }

            if (! $ticket || $ticket->mitra_id !== $mitraId) {
                $this->log($ticket, $gatekeeper, $assignment, 'unauthorized', $hash, $device);
                return [$ticket, 'Tiket tidak ditemukan atau milik Mitra lain (bukan untuk layanan usaha Anda).'];
            }
            if ($ticket->status === 'used') {
                $this->log($ticket, $gatekeeper, $assignment, 'duplicate', $hash, $device);
                return [$ticket, 'Tiket sudah pernah digunakan / check-in sebelumnya pada ' . ($ticket->used_at?->translatedFormat('d M Y, H:i') ?? '-') . ' WIB.'];
            }
            if ($ticket->status === 'revoked') {
                $this->log($ticket, $gatekeeper, $assignment, 'revoked', $hash, $device);
                return [$ticket, 'Tiket telah dibatalkan / dicabut oleh sistem.'];
            }
            if ($ticket->status === 'expired' || ($ticket->valid_until && $ticket->valid_until->endOfDay()->isPast())) {
                if ($ticket->status !== 'expired') $ticket->update(['status' => 'expired']);
                $this->log($ticket, $gatekeeper, $assignment, 'expired', $hash, $device);
                return [$ticket, 'Masa berlaku tiket telah kedaluwarsa.'];
            }
            if (! in_array($ticket->status, ['unused', 'active'], true) || ($ticket->valid_from && $ticket->valid_from->startOfDay()->isFuture())) {
                $this->log($ticket, $gatekeeper, $assignment, 'not_valid_now', $hash, $device);
                $validDate = $ticket->valid_from ? $ticket->valid_from->translatedFormat('d F Y') : '-';
                return [$ticket, 'Tiket belum dapat digunakan (berlaku mulai tanggal ' . $validDate . ').'];
            }

            $ticket->update(['status' => 'used', 'used_at' => now()]);
            $this->log($ticket, $gatekeeper, $assignment, 'accepted', $hash, $device);
            $this->audit->record('ticket.validated', $ticket, ['status' => 'unused'], ['status' => 'used'], $gatekeeper);
            return [$ticket->fresh(), null];
        });

        if ($error) throw ValidationException::withMessages(['token' => $error]);
        return $ticket;
    }

    private function log(?Ticket $ticket, User $user, ?GatekeeperAssignment $assignment, string $result, string $hash, ?string $device): void
    {
        TicketValidationLog::create(['ticket_id' => $ticket?->id, 'gatekeeper_user_id' => $user->id, 'gatekeeper_assignment_id' => $assignment?->id, 'result' => $result, 'device_reference' => $device, 'presented_token_hash' => $hash, 'scanned_at' => now()]);
    }
}
