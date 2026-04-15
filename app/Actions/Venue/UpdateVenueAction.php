<?php

namespace App\Actions\Venue;

use App\Http\Requests\UpdateVenueRequest;
use App\Models\Venue;

class UpdateVenueAction
{
    public function handle(UpdateVenueRequest $request, Venue $venue): Venue
    {
        $venue->update($request->validated());

        if ($request->has('manager_ids') && auth()->user()?->hasPermissionTo('venue.managers.update')) {
            $venue->managers()->sync($request->manager_ids);
        }

        return $venue->refresh();
    }
}
