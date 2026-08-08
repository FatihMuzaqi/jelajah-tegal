<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\MitraBankAccount;use App\Services\AuditLogger;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;
class BankAccountVerificationController extends Controller
{public function update(Request $r,MitraBankAccount $account,AuditLogger $audit):RedirectResponse{abort_unless($r->user()->can('bank-accounts.verify'),403);$data=$r->validate(['decision'=>'required|in:verify,reject']);$before=$account->status;$account->update(['status'=>$data['decision']==='verify'?'verified':'rejected','verified_by'=>$data['decision']==='verify'?$r->user()->id:null,'verified_at'=>$data['decision']==='verify'?now():null]);$audit->record('mitra.bank_account_'.$data['decision'],$account,['status'=>$before],['status'=>$account->status],$r->user());return back();}}
