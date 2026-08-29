<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CatalogEntity;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Region;
use App\Models\Review;
use App\Models\ServiceType;
use App\Support\FeatureFlags;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourismController extends Controller
{
    public function __construct(private FeatureFlags $flags) {}

    public function index(Request $request): View
    {
        abort_unless($this->flags->enabled('public-tourism'), 404);
        $data = $request->validate(['q' => 'nullable|string|max:100', 'category' => 'nullable|string|max:191', 'region' => 'nullable|integer', 'featured' => 'nullable|boolean', 'hidden_gem' => 'nullable|boolean', 'latitude' => 'nullable|numeric|between:-90,90', 'longitude' => 'nullable|numeric|between:-180,180', 'radius' => 'nullable|numeric|min:1|max:100']);
        $query = CatalogEntity::query()->publicTourism()->with(['category', 'region', 'tourism', 'media', 'mitra', 'offers.ticketPackage', 'offers.availabilities']);
        if ($q = $data['q'] ?? null) {
            $query->where(fn ($x) => $x->where('name', 'like', '%'.$q.'%')->orWhere('description', 'like', '%'.$q.'%'));
        }
        if ($category = $data['category'] ?? null) {
            $query->whereHas('category', fn ($x) => $x->where('slug', $category));
        }
        if ($region = $data['region'] ?? null) {
            $query->where('region_id', $region);
        }
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }
        if ($request->boolean('hidden_gem')) {
            $query->whereHas('tourism', fn ($x) => $x->where('is_hidden_gem', true));
        }
        if (isset($data['latitude'], $data['longitude'])) {
            $radius = (float) ($data['radius'] ?? 25) * 1000;
            $query->join('catalog_locations', 'catalog_locations.catalog_entity_id', '=', 'catalog_entities.id')->select('catalog_entities.*')->selectRaw('ST_Distance_Sphere(catalog_locations.location, ST_PointFromText(CONCAT("POINT(", ?, " ", ?, ")"), 4326)) AS distance_meters', [$data['longitude'], $data['latitude']])->whereRaw('ST_Distance_Sphere(catalog_locations.location, ST_PointFromText(CONCAT("POINT(", ?, " ", ?, ")"), 4326)) <= ?', [$data['longitude'], $data['latitude'], $radius])->orderBy('distance_meters');
        } else {
            $query->orderByDesc('is_featured')->latest('published_at');
        }
        $service = ServiceType::where('code', 'tourism')->first();

        return view('public.tourism.index', ['items' => $query->paginate(12)->withQueryString(), 'categories' => Category::where('service_type_id', $service?->id)->where('is_active', true)->orderBy('name')->get(), 'regions' => Region::orderBy('name')->get()]);
    }

    public function show(string $slug): View
    {
        abort_unless($this->flags->enabled('public-tourism'), 404);
        $tourism = CatalogEntity::query()->publicTourism()->where('slug', $slug)->with([
            'mitra', 'category', 'region', 'tourism', 'location', 'facilities', 'operatingHours', 'media',
            'offers.ticketPackage', 'offers.availabilities',
            'reviews' => fn ($q) => $q->where('status', 'published')->with(['user', 'replies.author', 'replies.mitra'])->latest(),
        ])->firstOrFail();

        return view('public.tourism.show', compact('tourism'));
    }

    public function favorite(Request $request, string $slug): RedirectResponse
    {
        abort_unless($request->user()->can('favorites.manage'), 403);
        $entity = CatalogEntity::publicTourism()->where('slug', $slug)->firstOrFail();
        Favorite::firstOrCreate(['user_id' => $request->user()->id, 'catalog_entity_id' => $entity->id]);

        return back()->with('status', 'Ditambahkan ke favorit.');
    }

    public function unfavorite(Request $request, string $slug): RedirectResponse
    {
        $entity = CatalogEntity::publicTourism()->where('slug', $slug)->firstOrFail();
        Favorite::where('user_id', $request->user()->id)->where('catalog_entity_id', $entity->id)->delete();

        return back()->with('status', 'Dihapus dari favorit.');
    }

    public function review(Request $request, string $slug): RedirectResponse
    {
        abort_unless($request->user()->can('reviews.create'), 403);
        $entity = CatalogEntity::publicTourism()->where('slug', $slug)->firstOrFail();
        $data = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'title' => ['nullable', 'string', 'max:191', new \App\Rules\CleanContent],
            'body' => ['required', 'string', 'min:5', 'max:5000', new \App\Rules\CleanContent],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'rating.required' => 'Silakan pilih rating bintang.',
            'body.required' => 'Isi ulasan pengalaman tidak boleh kosong.',
            'body.min' => 'Isi ulasan minimal 5 karakter.',
            'photos.max' => 'Maksimal 5 foto ulasan.',
            'photos.*.image' => 'File harus berupa gambar valid.',
            'photos.*.max' => 'Ukuran foto maksimal 5MB per file.',
        ]);

        if ($request->hasFile('photos')) {
            $webpConverter = app(\App\Services\WebpConverter::class);
            $uploadedPaths = [];
            foreach ($request->file('photos') as $photo) {
                if (! $photo->isValid()) continue;
                $converted = $webpConverter->convert($photo, quality: 82, maxWidth: 1200);
                if ($converted) {
                    $filename = str()->ulid().'.webp';
                    $objectKey = 'reviews/'.$filename;
                    \Illuminate\Support\Facades\Storage::disk('public')->put($objectKey, file_get_contents($converted['path']), 'public');
                    @unlink($converted['path']);
                    $uploadedPaths[] = $objectKey;
                } else {
                    $uploadedPaths[] = $photo->store('reviews', 'public');
                }
            }
            if (! empty($uploadedPaths)) {
                $data['photos'] = $uploadedPaths;
            }
        }

        $review = Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'catalog_entity_id' => $entity->id],
            $data + ['status' => 'published']
        );

        $review->syncCatalogRating();

        return back()->with('status', 'Ulasan Anda berhasil dikirim dan dipublikasikan!');
    }
}
