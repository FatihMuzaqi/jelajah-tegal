<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('feature-requests.review') === true;
    }

    public function rules(): array
    {
        return ['decision' => ['required', Rule::in(['approved', 'rejected'])], 'reason' => ['nullable', 'required_if:decision,rejected', 'string', 'max:2000']];
    }
}
