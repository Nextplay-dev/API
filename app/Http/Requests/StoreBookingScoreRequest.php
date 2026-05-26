<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scores' => ['required', 'array'],
            'scores.*.user_id' => ['nullable', 'exists:users,id'],
            'scores.*.booking_guest_id' => ['nullable', 'exists:booking_guests,id'],
            'scores.*.score' => ['required', 'integer', 'min:0'],
        ];
    }
}
