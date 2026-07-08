<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ListActivityAvailableSlotRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after:from'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'from' => Carbon::parse($this->input('from'))->utc(),
            'to' => Carbon::parse($this->input('to'))->utc(),
        ]);
    }
}
