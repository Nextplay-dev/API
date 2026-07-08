<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePushTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'push_token' => ['nullable', 'string'],
            'device_token' => ['nullable', 'string'],
            'device_type' => ['nullable', 'string'],
        ];
    }
}
