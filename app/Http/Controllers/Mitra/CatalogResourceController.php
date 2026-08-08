<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Models\CatalogEntity;
use App\Models\Review;
use App\Services\AuditLogger;
use App\Services\MitraMediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogResourceController
{
    use ResolvesActiveMitra;

    public function media(Request $r, string $domain, CatalogEntity $entity, MitraMediaStorage $storage, AuditLogger $audit): RedirectResponse
    {
        $this->owned($r, $domain, $entity);
        $d = $r->validate(['image' => 'required|file|mimes:jpg,jpeg,png,webp|max:8192', 'role' => 'required|in:cover,gallery', 'caption' => 'nullable|string|max:255']);
        $asset = $storage->store($this->activeMitra($r), $r->file('image'), $domain, false);
        DB::transaction(function () use ($entity, $asset, $d, $audit, $r, $domain) {
            if ($d['role'] === 'cover') {
                $entity->media()->wherePivot('role', 'cover')->detach();
            }$sort = (int) DB::table('catalog_media')->where('catalog_entity_id', $entity->id)->max('sort_order') + 1;
            $entity->media()->attach($asset->id, ['role' => $d['role'], 'sort_order' => $sort, 'caption' => $d['caption'] ?? null]);
            $audit->record($domain.'.media_added', $entity, [], ['media_asset_id' => $asset->id], $r->user());
        });

        return back()->with('status', 'Media ditambahkan.');
    }

    public function hours(Request $r, string $domain, CatalogEntity $entity, AuditLogger $audit): RedirectResponse
    {
        $this->owned($r, $domain, $entity);
        $d = $r->validate(['hours' => 'required|array|max:14', 'hours.*.weekday' => 'required|integer|between:1,7', 'hours.*.sequence' => 'nullable|integer|min:1|max:3', 'hours.*.opens_at' => 'nullable|date_format:H:i', 'hours.*.closes_at' => 'nullable|date_format:H:i', 'hours.*.is_closed' => 'sometimes|boolean']);
        DB::transaction(function () use ($entity, $d, $audit, $r, $domain) {
            $entity->operatingHours()->delete();
            foreach ($d['hours'] as $row) {
                $entity->operatingHours()->create($row);
            }$audit->record($domain.'.hours_updated', $entity, [], ['count' => count($d['hours'])], $r->user());
        });

        return back()->with('status', 'Jam operasional diperbarui.');
    }

    public function reply(Request $r, string $domain, CatalogEntity $entity, Review $review, AuditLogger $audit): RedirectResponse
    {
        $this->owned($r, $domain, $entity);
        abort_unless($review->catalog_entity_id === $entity->id && $review->status === 'published', 404);
        $d = $r->validate(['body' => 'required|string|min:3|max:2000']);
        $review->reply()->updateOrCreate([], ['mitra_id' => $entity->mitra_id, 'replied_by' => $r->user()->id, 'body' => $d['body'], 'status' => 'published']);
        $audit->record($domain.'.review_replied', $entity, [], ['review_id' => $review->id], $r->user());

        return back()->with('status', 'Balasan tersimpan.');
    }

    private function owned(Request $r, string $domain, CatalogEntity $entity): void
    {
        abort_unless(in_array($domain, ['culinary', 'event', 'rental'], true), 404);
        abort_unless($r->user()->can($domain.'.manage') && $entity->mitra_id === $this->activeMitra($r)->id && $entity->serviceType()->where('code',$domain)->exists(),403);
    }
}
