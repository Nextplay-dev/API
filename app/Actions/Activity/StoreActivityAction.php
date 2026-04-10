<?php

namespace App\Actions\Activity;

use App\Http\Requests\StoreActivityRequest;
use App\Models\Activity;

class StoreActivityAction
{
    public function handle(StoreActivityRequest $request): Activity
    {
        $activity = Activity::create($request->validated());

        if ($request->has('manager_ids') && auth()->user()?->hasPermissionTo('activity.managers.update')) {
            $activity->managers()->sync($request->manager_ids);
        }

        return $activity;
    }
}
