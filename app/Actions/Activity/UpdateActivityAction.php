<?php

namespace App\Actions\Activity;

use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;

class UpdateActivityAction
{
    public function handle(UpdateActivityRequest $request, Activity $activity): Activity
    {
        $activity->update($request->validated());

        if ($request->has('manager_ids') && auth()->user()?->hasPermissionTo('activity.managers.update')) {
            $activity->managers()->sync($request->manager_ids);
        }

        return $activity->refresh();
    }
}
