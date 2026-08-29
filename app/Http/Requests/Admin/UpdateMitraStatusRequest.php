<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMitraStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mitras.create') === true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['active', 'rejected', 'suspended'])],
            'reason' => ['nullable', 'required_if:status,rejected', 'required_if:status,suspended', 'string', 'max:2000'],
            'is_verified' => ['nullable', 'boolean'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
