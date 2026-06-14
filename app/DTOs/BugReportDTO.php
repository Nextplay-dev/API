<?php

namespace App\DTOs;

use App\Http\Requests\StoreBugReportRequest;

readonly class BugReportDTO
{
    public function __construct(
        public int $userId,
        public string $message,
    ) {}

    public static function fromRequest(StoreBugReportRequest $request): self
    {
        return new self(
            userId: $request->user()->id,
            message: $request->validated('message'),
        );
    }
}
