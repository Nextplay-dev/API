<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CreateBookingDTO
{
    public function __construct(
        public int $resource_id,
        public int $activity_id,
        public Carbon $start_at,
        public Carbon $end_at,
        public int $units = 1,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            resource_id: $request->validated('resource_id'),
            activity_id: $request->validated('activity_id'),
            start_at: $request->start_at,
            end_at: $request->end_at,
            units: $request->validated('units', 1),
        );
    }
}
