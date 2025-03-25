<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'game' => $this->game,
            'media' => $this->media,
            'level' => $this->level,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'bookings_count' => $this->bookings()->count(),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
        ];
    }
}
