<?php

namespace App\Actions\Slot;

use App\DTOs\ListAvailableSlotDTO;
use App\Services\SlotService;

class ListAvailableSlotsAction
{
    public function __construct(
        private SlotService $slotService
    ) {}
    public function handle(ListAvailableSlotDTO $dto) {
        if ($dto->resource) {
            abort_unless(
                $dto->resource->activities()->whereKey($dto->activity->id)->exists(),
                404
            );
            return $this->slotService
                ->computeAvailableSlotsForResource($dto->resource, $dto->activity, $dto->from, $dto->to);
        }

        return $this->slotService
            ->computeAvailableSlots($dto->activity, $dto->from, $dto->to);
    }
}
