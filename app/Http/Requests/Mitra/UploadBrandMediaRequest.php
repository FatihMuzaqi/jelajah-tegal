<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;

class UploadBrandMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mitras.update') === true;
    }

    public function rules(): array
    {
        return ['image' => ['required', 'file', 'max:8192', 'mimetypes:image/jpeg,image/png,image/webp']];
    }
}
