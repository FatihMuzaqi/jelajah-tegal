<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $query = Feedback::query()->latest();

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($term = trim($request->input('q', ''))) {
            $query->where(function ($nested) use ($term) {
                $nested->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('subject', 'like', "%{$term}%")
                    ->orWhere('message', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        $feedbacks = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Feedback::count(),
            'pending' => Feedback::where('status', 'pending')->count(),
            'saran' => Feedback::where('type', 'saran')->count(),
            'kritik' => Feedback::where('type', 'kritik')->count(),
        ];

        return view('admin.feedbacks.index', compact('feedbacks', 'stats'));
    }

    public function update(Request $request, Feedback $feedback): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewed,replied,archived'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $feedback->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $feedback->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Status pesan masukan berhasil diperbarui.');
    }

    public function destroy(Feedback $feedback): RedirectResponse
    {
        $feedback->delete();

        return back()->with('status', 'Pesan masukan berhasil dihapus.');
    }
}
