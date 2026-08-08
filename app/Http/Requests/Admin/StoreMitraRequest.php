<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMitraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mitras.create') === true;
    }

    public function rules(): array
    {
        return [
            'owner_name' => ['required', 'string', 'max:191'],
            'owner_email' => ['required', 'email:rfc', 'max:191'],
            'legal_name' => ['required', 'string', 'max:191'],
            'display_name' => ['required', 'string', 'max:191'],
            'slug' => ['required', 'alpha_dash', 'max:191', Rule::unique('mitras', 'slug')],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
        ];
    }
}
