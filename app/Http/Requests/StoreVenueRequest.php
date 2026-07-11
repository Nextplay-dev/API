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
            'phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'string', 'url', 'max:255'],
            'opening_hours' => ['nullable', 'array'],
            'opening_hours.*.day_of_week' => ['required_with:opening_hours', 'integer', 'between:0,6'],
            'opening_hours.*.opens_at' => ['required_with:opening_hours', 'string', 'date_format:H:i:s'],
            'opening_hours.*.closes_at' => ['required_with:opening_hours', 'string', 'date_format:H:i:s'],
        ];
    }
}
