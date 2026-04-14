<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Actions\Availability\CheckAvailabilityOverlapAction;

class StoreAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) return;

            $resource = $this->route('resource');
            
            try {
                app(CheckAvailabilityOverlapAction::class)->handle(
                    $resource->id, 
                    $this->validated()
                );
            } catch (\Illuminate\Validation\ValidationException $e) {
                foreach ($e->errors() as $key => $messages) {
                    foreach ($messages as $message) {
                        $validator->errors()->add($key, $message);
                    }
                }
            }
        });
    }
}
