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
        return $this->slotService
            ->computeAvailableSlots($dto->resource, $dto->activity, $dto->from, $dto->to);
    }
}
