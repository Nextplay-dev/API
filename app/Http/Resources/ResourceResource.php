<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'venue_id' => $this->venue_id,
            'name' => $this->name,
            'type' => $this->type,
            'capacity' => $this->capacity,
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'venue' => VenueResource::make($this->whenLoaded('venue')),
        ];
    }
}
