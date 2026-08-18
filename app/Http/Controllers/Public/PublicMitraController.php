<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CatalogEntity;
use App\Models\Mitra;
use App\Models\Region;
use App\Models\ServiceType;
use App\Support\FeatureFlags;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicMitraController extends Controller
{
    public function __construct(private readonly FeatureFlags $flags) {}

    public function index(Request $request): View
    {
        abort_unless($this->flags->enabled('public-mitra-directory'), 404);

        $query = Mitra::query()
            ->publiclyVisible()
            ->with([
                'region',
                'bannerMedia',
                'logoMedia',
                'features' => fn ($q) => $q->where('status', 'enabled')->with('serviceType'),
            ])
            ->withCount([
                'catalogEntities as published_catalogs_count' => fn ($q) => $q->where('status', 'published'),
            ]);

        // Filter Pencarian Keyword
        if ($q = trim($request->input('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('display_name', 'like', "%{$q}%")
                    ->orWhere('legal_name', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Filter Kategori (Dinas vs Non-Dinas)
        if ($category = $request->input('category')) {
            if (in_array($category, ['dinas', 'non_dinas'], true)) {
                $query->where('category', $category);
            }
        }

        // Filter Wilayah
        if ($regionId = $request->input('region')) {
            $query->where('region_id', $regionId);
        }

        // Filter Layanan / Sektor Bisnis
        if ($serviceCode = $request->input('service')) {
            $query->whereHas('features', function ($f) use ($serviceCode) {
                $f->where('status', 'enabled')
                    ->whereHas('serviceType', fn ($st) => $st->where('code', $serviceCode));
            });
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'name_asc') {
            $query->orderBy('display_name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('display_name', 'desc');
        } else {
            $query->latest('approved_at');
        }

        $mitras = $query->paginate(12)->withQueryString();

        $regions = Region::orderBy('name')->get();
        $serviceTypes = ServiceType::all();

        // Agregat Statistik Mitra Platform
        $totalAll = Mitra::publiclyVisible()->count();
        $totalDinas = Mitra::publiclyVisible()->where('category', 'dinas')->count();
        $totalNonDinas = Mitra::publiclyVisible()->where('category', 'non_dinas')->count();

        return view('public.mitra.index', compact(
            'mitras',
            'regions',
            'serviceTypes',
            'totalAll',
            'totalDinas',
            'totalNonDinas'
        ));
    }

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
