<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super-admin') || $this->user()?->can('users.manage') === true;
    }

    public function rules(): array
    {
        $adminId = $this->route('admin')?->id ?? $this->route('admin');

        return [
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email:rfc,dns', 'max:191', Rule::unique('users', 'email')->ignore($adminId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'string', Rule::in(['active', 'suspended', 'pending'])],
            'role' => ['nullable', 'string', Rule::in(['admin', 'dinas-supervisor', 'super-admin'])],
        ];
    }
}
