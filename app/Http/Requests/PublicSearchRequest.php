<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'integer', 'exists:regions,id'],
            'service' => ['nullable', 'string', 'max:32', 'exists:service_types,code'],
        ];
    }
}
