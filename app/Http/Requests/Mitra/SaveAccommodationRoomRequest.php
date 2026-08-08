<?php

namespace App\Http\Requests\Mitra;

use App\Models\ServiceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveAccommodationRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('accommodation.manage') ?? false;
    }

    public function rules(): array
    {
        $service = ServiceType::where('code', 'accommodation')->value('id');

        return ['name' => ['required', 'string', 'max:150'], 'description' => ['nullable', 'string'], 'room_type' => ['required', 'string', 'max:64'], 'kind' => ['required', Rule::in(['room', 'tent_plot'])], 'capacity_adults' => ['required', 'integer', 'min:1', 'max:100'], 'capacity_children' => ['required', 'integer', 'min:0', 'max:100'], 'nightly_price' => ['required', 'decimal:0,2', 'min:0'], 'sku' => ['nullable', 'string', 'max:100'], 'total_units' => ['required', 'integer', 'min:1', 'max:10000'], 'plot_count' => ['nullable', 'integer', 'min:1', 'max:10000', 'required_if:kind,tent_plot'], 'min_stay_nights' => ['nullable', 'integer', 'min:1', 'max:365'], 'max_stay_nights' => ['nullable', 'integer', 'min:1', 'max:365', 'gte:min_stay_nights'], 'advance_booking_days' => ['nullable', 'integer', 'min:0', 'max:1095'], 'availability_notes' => ['nullable', 'string', 'max:1000'], 'status' => ['required', Rule::in(['draft', 'active'])], 'bed_config' => ['nullable', 'array'], 'facilities' => ['nullable', 'array'], 'facilities.*' => [Rule::exists('facilities', 'id')->where(fn ($q) => $q->where('service_type_id', $service)->where('is_active', true))]];
    }
}
