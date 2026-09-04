<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatabaseNotification;
use App\Models\MitraBankAccount;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankAccountVerificationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('bank-accounts.verify'), 403);

        $counts = [
            'total' => MitraBankAccount::count(),
            'pending' => MitraBankAccount::where('status', 'pending')->count(),
            'verified' => MitraBankAccount::where('status', 'verified')->count(),
            'rejected' => MitraBankAccount::where('status', 'rejected')->count(),
        ];

        $query = MitraBankAccount::query()
            ->with(['mitra', 'verifier:id,name'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('bank')) {
            $query->where('bank_code', strtoupper($request->query('bank')));
        }

        if ($request->filled('q')) {
            $searchTerm = '%' . $request->query('q') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('bank_code', 'like', $searchTerm)
                    ->orWhereHas('mitra', function ($mq) use ($searchTerm) {
                        $mq->where('display_name', 'like', $searchTerm)
                            ->orWhere('legal_name', 'like', $searchTerm)
                            ->orWhere('slug', 'like', $searchTerm);
                    });
            });
        }

        $accounts = $query->paginate(20)->withQueryString();

        return view('admin.bank-accounts.index', compact('accounts', 'counts'));
    }

    public function update(Request $request, MitraBankAccount $account, AuditLogger $audit): RedirectResponse
    {
        abort_unless($request->user()->can('bank-accounts.verify'), 403);

        $validated = $request->validate([
            'decision' => ['required', 'in:verify,reject'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $before = $account->status;
        $isVerify = $validated['decision'] === 'verify';

        $account->update([
            'status' => $isVerify ? 'verified' : 'rejected',
            'verified_by' => $isVerify ? $request->user()->id : null,
            'verified_at' => $isVerify ? now() : null,
        ]);

        $audit->record(
            'mitra.bank_account_' . $validated['decision'],
            $account,
            ['status' => $before],
            ['status' => $account->status, 'reason' => $validated['reason'] ?? null],
            $request->user()
        );

        // Kirim notifikasi ke Mitra
        if ($account->mitra && $account->mitra->owner_user_id) {
            DatabaseNotification::create([
                'user_id' => $account->mitra->owner_user_id,
                'mitra_id' => $account->mitra_id,
                'type' => 'bank_account.reviewed',
                'data' => [
                    'title' => 'Verifikasi Rekening Bank',
                    'message' => 'Rekening ' . $account->bank_code . ' (' . $account->masked_number . ') atas nama ' . $account->decrypted_account_name . ' telah ' . ($isVerify ? 'disetujui' : 'ditolak') . ($validated['reason'] ?? false ? '. Alasan: ' . $validated['reason'] : '.'),
                ],
            ]);
        }

        $message = $isVerify 
            ? 'Rekening bank mitra berhasil diverifikasi.' 
            : 'Rekening bank mitra telah ditolak.';

        return back()->with('status', $message);
    }
}
