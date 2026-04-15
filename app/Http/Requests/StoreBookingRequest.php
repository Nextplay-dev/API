<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resource_id' => ['required', 'exists:resources,id'],
            'activity_id' => ['required', 'exists:activities,id'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'units' => ['integer', 'min:1'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'start_at' => Carbon::parse($this->input('start_at'))->utc(),
            'end_at'   => Carbon::parse($this->input('end_at'))->utc(),
        ]);
    }
}
