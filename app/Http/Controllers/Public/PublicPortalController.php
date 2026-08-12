<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicSearchRequest;
use App\Models\ApplicationSetting;
use App\Models\CatalogEntity;
use App\Models\Category;
use App\Models\Mitra;
use App\Models\Region;
use App\Models\ServiceType;
use App\Support\FeatureFlags;
use Illuminate\Contracts\View\View;

class PublicPortalController extends Controller
{
    public function __construct(private readonly FeatureFlags $flags) {}

    public function home(PublicSearchRequest $request): View
    {
        $filters = $request->validated();
        $directoryEnabled = $this->flags->enabled('public-mitra-directory');

        $mitras = Mitra::query()
            ->publiclyVisible()
            ->select(['id', 'display_name', 'slug', 'description', 'region_id'])
            ->with([
                'region:id,name',
                'features' => fn ($query) => $query->where('status', 'enabled')
                    ->select(['id', 'mitra_id', 'service_type_id', 'status'])
                    ->with('serviceType:id,code,name'),
            ])
            ->when(! $directoryEnabled, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($filters['q'] ?? null, function ($query, string $term) {
                $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($term)).'%';
                $query->where(function ($nested) use ($term) {
                    $nested->where('display_name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhereHas('region', fn ($region) => $region->where('name', 'like', $term));
                });
            })
            ->when($filters['region'] ?? null, fn ($query, $region) => $query->where('region_id', $region))
            ->when($filters['service'] ?? null, fn ($query, $service) => $query->whereHas(
                'features',
                fn ($feature) => $feature->where('status', 'enabled')->whereHas('serviceType', fn ($type) => $type->where('code', $service))
            ))
            ->orderBy('display_name')
            ->paginate(9)
            ->withQueryString();

        $services = ServiceType::query()->orderBy('sort_order')->get(['id', 'code', 'name']);
        $categories = Category::query()->where('is_active', true)->with('serviceType:id,code,name')->orderBy('name')->get(['id', 'service_type_id', 'name', 'slug']);
        $regions = Region::query()->whereHas('mitras', fn ($query) => $query->publiclyVisible())->orderBy('name')->get(['id', 'name']);
        $visibleMitraQuery = Mitra::query()->publiclyVisible();
        $stats = [
            ['label' => 'Mitra aktif', 'value' => (clone $visibleMitraQuery)->count()],
            ['label' => 'Lokasi tersedia', 'value' => (clone $visibleMitraQuery)->whereNotNull('region_id')->distinct()->count('region_id')],
            ['label' => 'Layanan aktif', 'value' => ServiceType::query()->whereHas('categories', fn ($query) => $query->where('is_active', true))->count()],
        ];
        $featuredTourisms = CatalogEntity::publicTourism()
            ->with(['region', 'category', 'media', 'tourism', 'mitra', 'offers'])
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(6)
            ->get();

        $featuredAccommodations = CatalogEntity::publicAccommodation()
            ->with(['region', 'category', 'accommodation.rooms.offer', 'media', 'mitra'])
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(4)
            ->get();

        $popularMitras = Mitra::query()
            ->publiclyVisible()
            ->with([
                'region:id,name',
                'features' => fn ($query) => $query->where('status', 'enabled')->with('serviceType:id,code,name'),
                'media',
            ])
            ->withCount('catalogEntities')
            ->orderByDesc('catalog_entities_count')
            ->orderBy('display_name')
            ->limit(6)
            ->get();

        $allRegions = Region::orderBy('name')->get(['id', 'name', 'code']);

        return view('public.home', [
            'mitras' => $mitras,
            'popularMitras' => $popularMitras,
            'services' => $services,
            'categories' => $categories,
            'regions' => $allRegions,
            'stats' => $stats,
            'filters' => $filters,
            'domains' => config('public-portal.domains'),
            'aiPlannerEnabled' => $this->flags->enabled('public-ai-planner'),
            'newsletterEnabled' => $this->flags->enabled('public-newsletter'),
            'faq' => $this->publishedSetting('public.faq'),
            'featuredTourisms' => $featuredTourisms,
            'featuredAccommodations' => $featuredAccommodations,
        ]);
    }

    public function about(): View
    {
        return $this->contentPage('Tentang Lokantara', 'public.about', 'Informasi resmi mengenai Lokantara belum diterbitkan.');
    }

    public function faq(): View
    {
        return $this->contentPage('Pertanyaan yang Sering Diajukan', 'public.faq', 'FAQ resmi belum diterbitkan.');
    }

    public function contact(): View
    {
        return $this->contentPage('Kontak', 'public.contact', 'Informasi kontak resmi belum diterbitkan.');
    }

    public function privacy(): View
    {
        return $this->contentPage('Kebijakan Privasi', 'public.privacy', 'Kebijakan privasi belum diterbitkan. Jangan memproses data publik sebelum dokumen ini disetujui.');
    }

    public function terms(): View
    {
        return $this->contentPage('Syarat dan Ketentuan', 'public.terms', 'Syarat dan ketentuan belum diterbitkan.');
    }

    private function contentPage(string $title, string $key, string $emptyMessage): View
    {
        return view('public.content-page', [
            'title' => $title,
            'content' => $this->publishedSetting($key),
            'emptyMessage' => $emptyMessage,
        ]);
    }

    private function publishedSetting(string $key): ?array
    {
        $setting = ApplicationSetting::query()
            ->whereNull('mitra_id')
            ->where('key_name', $key)
            ->where('is_secret', false)
            ->first(['value_json']);
        $content = $setting?->value_json;

        return is_array($content) && ($content['published'] ?? false) === true ? $content : null;
    }
}
