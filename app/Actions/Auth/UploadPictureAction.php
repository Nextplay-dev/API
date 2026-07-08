<?php

namespace App\Actions\Auth;

use App\DTOs\UploadPictureDTO;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadPictureAction
{
    public function handle(UploadPictureDTO $dto): string
    {
        $file = $dto->picture;
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('profiles', $filename, 'r2');

        return Storage::disk('r2')->url($path);
    }
}
