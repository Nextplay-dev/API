<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\TryEmailAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TryEmailRequest;

class TryEmailController extends Controller
{
    public function __invoke(TryEmailRequest $request, TryEmailAction $action)
    {
        $result = $action->handle($request->validated('email'));

        return response()->json($result);
    }
}
