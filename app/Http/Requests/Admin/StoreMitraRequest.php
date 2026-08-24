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

    protected function prepareForValidation(): void
    {
        if ($this->filled('slug')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->slug),
            ]);
        } elseif ($this->filled('display_name')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->display_name),
            ]);
        } elseif ($this->filled('legal_name')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->legal_name),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'owner_name' => ['required', 'string', 'max:191'],
            'owner_email' => ['required', 'email:rfc', 'max:191'],
            'category' => ['required', 'string', Rule::in(['dinas', 'non_dinas'])],
            'legal_name' => ['required', 'string', 'max:191'],
            'display_name' => ['required', 'string', 'max:191'],
            'slug' => ['required', 'alpha_dash', 'max:191', Rule::unique('mitras', 'slug')],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
        ];
    }
}
