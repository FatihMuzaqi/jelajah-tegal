<?php
namespace App\Policies;
use App\Models\User;use App\Models\WithdrawalClaim;
class WithdrawalClaimPolicy
{public function view(User $user,WithdrawalClaim $claim):bool{return $user->can('withdrawals.process')||($user->can('withdrawals.view')&&$user->mitraMemberships()->where('mitra_id',$claim->mitra_id)->where('status','active')->exists());}public function cancel(User $user,WithdrawalClaim $claim):bool{return $claim->submitted_by===$user->id&&$user->can('withdrawals.cancel');}}
