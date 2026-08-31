<?php

namespace App\Http\Requests\Mitra;

use App\Models\ServiceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTourismRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tourism.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name') && blank($this->input('slug'))) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->input('name')),
            ]);
        } elseif ($this->filled('slug')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->input('slug')),
            ]);
        }
    }

    public function rules(): array
    {
        $service = ServiceType::where('code', 'tourism')->value('id');
        $id = $this->route('tourism')?->id;

        return ['name' => ['required', 'string', 'max:191'], 'slug' => ['required', 'alpha_dash', 'max:191', Rule::unique('catalog_entities')->ignore($id)], 'category_id' => ['required', Rule::exists('categories', 'id')->where(fn ($q) => $q->where('service_type_id', $service)->where('is_active', true))], 'region_id' => ['required', 'exists:regions,id'], 'description' => ['nullable', 'string'], 'address' => ['nullable', 'string'], 'destination_type' => ['required', Rule::in(['nature', 'culture', 'recreation', 'education', 'religious', 'other'])], 'visit_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'], 'badge' => ['nullable', 'string', 'max:64'], 'is_hidden_gem' => ['sometimes', 'boolean'], 'is_featured' => ['sometimes', 'boolean'], 'latitude' => ['required', 'numeric', 'between:-90,90'], 'longitude' => ['required', 'numeric', 'between:-180,180'], 'facilities' => ['nullable', 'array'], 'facilities.*' => [Rule::exists('facilities', 'id')->where(fn ($q) => $q->where('service_type_id', $service)->where('is_active', true))]];
    }
}
