<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $venueId = $this->route('resource')?->venue_id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'activity_ids' => ['nullable', 'array'],
            'activity_ids.*' => ['integer', Rule::exists('activities', 'id')->where('venue_id', $venueId)],
        ];
    }
}
