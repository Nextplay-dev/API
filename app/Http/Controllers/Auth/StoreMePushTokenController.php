<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\StorePushTokenAction;
use App\DTOs\StorePushTokenDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePushTokenRequest;
use Illuminate\Http\Response;

class StoreMePushTokenController extends Controller
{
    public function __invoke(StorePushTokenRequest $request, StorePushTokenAction $action): Response
    {
        $dto = StorePushTokenDTO::fromRequest($request);
        $action->handle($request->user(), $dto);

        return response()->noContent();
    }
}
