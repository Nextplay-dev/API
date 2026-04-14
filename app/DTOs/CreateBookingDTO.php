<?php

namespace App\DTOs;

class CreateBookingDTO
{
    public function __construct(
        public int $resource_id,
        public int $activity_id,
        public string $start_at,
        public string $end_at,
        public int $units = 1,
        public ?int $slot_id = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            resource_id: $request->integer('resource_id'),
            activity_id: $request->integer('activity_id'),
            start_at: $request->string('start_at'),
            end_at: $request->string('end_at'),
            units: $request->integer('units', 1),
            slot_id: $request->integer('slot_id'),
        );
    }
}
