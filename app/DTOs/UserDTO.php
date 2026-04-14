<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password = null,
        public ?array $roles = null,
        public ?string $originReferrer = null,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            roles: $request->has('roles') ? $request->validated('roles', []) : null,
            originReferrer: $request->header('X-Origin-Referrer') 
                ?? $request->validated('origin_referrer') 
                ?? $request->header('referer')
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
