<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = auth()->user();

        $rules = [
            'name' => ['sometimes', 'string', 'max:255'],
            'bio' => ['sometimes', 'nullable', 'string'],
            'picture_profile_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'enable_core_notification' => ['sometimes', 'boolean'],
            'enable_commercial_notification' => ['sometimes', 'boolean'],
        ];

        if ($user && $user->social_provider === null) {
            $rules['current_password'] = ['required_with:password', 'current_password'];
            $rules['password'] = ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['prohibited'];
            $rules['current_password'] = ['prohibited'];
        }

        return $rules;
    }
}
