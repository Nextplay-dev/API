<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($this->route('category'))],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ];
    }
}
