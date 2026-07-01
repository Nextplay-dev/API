<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "address" => $this->address,
            "media" => $this->media,
            "tournaments_count" => $this->whenCounted('tournaments'),
            "tournaments" => VenueTournamentResource::collection($this->whenLoaded("tournaments")),
            "ongoing_tournaments_count" => $this->whenCounted('ongoingTournaments'),
            "ongoing_tournaments" => VenueTournamentResource::collection($this->whenLoaded("ongoingTournaments")),
            "category_id" => $this->category_id,
            "category" => CategoryResource::make($this->whenLoaded("category")),
            "managers" => $this->when(
                $request->user()?->hasPermissionTo('venue.managers.view'),
                fn () => UserResource::collection($this->whenLoaded("managers"))
            ),
            "resources" => ResourceResource::collection($this->whenLoaded("resources")),
            "activities" => ActivityResource::collection($this->whenLoaded("activities")),
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
        ];
    }
}
