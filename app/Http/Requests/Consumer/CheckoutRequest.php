<?php
namespace App\Http\Requests\Consumer;
use Illuminate\Foundation\Http\FormRequest;use Illuminate\Validation\Rule;
class CheckoutRequest extends FormRequest
{public function authorize():bool{return $this->user()!==null;}public function rules():array{return['idempotency_key'=>'required|string|min:8|max:191','domain'=>['required',Rule::in(['tourism','event','culinary','rental','accommodation'])],'reference_id'=>'required|string|size:26','quantity'=>'required_unless:domain,culinary,rental|integer|min:1|max:100','service_date'=>'required_if:domain,tourism|date|after_or_equal:today','start_date'=>'required_if:domain,accommodation|date|after_or_equal:today','end_date'=>'required_if:domain,accommodation|date|after:start_date','adults'=>'nullable|integer|min:1|max:100','children'=>'nullable|integer|min:0|max:100','voucher_code'=>'nullable|string|max:64'];}}
