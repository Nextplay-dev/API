<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingScoreDTO
{
    public function __construct(
        public array $scores,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            scores: $request->validated('scores'),
        );
    }
}
