<?php

namespace App\Actions\Tourism;

use App\Enums\CatalogStatus;
use App\Models\CatalogEntity;
use App\Models\DatabaseNotification;
use App\Models\ModerationAction;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ModerateTourismDestination
{
    public function __construct(private AuditLogger $audit) {}

    public function execute(CatalogEntity $entity, string $decision, ?string $reason, $actor): CatalogEntity
    {
        return DB::transaction(function () use ($entity, $decision, $reason, $actor) {
            $entity = CatalogEntity::lockForUpdate()->findOrFail($entity->id);
            $target = match ($decision) {
                'approve' => CatalogStatus::Published->value, 'reject' => CatalogStatus::Rejected->value, 'takedown' => CatalogStatus::TakenDown->value, default => null
            };
            if (! $target) {
                throw ValidationException::withMessages(['decision' => 'Keputusan tidak valid.']);
            }
            if (in_array($decision, ['reject', 'takedown'], true) && blank($reason)) {
                throw ValidationException::withMessages(['reason' => 'Alasan wajib diisi.']);
            }
            $allowed = $decision === 'takedown' ? [$entity->status === CatalogStatus::Published->value] : [in_array($entity->status, [CatalogStatus::Submitted->value, CatalogStatus::UnderReview->value], true)];
            if (! $allowed[0]) {
                throw ValidationException::withMessages(['decision' => 'Keputusan tidak sesuai lifecycle saat ini.']);
            }
            $report = $entity->moderationReports()->where('status', 'open')->latest()->lockForUpdate()->first();
            if (! $report) {
                $report = $entity->moderationReports()->create(['reporter_user_id' => $actor->id, 'mitra_id' => $entity->mitra_id, 'reason_code' => $decision === 'takedown' ? 'admin_takedown' : 'publication_review', 'status' => 'open']);
            }
            $from = $entity->status;
            if ($from === CatalogStatus::Submitted->value) {
                ModerationAction::create(['report_id' => $report->id, 'actor_user_id' => $actor->id, 'action_type' => 'review_started', 'from_status' => $from, 'to_status' => CatalogStatus::UnderReview->value]);
                $entity->update(['status' => CatalogStatus::UnderReview->value]);
                $from = CatalogStatus::UnderReview->value;
            }
            $entity->update(['status' => $target, 'published_at' => $target === CatalogStatus::Published->value ? now() : $entity->published_at]);
            ModerationAction::create(['report_id' => $report->id, 'actor_user_id' => $actor->id, 'action_type' => $decision, 'from_status' => $from, 'to_status' => $target, 'notes' => $reason]);
            $report->update(['status' => 'resolved', 'assigned_to' => $actor->id, 'resolved_at' => now()]);
            $ownerIds = DB::table('mitra_members')->where('mitra_id', $entity->mitra_id)->where('status', 'active')->pluck('user_id');
            foreach ($ownerIds as $userId) {
                DatabaseNotification::create(['user_id' => $userId, 'mitra_id' => $entity->mitra_id, 'type' => 'tourism.'.$decision, 'data' => ['catalog_entity_id' => $entity->id, 'name' => $entity->name, 'reason' => $reason]]);
            }
            $this->audit->record('tourism.'.$decision, $entity, ['status' => $from], ['status' => $target, 'reason' => $reason], $actor);

            return $entity->fresh();
        });
    }
}
