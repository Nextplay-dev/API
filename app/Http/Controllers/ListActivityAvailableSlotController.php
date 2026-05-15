<?php

namespace App\Http\Controllers;

use App\Actions\Slot\ListAvailableSlotsAction;
use App\DTOs\ListAvailableSlotDTO;
use App\Http\Requests\ListActivityAvailableSlotRequest;
use App\Models\Activity;
use App\Models\Resource;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;

class ListActivityAvailableSlotController {
    public function __invoke(
        Venue $venue,
        Activity $activity,
        Resource $resource,
        ListActivityAvailableSlotRequest $request,
        ListAvailableSlotsAction $action,
    ): JsonResponse {

        $dto = new ListAvailableSlotDTO(
            activity: $activity,
            from: $request->from,
            to: $request->to,
        );
        return response()->json($action->handle($dto));
    }
}