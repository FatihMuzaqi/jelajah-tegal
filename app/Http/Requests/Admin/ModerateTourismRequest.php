<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModerateTourismRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tourism.moderate') ?? false;
    }

    public function rules(): array
    {
        return ['decision' => ['required', Rule::in(['approve', 'reject', 'takedown'])], 'reason' => ['nullable', 'string', 'max:2000', 'required_if:decision,reject,takedown']];
    }
}
