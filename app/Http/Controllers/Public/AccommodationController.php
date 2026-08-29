<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AccommodationRoom;
use App\Models\CatalogEntity;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Region;
use App\Models\Review;
use App\Models\ServiceType;
use App\Support\FeatureFlags;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function __construct(private FeatureFlags $flags) {}

    public function index(Request $request): View
    {
        abort_unless($this->flags->enabled('public-accommodation'), 404);
        $data = $request->validate(['q' => 'nullable|string|max:100', 'category' => 'nullable|string|max:191', 'region' => 'nullable|integer', 'min_price' => 'nullable|numeric|min:0', 'max_price' => 'nullable|numeric|min:0', 'adults' => 'nullable|integer|min:1|max:100', 'children' => 'nullable|integer|min:0|max:100', 'facility' => 'nullable|integer', 'latitude' => 'nullable|numeric|between:-90,90', 'longitude' => 'nullable|numeric|between:-180,180', 'radius' => 'nullable|numeric|min:1|max:100', 'nearby_destination' => 'nullable|string|max:191', 'sort' => 'nullable|in:latest,price_asc,rating,distance']);
        if (isset($data['min_price'], $data['max_price']) && $data['max_price'] < $data['min_price']) {
            throw ValidationException::withMessages(['max_price' => 'Harga maksimum tidak boleh lebih kecil dari harga minimum.']);
        }
        if (isset($data['latitude']) xor isset($data['longitude'])) {
            throw ValidationException::withMessages(['latitude' => 'Latitude dan longitude wajib diisi berpasangan.']);
        }
        if (($data['sort'] ?? null) === 'distance' && ! (isset($data['latitude'], $data['longitude']) || isset($data['nearby_destination']))) {
            throw ValidationException::withMessages(['sort' => 'Pengurutan jarak memerlukan koordinat atau destinasi acuan.']);
        }
        $query = CatalogEntity::query()->publicAccommodation()->with(['category', 'region', 'accommodation.rooms.offer', 'media', 'mitra']);
        if ($q = $data['q'] ?? null) {
            $query->where(fn ($x) => $x->where('name', 'like', '%'.$q.'%')->orWhere('description', 'like', '%'.$q.'%'));
        }
        if ($category = $data['category'] ?? null) {
            $query->whereHas('category', fn ($x) => $x->where('slug', $category));
        }
        if ($region = $data['region'] ?? null) {
            $query->where('region_id', $region);
        }
        if ($facility = $data['facility'] ?? null) {
            $query->whereHas('facilities', fn ($x) => $x->where('facilities.id', $facility));
        }
        $query->whereHas('accommodation.rooms', function ($rooms) use ($data) {
            $rooms->where('status', 'active')->whereHas('offer', function ($offer) use ($data) {
                $offer->whereIn('status', ['active', 'published']);
                if (isset($data['min_price'])) {
                    $offer->where('price', '>=', $data['min_price']);
                }
                if (isset($data['max_price'])) {
                    $offer->where('price', '<=', $data['max_price']);
                }
            });
            if (isset($data['adults'])) {
                $rooms->where('capacity_adults', '>=', $data['adults']);
            }
            if (isset($data['children'])) {
                $rooms->where('capacity_children', '>=', $data['children']);
            }
        });
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;
        if ($slug = $data['nearby_destination'] ?? null) {
            $destination = CatalogEntity::publicTourism()->where('slug', $slug)->with('location')->firstOrFail();
            $latitude = $destination->location->latitude;
            $longitude = $destination->location->longitude;
        }
        if ($latitude !== null && $longitude !== null) {
            $radius = (float) ($data['radius'] ?? 25) * 1000;
            $query->join('catalog_locations', 'catalog_locations.catalog_entity_id', '=', 'catalog_entities.id')->select('catalog_entities.*')->selectRaw('ST_Distance_Sphere(catalog_locations.location, ST_PointFromText(CONCAT("POINT(", ?, " ", ?, ")"), 4326)) AS distance_meters', [$longitude, $latitude])->whereRaw('ST_Distance_Sphere(catalog_locations.location, ST_PointFromText(CONCAT("POINT(", ?, " ", ?, ")"), 4326)) <= ?', [$longitude, $latitude, $radius])->orderBy('distance_meters');
        } elseif (($data['sort'] ?? null) === 'rating') {
            $query->orderByDesc('rating_average');
        } elseif (($data['sort'] ?? null) === 'price_asc') {
            $query->withMin(['offers as minimum_price' => fn ($q) => $q->whereIn('status', ['active', 'published'])], 'price')->orderBy('minimum_price');
        } else {
            $query->latest('published_at');
        }
        $service = ServiceType::where('code', 'accommodation')->first();

        return view('public.accommodation.index', ['items' => $query->paginate(12)->withQueryString(), 'categories' => Category::where('service_type_id', $service?->id)->where('is_active', true)->orderBy('name')->get(), 'facilities' => $service?->facilities()->where('is_active', true)->orderBy('name')->get() ?? collect(), 'regions' => Region::orderBy('name')->get()]);
    }

    public function show(string $slug): View
    {
        abort_unless($this->flags->enabled('public-accommodation'), 404);
        $accommodation = CatalogEntity::publicAccommodation()->where('slug', $slug)->with([
            'mitra', 'category', 'region', 'location', 'facilities', 'media',
            'accommodation.rooms' => fn ($q) => $q->where('status', 'active')->with(['offer.availabilities', 'facilities', 'media']),
            'reviews' => fn ($q) => $q->where('status', 'published')->with(['user', 'replies.author', 'replies.mitra'])->latest(),
        ])->firstOrFail();

        return view('public.accommodation.show', compact('accommodation'));
    }

    public function room(string $slug, AccommodationRoom $room): View
    {
        abort_unless($this->flags->enabled('public-accommodation'), 404);
        $accommodation = CatalogEntity::publicAccommodation()->where('slug', $slug)->with(['accommodation', 'mitra', 'region', 'category', 'location', 'media'])->firstOrFail();
        abort_unless($room->accommodation_id === $accommodation->accommodation->id && $room->status === 'active' && in_array($room->offer->status, ['active', 'published']), 404);
        $room->load(['facilities', 'media', 'offer.availabilities']);

        return view('public.accommodation.room', compact('accommodation', 'room'));
    }

    public function favorite(Request $request, string $slug): RedirectResponse
    {
        abort_unless($request->user()->can('favorites.manage'), 403);
        $entity = CatalogEntity::publicAccommodation()->where('slug', $slug)->firstOrFail();
        Favorite::firstOrCreate(['user_id' => $request->user()->id, 'catalog_entity_id' => $entity->id]);

        return back()->with('status', 'Penginapan ditambahkan ke favorit.');
    }

    public function unfavorite(Request $request, string $slug): RedirectResponse
    {
        $entity = CatalogEntity::publicAccommodation()->where('slug', $slug)->firstOrFail();
        Favorite::where('user_id', $request->user()->id)->where('catalog_entity_id', $entity->id)->delete();

        return back()->with('status', 'Penginapan dihapus dari favorit.');
    }

    public function review(Request $request, string $slug): RedirectResponse
    {
        abort_unless($request->user()->can('reviews.create'), 403);
        $entity = CatalogEntity::publicAccommodation()->where('slug', $slug)->firstOrFail();
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
