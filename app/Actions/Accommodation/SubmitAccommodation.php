<?php

namespace App\Actions\Accommodation;

use App\Models\CatalogEntity;
use App\Models\ModerationAction;
use App\Models\ModerationReport;
use App\Services\AuditLogger;
use App\Services\PlatformNotifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitAccommodation
{
    public function __construct(private AuditLogger $audit, private PlatformNotifier $notifier) {}

    public function execute(CatalogEntity $entity, $actor): CatalogEntity
    {
        return DB::transaction(function () use ($entity, $actor) {
            $entity = CatalogEntity::lockForUpdate()->with(['accommodation.rooms', 'location'])->findOrFail($entity->id);
            abort_unless($entity->serviceType()->where('code', 'accommodation')->exists(), 404);
            if (! in_array($entity->status, ['draft', 'rejected'], true)) {
                throw ValidationException::withMessages(['status' => 'Status properti tidak dapat diajukan.']);
            }
            $missing = [];
            foreach (['category_id' => 'kategori', 'region_id' => 'wilayah', 'description' => 'deskripsi', 'address' => 'alamat'] as $field => $label) {
                if (blank($entity->{$field})) {
                    $missing[] = $label;
                }
            }
            if (! $entity->accommodation) {
                $missing[] = 'detail properti';
            }
            if (! $entity->location) {
                $missing[] = 'koordinat';
            }
            if (! $entity->media()->wherePivot('role', 'cover')->exists()) {
                $missing[] = 'media cover properti';
            }
            if (! $entity->accommodation?->rooms()->where('status', 'active')->exists()) {
                $missing[] = 'kamar aktif';
            }
            if ($missing) {
                throw ValidationException::withMessages(['status' => 'Lengkapi: '.implode(', ', $missing).'.']);
            }
            $from = $entity->status;
            $entity->update(['status' => 'submitted', 'published_at' => null, 'archived_at' => null]);
            $report = ModerationReport::create(['reporter_user_id' => $actor->id, 'mitra_id' => $entity->mitra_id, 'catalog_entity_id' => $entity->id, 'reason_code' => 'accommodation_publication_review', 'description' => 'Pengajuan publikasi penginapan.', 'status' => 'open']);
            ModerationAction::create(['report_id' => $report->id, 'actor_user_id' => $actor->id, 'action_type' => 'submitted', 'from_status' => $from, 'to_status' => 'submitted']);
            $this->audit->record('accommodation.submitted', $entity, ['status' => $from], ['status' => 'submitted'], $actor);
            $this->notifier->administrators('accommodation.submitted', $entity->mitra_id, ['catalog_entity_id' => $entity->id, 'name' => $entity->name]);

            return $entity->fresh();
        });
    }
}
