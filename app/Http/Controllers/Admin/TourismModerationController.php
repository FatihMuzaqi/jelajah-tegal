<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Tourism\ModerateTourismDestination;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModerateTourismRequest;
use App\Models\CatalogEntity;
use App\Models\Review;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TourismModerationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('tourism.moderate'), 403);
        $items = CatalogEntity::whereIn('status', ['submitted', 'under_review'])->whereHas('serviceType', fn ($q) => $q->where('code', 'tourism'))->with(['mitra', 'category'])->oldest('updated_at')->paginate(20);

        $reviews = Review::where('status', 'pending')->with(['user', 'catalogEntity'])->oldest()->paginate(10, ['*'], 'reviews');

        return view('admin.tourism.index', compact('items', 'reviews'));
    }

    public function show(Request $request, CatalogEntity $tourism): View
    {
        Gate::authorize('moderate', $tourism);

        return view('admin.tourism.show', ['tourism' => $tourism->load(['mitra', 'tourism', 'location', 'category', 'region', 'facilities', 'operatingHours', 'media', 'offers.availabilities', 'moderationReports.actions.actor'])]);
    }

    public function update(ModerateTourismRequest $request, CatalogEntity $tourism, ModerateTourismDestination $action): RedirectResponse
    {
        Gate::authorize('moderate', $tourism);
        $data = $request->validated();
        $action->execute($tourism, $data['decision'], $data['reason'] ?? null, $request->user());

        return redirect()->route('admin.tourism.show', $tourism)->with('status', 'Keputusan moderasi tersimpan.');
    }

    public function review(Request $request, Review $review, AuditLogger $audit): RedirectResponse
    {
        abort_unless($request->user()->can('tourism.moderate'), 403);
        $data = $request->validate(['decision' => 'required|in:publish,reject', 'reason' => 'nullable|string|max:2000|required_if:decision,reject']);
        abort_unless($review->status === 'pending', 422);
        $review->update(['status' => $data['decision'] === 'publish' ? 'published' : 'rejected', 'moderated_by' => $request->user()->id, 'moderated_at' => now()]);
        $entity = $review->catalogEntity;
        $ratings = $entity->reviews()->where('status', 'published')->selectRaw('COUNT(*) total, COALESCE(AVG(rating), 0) average')->first();
        $entity->update(['rating_count' => $ratings->total, 'rating_average' => $ratings->average]);
        $audit->record('tourism.review_'.$data['decision'], $entity, ['review_status' => 'pending'], ['review_id' => $review->id, 'reason' => $data['reason'] ?? null], $request->user());

        return back()->with('status', 'Moderasi ulasan disimpan.');
    }
}
