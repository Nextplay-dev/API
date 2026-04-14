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
            "tournaments" => TournamentResource::collection($this->whenLoaded("tournaments")),
            "category_id" => $this->category_id,
            "category" => CategoryResource::make($this->whenLoaded("category")),
            "managers" => $this->when(
                $request->user()?->hasPermissionTo('venue.managers.view'),
                fn () => UserResource::collection($this->whenLoaded("managers"))
            ),
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
        ];
    }
}
