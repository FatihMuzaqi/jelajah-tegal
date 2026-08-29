<?php

namespace App\Http\Controllers\Public\Concerns;

use App\Models\CatalogEntity;
use App\Rules\CleanContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HandlesCatalogInteractions
{
    protected function toggleFavorite(Request $request, CatalogEntity $entity, bool $add): RedirectResponse
    {
        abort_unless($entity->status === 'published', 404);
        if ($add) {
            $entity->favorites()->firstOrCreate(['user_id' => $request->user()->id]);
        } else {
            $entity->favorites()->where('user_id', $request->user()->id)->delete();
        }

        return back()->with('status', $add ? 'Ditambahkan ke favorit.' : 'Dihapus dari favorit.');
    }

    protected function storeReview(Request $request, CatalogEntity $entity): RedirectResponse
    {
        abort_unless($entity->status === 'published', 404);
        abort_unless($request->user()->can('reviews.create'), 403);

        $data = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'title' => ['nullable', 'string', 'max:191', new CleanContent],
            'body' => ['required', 'string', 'min:5', 'max:3000', new CleanContent],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'rating.required' => 'Silakan pilih rating bintang.',
            'body.required' => 'Isi ulasan tidak boleh kosong.',
            'body.min' => 'Isi ulasan minimal 5 karakter.',
            'photos.max' => 'Maksimal 5 foto ulasan.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran foto maksimal 5MB per file.',
        ]);

        $photoPaths = $this->processReviewPhotos($request);
        if ($photoPaths !== null) {
            $data['photos'] = $photoPaths;
        }

        $review = $entity->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id, 'catalog_entity_id' => $entity->id],
            $data + ['status' => 'published']
        );

        $review->syncCatalogRating();

        return back()->with('status', 'Ulasan Anda berhasil dikirim dan dipublikasikan!');
    }

    protected function processReviewPhotos(Request $request): ?array
    {
        if (! $request->hasFile('photos')) {
            return null;
        }

        $webpConverter = app(\App\Services\WebpConverter::class);
        $uploadedPaths = [];

        foreach ($request->file('photos') as $photo) {
            if (! $photo->isValid()) {
                continue;
            }

            $converted = $webpConverter->convert($photo, quality: 82, maxWidth: 1200);

            if ($converted) {
                $filename = str()->ulid().'.webp';
                $objectKey = 'reviews/'.$filename;
                \Illuminate\Support\Facades\Storage::disk('public')->put($objectKey, file_get_contents($converted['path']), 'public');
                @unlink($converted['path']);
                $uploadedPaths[] = $objectKey;
            } else {
                $objectKey = $photo->store('reviews', 'public');
                $uploadedPaths[] = $objectKey;
            }
        }

        return ! empty($uploadedPaths) ? $uploadedPaths : null;
    }
}
