<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadKycRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('kyc.submit') === true;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', Rule::in(['business_license', 'tax_document', 'owner_identity', 'bank_proof'])],
            'document' => ['required', 'file', 'max:5120', 'mimetypes:application/pdf,image/jpeg,image/png'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'expires_on' => ['nullable', 'date', 'after:today'],
        ];
    }
}
