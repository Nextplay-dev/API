<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password = null,
        public array $roles = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->string('name'),
            email: $request->string('email'),
            password: $request->string('password'),
            roles: $request->input('roles', [])
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
