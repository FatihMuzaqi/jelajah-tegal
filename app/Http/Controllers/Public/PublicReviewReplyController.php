<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Rules\CleanContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicReviewReplyController extends Controller
{
    /**
     * Store a reply to a published review.
     */
    public function store(Request $request, Review $review): RedirectResponse
    {
        abort_unless($review->status === 'published', 404);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:2000', new CleanContent],
        ], [
            'body.required' => 'Isi balasan ulasan tidak boleh kosong.',
            'body.min' => 'Balasan ulasan minimal 3 karakter.',
            'body.max' => 'Balasan ulasan maksimal 2000 karakter.',
        ]);

        $user = $request->user();
        $mitraId = null;

        // If the replying user is the mitra owner of this catalog entity
        if ($user->mitra_id && $review->catalogEntity && $user->mitra_id === $review->catalogEntity->mitra_id) {
            $mitraId = $user->mitra_id;
        }

        $review->replies()->create([
            'replied_by' => $user->id,
            'mitra_id' => $mitraId,
            'body' => $data['body'],
            'status' => 'published',
        ]);

        return back()->with('status', 'Balasan ulasan Anda berhasil dikirim!');
    }
}
