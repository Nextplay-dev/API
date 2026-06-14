<?php

namespace App\DTOs;

use App\Http\Requests\Auth\UpdateMeRequest;

readonly class UpdateMeDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $password = null,
        public ?string $bio = null,
        public ?string $pictureProfileUrl = null,
    ) {}

    public static function fromRequest(UpdateMeRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            password: $request->validated('password'),
            bio: $request->validated('bio'),
            pictureProfileUrl: $request->validated('picture_profile_url'),
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->password !== null) {
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
