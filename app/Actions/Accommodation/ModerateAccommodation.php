<?php

namespace App\Actions\Accommodation;

use App\Models\CatalogEntity;
use App\Models\DatabaseNotification;
use App\Models\ModerationAction;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ModerateAccommodation
{
    public function __construct(private AuditLogger $audit) {}

    public function execute(CatalogEntity $entity, string $decision, ?string $reason, $actor): CatalogEntity
    {
        return DB::transaction(function () use ($entity, $decision, $reason, $actor) {
            $entity = CatalogEntity::lockForUpdate()->findOrFail($entity->id);
            abort_unless($entity->serviceType()->where('code', 'accommodation')->exists(), 404);
            $target = match ($decision) {
                'approve' => 'published', 'reject' => 'rejected', 'takedown' => 'taken_down', default => null
            };
            if (! $target) {
                throw ValidationException::withMessages(['decision' => 'Keputusan tidak valid.']);
            }
            if (in_array($decision, ['reject', 'takedown'], true) && blank($reason)) {
                throw ValidationException::withMessages(['reason' => 'Alasan wajib diisi.']);
            }
            $valid = $decision === 'takedown' ? $entity->status === 'published' : in_array($entity->status, ['submitted', 'under_review'], true);
            if (! $valid) {
                throw ValidationException::withMessages(['decision' => 'Keputusan tidak sesuai lifecycle saat ini.']);
            }
            $report = $entity->moderationReports()->where('status', 'open')->latest()->lockForUpdate()->first();
            if (! $report) {
                $report = $entity->moderationReports()->create(['reporter_user_id' => $actor->id, 'mitra_id' => $entity->mitra_id, 'reason_code' => $decision === 'takedown' ? 'accommodation_takedown' : 'accommodation_publication_review', 'status' => 'open']);
            }
            $from = $entity->status;
            if ($from === 'submitted') {
                ModerationAction::create(['report_id' => $report->id, 'actor_user_id' => $actor->id, 'action_type' => 'review_started', 'from_status' => 'submitted', 'to_status' => 'under_review']);
                $entity->update(['status' => 'under_review']);
                $from = 'under_review';
            }
            $entity->update(['status' => $target, 'published_at' => $target === 'published' ? now() : $entity->published_at]);
            ModerationAction::create(['report_id' => $report->id, 'actor_user_id' => $actor->id, 'action_type' => $decision, 'from_status' => $from, 'to_status' => $target, 'notes' => $reason]);
            $report->update(['status' => 'resolved', 'assigned_to' => $actor->id, 'resolved_at' => now()]);
            foreach (DB::table('mitra_members')->where('mitra_id', $entity->mitra_id)->where('status', 'active')->pluck('user_id') as $userId) {
                DatabaseNotification::create(['user_id' => $userId, 'mitra_id' => $entity->mitra_id, 'type' => 'accommodation.'.$decision, 'data' => ['catalog_entity_id' => $entity->id, 'name' => $entity->name, 'reason' => $reason]]);
            }
            $this->audit->record('accommodation.'.$decision, $entity, ['status' => $from], ['status' => $target, 'reason' => $reason], $actor);

            return $entity->fresh();
        });
    }
}
