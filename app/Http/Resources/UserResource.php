<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'permissions' => $this->whenLoaded('roles', fn () => $this->getAllPermissions()),
            'created_at' => $this->created_at,
        ];
    }

    private function getAllPermissions(): array
    {
        $directPermissions = $this->relationLoaded('permissions')
            ? $this->permissions->pluck('name')
            : collect();

        $rolePermissions = $this->roles
            ->flatMap(fn ($role) => $role->permissions->pluck('name'));

        return $directPermissions->merge($rolePermissions)->unique()->values()->all();
    }
}
