<?php

namespace App\Http\Requests\Mitra;

use App\Models\ServiceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveAccommodationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('accommodation.manage') ?? false;
    }

    public function rules(): array
    {
        $service = ServiceType::where('code', 'accommodation')->value('id');
        $id = $this->route('accommodation')?->id;

        return ['name' => ['required', 'string', 'max:191'], 'slug' => ['required', 'alpha_dash', 'max:191', Rule::unique('catalog_entities')->ignore($id)], 'category_id' => ['required', Rule::exists('categories', 'id')->where(fn ($q) => $q->where('service_type_id', $service)->where('is_active', true))], 'region_id' => ['required', 'exists:regions,id'], 'description' => ['nullable', 'string'], 'address' => ['nullable', 'string'], 'property_type' => ['required', Rule::in(['hotel', 'homestay', 'villa', 'camping_ground', 'resort'])], 'check_in_time' => ['nullable', 'date_format:H:i'], 'check_out_time' => ['nullable', 'date_format:H:i'], 'star_rating' => ['nullable', 'integer', 'between:1,5'], 'is_featured' => ['sometimes', 'boolean'], 'latitude' => ['required', 'numeric', 'between:-90,90'], 'longitude' => ['required', 'numeric', 'between:-180,180'], 'facilities' => ['nullable', 'array'], 'facilities.*' => [Rule::exists('facilities', 'id')->where(fn ($q) => $q->where('service_type_id', $service)->where('is_active', true))]];
    }
}
