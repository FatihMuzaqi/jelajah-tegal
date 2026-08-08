<?php

namespace App\Http\Controllers\Mitra;

use App\Actions\Withdrawals\SubmitWithdrawal;
use App\Actions\Withdrawals\TransitionWithdrawal;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;
use App\Http\Requests\Mitra\StoreWithdrawalRequest;
use App\Models\MitraBankAccount;
use App\Models\WithdrawalClaim;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    use ResolvesActiveMitra;
    public function index(Request $request): View {$mitra=$this->activeMitra($request);abort_unless($request->user()->can('withdrawals.view'),403);return view('mitra.withdrawals.index',['balance'=>$mitra->balance,'withdrawals'=>$mitra->withdrawals()->latest()->paginate(20)]);}
    public function create(Request $request): View {$mitra=$this->activeMitra($request);abort_unless($request->user()->can('withdrawals.submit'),403);return view('mitra.withdrawals.form',['balance'=>$mitra->balance,'accounts'=>$mitra->bankAccounts()->where('status','verified')->whereNotNull('verified_at')->get()]);}
    public function store(StoreWithdrawalRequest $request,SubmitWithdrawal $action): RedirectResponse {$mitra=$this->activeMitra($request);$bank=MitraBankAccount::findOrFail($request->validated('bank_account_id'));$claim=$action->execute($mitra,$request->user(),$bank,$request->validated('amount'),$request->validated('idempotency_key'),$request->validated('notes'));return redirect()->route('mitra.withdrawals.show',$claim)->with('status','Withdrawal diajukan dan saldo telah ditahan.');}
    public function show(Request $request,WithdrawalClaim $withdrawal): View {$this->authorize('view',$withdrawal);abort_unless($withdrawal->mitra_id===$this->activeMitra($request)->id,404);return view('mitra.withdrawals.show',['withdrawal'=>$withdrawal->load(['transfer','journals.lines'])]);}
    public function cancel(Request $request,WithdrawalClaim $withdrawal,TransitionWithdrawal $action): RedirectResponse {$this->authorize('cancel',$withdrawal);abort_unless($withdrawal->mitra_id===$this->activeMitra($request)->id,404);$data=$request->validate(['reason'=>'required|string|min:5|max:1000']);$action->execute($withdrawal,'cancel',$request->user(),$data);return back()->with('status','Withdrawal dibatalkan dan saldo dikembalikan.');}
}
