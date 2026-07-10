<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'media' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['exists:users,id'],
            'is_virtual' => ['sometimes', 'boolean'],
            'external_booking_url' => ['nullable', 'string', 'url', 'max:255'],
        ];
    }
}
