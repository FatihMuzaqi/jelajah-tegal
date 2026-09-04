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
        $baseSlug = null;
        if ($this->filled('slug')) {
            $baseSlug = \Illuminate\Support\Str::slug($this->slug);
        } elseif ($this->filled('display_name')) {
            $baseSlug = \Illuminate\Support\Str::slug($this->display_name);
        } elseif ($this->filled('legal_name')) {
            $baseSlug = \Illuminate\Support\Str::slug($this->legal_name);
        }

        if ($baseSlug) {
            $slug = $baseSlug;
            $counter = 1;
            while (\App\Models\Mitra::where('slug', $slug)->exists()) {
                $counter++;
                $slug = "{$baseSlug}-{$counter}";
            }
            $this->merge([
                'slug' => $slug,
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
            'slug' => ['required', 'string', 'max:191', Rule::unique('mitras', 'slug')->whereNull('deleted_at')],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
        ];
    }
}
