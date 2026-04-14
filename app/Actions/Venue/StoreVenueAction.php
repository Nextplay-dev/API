<?php

namespace App\Actions\Venue;

use App\Http\Requests\StoreVenueRequest;
use App\Models\Venue;

class StoreVenueAction
{
    public function handle(StoreVenueRequest $request): Venue
    {
        $venue = Venue::create($request->validated());

        if ($request->has('manager_ids') && auth()->user()?->hasPermissionTo('venue.managers.update')) {
            $venue->managers()->sync($request->manager_ids);
        }

        return $venue;
    }
}
