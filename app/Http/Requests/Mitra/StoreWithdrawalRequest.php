<?php
namespace App\Http\Requests\Mitra;
use Illuminate\Foundation\Http\FormRequest;
class StoreWithdrawalRequest extends FormRequest
{public function authorize():bool{return $this->user()?->can('withdrawals.submit')??false;}public function rules():array{return['bank_account_id'=>'required|string|size:26','amount'=>'required|decimal:0,2|min:1','idempotency_key'=>'required|string|min:8|max:191','notes'=>'nullable|string|max:1000'];}}
