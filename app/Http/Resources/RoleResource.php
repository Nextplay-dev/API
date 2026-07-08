<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_locked' => $this->is_locked,
            'weight' => $this->weight,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
