<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mitras.update') === true;
    }

    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:5000'],
            'contact_email' => ['nullable', 'email:rfc', 'max:191'],
            'contact_phone' => ['nullable', 'string', 'max:32', 'regex:/^[0-9+() .-]+$/'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'address' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
