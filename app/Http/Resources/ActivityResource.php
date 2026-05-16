<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'duration_minutes' => $this->duration_minutes,
            'slot_interval_minutes' => $this->slot_interval_minutes,
            'rules' => $this->rules_json,
            'resources' => ResourceResource::collection($this->whenLoaded('resources')),
        ];
    }
}
