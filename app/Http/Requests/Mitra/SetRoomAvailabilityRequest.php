<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;

class SetRoomAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('accommodation.manage') ?? false;
    }

    public function rules(): array
    {
        return ['start_date' => ['required', 'date', 'after_or_equal:today'], 'end_date' => ['required', 'date', 'after_or_equal:start_date', 'before_or_equal:'.now()->addYear()->toDateString()], 'available_units' => ['required', 'integer', 'min:0'], 'price_override' => ['nullable', 'decimal:0,2', 'min:0'], 'is_blocked' => ['sometimes', 'boolean']];
    }
}
