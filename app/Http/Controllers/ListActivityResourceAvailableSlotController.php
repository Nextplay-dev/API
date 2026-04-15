<?php

namespace App\Http\Controllers;

use App\Actions\Slot\ListAvailableSlotsAction;
use App\DTOs\ListAvailableSlotDTO;
use App\Http\Requests\ListActivityResourceAvailableSlotRequest;
use App\Models\Activity;
use App\Models\Resource;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;

class ListActivityResourceAvailableSlotController {
    public function __invoke(
        Venue $venue,
        Activity $activity,
        Resource $resource,
        ListActivityResourceAvailableSlotRequest $request,
        ListAvailableSlotsAction $action,
    ): JsonResponse {

        $dto = new ListAvailableSlotDTO(
            activity: $activity,
            resource: $resource,
            from: $request->from,
            to: $request->to,
        );
        return response()->json($action->handle($dto));
    }
}