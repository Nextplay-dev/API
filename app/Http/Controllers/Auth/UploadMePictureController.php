<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\UploadPictureAction;
use App\DTOs\UploadPictureDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UploadPictureRequest;
use Illuminate\Http\JsonResponse;

class UploadMePictureController extends Controller
{
    public function __invoke(UploadPictureRequest $request, UploadPictureAction $action): JsonResponse
    {
        $url = $action->handle(UploadPictureDTO::fromRequest($request));

        return response()->json([
            'url' => $url,
        ]);
    }
}
