<?php

namespace App\Actions\Activity;

use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;

class UpdateActivityAction
{
    public function handle(UpdateActivityRequest $request, Activity $activity): Activity
    {
        $activity->update($request->validated());

        return $activity->refresh();
    }
}
