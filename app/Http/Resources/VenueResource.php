<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'description' => $this->description,
            'media' => $this->media,
            'phone' => $this->phone,
            'website' => $this->website,
            'tournaments_count' => $this->whenCounted('tournaments'),
            'tournaments' => VenueTournamentResource::collection($this->whenLoaded('tournaments')),
            'ongoing_tournaments_count' => $this->whenCounted('ongoingTournaments'),
            'ongoing_tournaments' => VenueTournamentResource::collection($this->whenLoaded('ongoingTournaments')),
            'category_id' => $this->category_id,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'managers' => $this->when(
                $request->user()?->hasPermissionTo('venue.managers.view'),
                fn () => UserResource::collection($this->whenLoaded('managers'))
            ),
            'resources' => ResourceResource::collection($this->whenLoaded('resources')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'opening_hours' => OpeningHoursResource::collection($this->whenLoaded('openingHours')),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_virtual' => $this->is_virtual,
            'external_booking_url' => $this->when(
                $request->user()?->hasPermissionTo('venue.update'),
                $this->external_booking_url
            ),
            'external_booking_clicks_count' => $this->when(
                $request->user()?->hasPermissionTo('venue.update'),
                fn () => $this->analytics()->where('action', 'venue.click_external_booking')->count()
            ),
            'visits_count' => $this->when(
                $request->user()?->hasPermissionTo('venue.update'),
                fn () => $this->analytics()->where('action', 'venue_visit')->count()
            ),
        ];
    }
}
