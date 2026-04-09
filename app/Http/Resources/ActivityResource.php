<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            "category_id" => $this->activity_category_id,
            "category" => ActivityCategoryResource::make($this->whenLoaded("category")),
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
        ];
    }
}
