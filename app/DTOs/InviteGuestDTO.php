<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class InviteGuestDTO
{
    public function __construct(
        public string $email,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
        );
    }
}
