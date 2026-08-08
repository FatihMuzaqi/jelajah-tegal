<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mitras.update') === true;
    }

    public function rules(): array
    {
        return ['service_type_id' => ['required', 'integer', 'exists:service_types,id'], 'reason' => ['required', 'string', 'max:2000']];
    }
}
