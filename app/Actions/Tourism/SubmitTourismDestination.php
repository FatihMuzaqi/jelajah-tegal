<?php

namespace App\Actions\Tourism;

use App\Enums\CatalogStatus;
use App\Models\CatalogEntity;
use App\Models\ModerationAction;
use App\Models\ModerationReport;
use App\Services\AuditLogger;
use App\Services\PlatformNotifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitTourismDestination
{
    public function __construct(private AuditLogger $audit, private PlatformNotifier $notifier) {}

    public function execute(CatalogEntity $entity, $actor): CatalogEntity
    {
        return DB::transaction(function () use ($entity, $actor) {
            $entity = CatalogEntity::lockForUpdate()->with(['tourism', 'location'])->findOrFail($entity->id);
            if (! in_array($entity->status, [CatalogStatus::Draft->value, CatalogStatus::Rejected->value], true)) {
                throw ValidationException::withMessages(['status' => 'Status konten tidak dapat diajukan.']);
            }
            $missing = [];
            foreach (['category_id' => 'kategori', 'region_id' => 'wilayah', 'description' => 'deskripsi', 'address' => 'alamat'] as $field => $label) {
                if (blank($entity->{$field})) {
                    $missing[] = $label;
                }
            }
            if (! $entity->tourism) {
                $missing[] = 'detail wisata';
            }
            if (! $entity->location) {
                $missing[] = 'koordinat';
            }
            if (! $entity->media()->wherePivot('role', 'cover')->exists()) {
                $missing[] = 'media cover';
            }
            if (! $entity->operatingHours()->exists()) {
                $missing[] = 'jam operasional';
            }
            if ($missing) {
                throw ValidationException::withMessages(['status' => 'Lengkapi: '.implode(', ', $missing).'.']);
            }
            $from = $entity->status;
            $entity->update(['status' => CatalogStatus::Submitted->value, 'published_at' => null, 'archived_at' => null]);
            $report = ModerationReport::create(['reporter_user_id' => $actor->id, 'mitra_id' => $entity->mitra_id, 'catalog_entity_id' => $entity->id, 'reason_code' => 'publication_review', 'description' => 'Pengajuan publikasi wisata.', 'status' => 'open']);
            ModerationAction::create(['report_id' => $report->id, 'actor_user_id' => $actor->id, 'action_type' => 'submitted', 'from_status' => $from, 'to_status' => CatalogStatus::Submitted->value]);
            $this->audit->record('tourism.submitted', $entity, ['status' => $from], ['status' => $entity->status], $actor);
            $this->notifier->administrators('tourism.submitted', $entity->mitra_id, ['catalog_entity_id' => $entity->id, 'name' => $entity->name]);

            return $entity->fresh();
        });
    }
}
