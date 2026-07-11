<?php

namespace App\Actions\Venue;

use App\Http\Requests\StoreVenueRequest;
use App\Models\Venue;

class StoreVenueAction
{
    public function handle(StoreVenueRequest $request): Venue
    {
        $data = $request->validated();
        $openingHours = $data['opening_hours'] ?? null;
        unset($data['opening_hours']);

        $venue = Venue::create($data);

        if ($openingHours) {
            $this->saveOpeningHours($venue, $openingHours);
        }

        if ($request->has('manager_ids') && auth()->user()?->hasPermissionTo('venue.managers.update')) {
            $venue->managers()->sync($request->manager_ids);
        }

        return $venue;
    }

    private function saveOpeningHours(Venue $venue, array $weeklyHours): void
    {
        foreach ($weeklyHours as $slot) {
            $venue->openingHours()->create([
                'day_of_week' => $slot['day_of_week'],
                'opens_at' => $slot['opens_at'],
                'closes_at' => $slot['closes_at'],
            ]);
        }
    }
}
