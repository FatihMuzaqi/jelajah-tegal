<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('members.manage') === true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:191'], 'email' => ['required', 'email:rfc', 'max:191'], 'role' => ['required', Rule::in(['mitra-staff', 'gatekeeper'])]];
    }
}
