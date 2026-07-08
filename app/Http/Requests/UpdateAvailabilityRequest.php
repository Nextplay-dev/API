<?php

namespace App\Http\Requests;

use App\Actions\Availability\CheckAvailabilityOverlapAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day_of_week' => ['sometimes', 'integer', 'between:0,6'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $availability = $this->route('availability');

            // Merge with existing data for partial updates
            $data = array_merge([
                'day_of_week' => $availability->day_of_week,
                'start_time' => $availability->start_time,
                'end_time' => $availability->end_time,
            ], $this->validated());

            try {
                app(CheckAvailabilityOverlapAction::class)->handle(
                    $availability->resource_id,
                    $data,
                    $availability->id
                );
            } catch (ValidationException $e) {
                foreach ($e->errors() as $key => $messages) {
                    foreach ($messages as $message) {
                        $validator->errors()->add($key, $message);
                    }
                }
            }
        });
    }
}
