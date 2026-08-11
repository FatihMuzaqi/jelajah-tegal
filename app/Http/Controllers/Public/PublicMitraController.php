<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CatalogEntity;
use App\Models\Mitra;
use App\Support\FeatureFlags;
use Illuminate\View\View;

class PublicMitraController extends Controller
{
    public function __construct(private readonly FeatureFlags $flags) {}

    public function show(string $slug): View
    {
        abort_unless($this->flags->enabled('public-mitra-directory'), 404);

        $mitra = Mitra::query()
            ->publiclyVisible()
            ->where('slug', $slug)
            ->with([
                'region',
                'features' => fn ($q) => $q->where('status', 'enabled')->with('serviceType'),
            ])
            ->firstOrFail();

        $tourisms = CatalogEntity::publicTourism()
            ->where('mitra_id', $mitra->id)
            ->with(['category', 'region', 'media', 'offers', 'tourism'])
            ->latest('published_at')
            ->get();

        $accommodations = CatalogEntity::publicAccommodation()
            ->where('mitra_id', $mitra->id)
            ->with(['category', 'region', 'media', 'accommodation.rooms.offer'])
            ->latest('published_at')
            ->get();

        $culinaries = CatalogEntity::publicDomain('culinary')
            ->where('mitra_id', $mitra->id)
            ->with(['category', 'region', 'media'])
            ->latest('published_at')
            ->get();

        $events = CatalogEntity::publicDomain('event')
            ->where('mitra_id', $mitra->id)
            ->with(['category', 'region', 'media'])
            ->latest('published_at')
            ->get();

        $rentals = CatalogEntity::publicDomain('rental')
            ->where('mitra_id', $mitra->id)
            ->with(['category', 'region', 'media'])
            ->latest('published_at')
            ->get();

        $totalCatalogs = $tourisms->count() + $accommodations->count() + $culinaries->count() + $events->count() + $rentals->count();

        return view('public.mitra.show', compact(
            'mitra',
            'tourisms',
            'accommodations',
            'culinaries',
            'events',
            'rentals',
            'totalCatalogs'
        ));
    }
}
