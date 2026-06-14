<?php

namespace App\DTOs;

use App\Http\Requests\Auth\UploadPictureRequest;
use Illuminate\Http\UploadedFile;

readonly class UploadPictureDTO
{
    public function __construct(
        public UploadedFile $picture,
    ) {}

    public static function fromRequest(UploadPictureRequest $request): self
    {
        return new self(
            picture: $request->file('picture')
        );
    }
}
