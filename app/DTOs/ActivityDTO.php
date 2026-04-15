<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class ActivityDTO
{
    public function __construct(
        public string $name,
        public int $durationMinutes,
        public int $slotIntervalMinutes,
        public array $rulesJson = [],
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            durationMinutes: (int) $request->validated('duration_minutes'),
            slotIntervalMinutes: (int) $request->validated('slot_interval_minutes'),
            rulesJson: $request->validated('rules_json', []),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'duration_minutes' => $this->durationMinutes,
            'slot_interval_minutes' => $this->slotIntervalMinutes,
            'rules_json' => $this->rulesJson,
        ];
    }
}
