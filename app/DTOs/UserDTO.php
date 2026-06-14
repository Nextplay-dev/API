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
        public ?string $bio = null,
        public ?string $pictureProfileUrl = null,
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
                ?? $request->header('referer'),
            bio: $request->validated('bio'),
            pictureProfileUrl: $request->validated('picture_profile_url')
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

        if ($this->bio !== null) {
            $data['bio'] = $this->bio;
        }

        if ($this->pictureProfileUrl !== null) {
            $data['picture_profile_url'] = $this->pictureProfileUrl;
        }

        return $data;
    }
}
