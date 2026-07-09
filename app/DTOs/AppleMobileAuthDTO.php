<?php

namespace App\DTOs;

readonly class AppleMobileAuthDTO
{
    public function __construct(
        public string $identityToken,
        public ?string $authorizationCode = null,
        public ?string $name = null,
    ) {}
}
