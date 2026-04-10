<?php
 
namespace App\DTOs;
 
use Illuminate\Http\Request;
 
readonly class RoleDTO
{
    public function __construct(
        public string $name,
        public array $permissions = []
    ) {}
 
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->string('name'),
            permissions: $request->input('permissions', [])
        );
    }
 
    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
