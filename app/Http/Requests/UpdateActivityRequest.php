<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'media' => ['sometimes', 'string', 'max:255'],
            'activity_category_id' => ['sometimes', 'exists:activity_categories,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['exists:users,id'],
        ];
    }
}
