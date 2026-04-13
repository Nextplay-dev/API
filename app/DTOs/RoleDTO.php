<?php
 
namespace App\DTOs;
 
use Illuminate\Http\Request;
 
readonly class RoleDTO
{
    public function __construct(
        public string $name,
        public ?array $permissions = null,
        public int $weight = 0
    ) {}
 
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->string('name'),
            permissions: $request->has('permissions') ? $request->input('permissions', []) : null,
            weight: $request->integer('weight', 0)
        );
    }
 
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'weight' => $this->weight,
        ];
    }
}
