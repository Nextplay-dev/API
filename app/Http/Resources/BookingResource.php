<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'resource' => new ResourceResource($this->whenLoaded('resource')),
            'activity' => ActivityResource::make($this->whenLoaded('activity')),
            'slot' => SlotResource::make($this->whenLoaded('slot')),
            'start_at' => $this->start_at->toIso8601String(),
            'end_at' => $this->end_at->toIso8601String(),
            'units' => $this->units,
            'status' => $this->status,
            'payment' => $this->payment,
            'created_at' => $this->created_at,
        ];
    }
}
