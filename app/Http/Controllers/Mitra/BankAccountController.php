<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\StoreBankAccountRequest;
use App\Models\MitraBankAccount;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    use ResolvesActiveMitra;

    public function index(Request $request): View
    {
        $mitra = $this->activeMitra($request);
        abort_unless($request->user()->can('bank-accounts.manage'), 403);
        $accounts = $mitra->bankAccounts()->get()->map(fn ($account) => [
            'model' => $account,
            'name' => $account->decrypted_account_name,
            'masked' => $account->masked_number,
        ]);

        return view('mitra.bank-accounts.index', compact('mitra', 'accounts'));
    }

    public function store(StoreBankAccountRequest $request, AuditLogger $audit): RedirectResponse
    {
        $mitra = $this->activeMitra($request);
        $number = preg_replace('/\D+/', '', $request->validated('account_number'));
        DB::transaction(function () use ($request, $mitra, $number, $audit) {
            if ($request->boolean('is_primary')) {
                $mitra->bankAccounts()->update(['is_primary' => false]);
            }
            $account = $mitra->bankAccounts()->create(['bank_code' => strtoupper($request->validated('bank_code')), 'account_name_encrypted' => $request->validated('account_name'), 'account_number_encrypted' => $number, 'account_fingerprint' => hash_hmac('sha256', $number, config('app.key')), 'status' => 'pending', 'is_primary' => $request->boolean('is_primary')]);
            $audit->record('mitra.bank_account_added', $account, [], ['bank_code' => $account->bank_code, 'last_four' => substr($number, -4)], $request->user());
        });

        return back()->with('status', 'Rekening bank ditambahkan dan menunggu verifikasi.');
    }

    public function destroy(Request $request, MitraBankAccount $account, AuditLogger $audit): RedirectResponse
    {
        $this->authorize('update', $account);
        abort_unless($account->mitra_id === $this->activeMitra($request)->id, 404);
        $audit->record('mitra.bank_account_removed', $account, ['status' => $account->status], [], $request->user());
        $account->delete();

        return back()->with('status', 'Rekening bank dihapus.');
    }
}
