<?php

namespace App\Actions\Activity;

use App\Http\Requests\StoreActivityRequest;
use App\Models\Activity;

class StoreActivityAction
{
    public function handle(StoreActivityRequest $request): Activity
    {
        return Activity::create($request->validated());
    }
}
