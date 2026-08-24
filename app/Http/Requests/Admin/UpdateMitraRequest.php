<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMitraRequest extends FormRequest
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
        $mitraId = $this->route('mitra')?->id ?? $this->route('mitra');

        return [
            'category' => ['required', 'string', Rule::in(['dinas', 'non_dinas'])],
            'legal_name' => ['required', 'string', 'max:191'],
            'display_name' => ['required', 'string', 'max:191'],
            'slug' => ['required', 'alpha_dash', 'max:191', Rule::unique('mitras', 'slug')->ignore($mitraId)],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email:rfc', 'max:191'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'registration_number' => ['nullable', 'string', 'max:100'],
        ];
    }
}
