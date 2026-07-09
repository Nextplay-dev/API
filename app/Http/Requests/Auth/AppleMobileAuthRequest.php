<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AppleMobileAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identity_token' => ['required', 'string'],
            'authorization_code' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
        ];
    }
}
