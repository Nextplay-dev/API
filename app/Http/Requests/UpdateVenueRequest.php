<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVenueRequest extends FormRequest
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
            'description' => ['nullable', 'string'],
            'media' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['exists:users,id'],
            'is_virtual' => ['sometimes', 'boolean'],
            'external_booking_url' => ['nullable', 'string', 'url', 'max:255'],
        ];
    }
}
