<?php
 
namespace App\DTOs;
 
use Illuminate\Foundation\Http\FormRequest;
 
readonly class RoleDTO
{
    public function __construct(
        public string $name,
        public ?array $permissions = null,
        public int $weight = 0
    ) {}
 
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            permissions: $request->has('permissions') ? $request->validated('permissions', []) : null,
            weight: $request->validated('weight', 0)
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
