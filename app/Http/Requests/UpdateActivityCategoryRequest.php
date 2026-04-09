<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivityCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('activity_categories', 'name')->ignore($this->route('activityCategory'))],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ];
    }
}
