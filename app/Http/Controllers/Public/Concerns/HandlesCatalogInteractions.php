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
        ], [
            'rating.required' => 'Silakan pilih rating bintang.',
            'body.required' => 'Isi ulasan tidak boleh kosong.',
            'body.min' => 'Isi ulasan minimal 5 karakter.',
        ]);

        $review = $entity->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id, 'catalog_entity_id' => $entity->id],
            $data + ['status' => 'published']
        );

        $review->syncCatalogRating();

        return back()->with('status', 'Ulasan Anda berhasil dikirim dan dipublikasikan!');
    }
}
