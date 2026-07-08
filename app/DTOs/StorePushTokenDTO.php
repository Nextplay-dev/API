<?php

namespace App\DTOs;

use App\Http\Requests\StorePushTokenRequest;

class StorePushTokenDTO
{
    public function __construct(
        public ?string $pushToken,
        public ?string $deviceToken,
        public ?string $deviceType,
    ) {}

    public static function fromRequest(StorePushTokenRequest $request): self
    {
        return new self(
            pushToken: $request->validated('push_token'),
            deviceToken: $request->validated('device_token'),
            deviceType: $request->validated('device_type'),
        );
    }
}
