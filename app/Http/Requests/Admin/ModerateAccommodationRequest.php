<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModerateAccommodationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('accommodation.moderate') ?? false;
    }

    public function rules(): array
    {
        return ['decision' => ['required', Rule::in(['approve', 'reject', 'takedown'])], 'reason' => ['nullable', 'string', 'max:2000', 'required_if:decision,reject,takedown']];
    }
}
