<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $venueId = $this->route('venue')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'activity_ids' => ['nullable', 'array'],
            'activity_ids.*' => ['integer', Rule::exists('activities', 'id')->where('venue_id', $venueId)],
        ];
    }
}
