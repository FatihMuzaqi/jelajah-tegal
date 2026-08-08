<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('bank-accounts.manage') === true;
    }

    public function rules(): array
    {
        return ['bank_code' => ['required', 'alpha_num', 'max:32'], 'account_name' => ['required', 'string', 'max:191'], 'account_number' => ['required', 'digits_between:6,30'], 'is_primary' => ['nullable', 'boolean']];
    }
}
