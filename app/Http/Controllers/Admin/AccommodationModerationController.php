<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Accommodation\ModerateAccommodation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModerateAccommodationRequest;
use App\Models\CatalogEntity;
use App\Models\Review;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccommodationModerationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('accommodation.moderate'), 403);
        $items = CatalogEntity::whereIn('status', ['submitted', 'under_review'])->whereHas('serviceType', fn ($q) => $q->where('code', 'accommodation'))->with(['mitra', 'category', 'accommodation.rooms'])->oldest('updated_at')->paginate(20);

        $reviews = Review::where('status', 'pending')->whereHas('catalogEntity.serviceType', fn ($query) => $query->where('code', 'accommodation'))->with(['user', 'catalogEntity'])->oldest()->paginate(10, ['*'], 'reviews');

        return view('admin.accommodation.index', compact('items', 'reviews'));
    }

    public function show(Request $request, CatalogEntity $accommodation): View
    {
        abort_unless($request->user()->can('accommodation.moderate') && $accommodation->serviceType()->where('code', 'accommodation')->exists(), 403);

        return view('admin.accommodation.show', ['accommodation' => $accommodation->load(['mitra', 'accommodation.rooms.offer', 'accommodation.rooms.facilities', 'location', 'category', 'region', 'facilities', 'media', 'moderationReports.actions.actor'])]);
    }

    public function update(ModerateAccommodationRequest $request, CatalogEntity $accommodation, ModerateAccommodation $action): RedirectResponse
    {
        abort_unless($accommodation->serviceType()->where('code', 'accommodation')->exists(), 404);
        $data = $request->validated();
        $action->execute($accommodation, $data['decision'], $data['reason'] ?? null, $request->user());

        return redirect()->route('admin.accommodation.show', $accommodation)->with('status', 'Keputusan moderasi tersimpan.');
    }

    public function review(Request $request, Review $review, AuditLogger $audit): RedirectResponse
    {
        abort_unless($request->user()->can('accommodation.moderate') && $review->catalogEntity->serviceType()->where('code', 'accommodation')->exists(), 403);
        $data = $request->validate(['decision' => 'required|in:publish,reject', 'reason' => 'nullable|string|max:2000|required_if:decision,reject']);
        abort_unless($review->status === 'pending', 422);
        $review->update(['status' => $data['decision'] === 'publish' ? 'published' : 'rejected', 'moderated_by' => $request->user()->id, 'moderated_at' => now()]);
        $entity = $review->catalogEntity;
        $ratings = $entity->reviews()->where('status', 'published')->selectRaw('COUNT(*) total, COALESCE(AVG(rating), 0) average')->first();
        $entity->update(['rating_count' => $ratings->total, 'rating_average' => $ratings->average]);
        $audit->record('accommodation.review_'.$data['decision'], $entity, ['review_status' => 'pending'], ['review_id' => $review->id, 'reason' => $data['reason'] ?? null], $request->user());

        return back()->with('status', 'Moderasi ulasan penginapan disimpan.');
    }
}
