<?php

namespace App\Actions\Venue;

use App\Http\Requests\UpdateVenueRequest;
use App\Models\Venue;

class UpdateVenueAction
{
    public function handle(UpdateVenueRequest $request, Venue $venue): Venue
    {
        $data = $request->validated();
        $openingHours = $data['opening_hours'] ?? null;
        unset($data['opening_hours']);

        $venue->update($data);

        if ($openingHours !== null) {
            $venue->openingHours()->delete();
            foreach ($openingHours as $slot) {
                $venue->openingHours()->create([
                    'day_of_week' => $slot['day_of_week'],
                    'opens_at' => $slot['opens_at'],
                    'closes_at' => $slot['closes_at'],
                ]);
            }
        }

        if ($request->has('manager_ids') && auth()->user()?->hasPermissionTo('venue.managers.update')) {
            $venue->managers()->sync($request->manager_ids);
        }

        return $venue->refresh();
    }
}
