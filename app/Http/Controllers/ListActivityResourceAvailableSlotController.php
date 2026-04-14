<?php

namespace App\Http\Controllers;

use App\Actions\Slot\ListAvailableSlotsAction;
use App\DTOs\ListAvailableSlotDTO;
use App\Models\Activity;
use App\Models\Resource;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListActivityResourceAvailableSlotController {
    public function __invoke(
        Venue $venue,
        Request $request,
        Activity $activity,
        Resource $resource,
        ListAvailableSlotsAction $action,
    ): JsonResponse {
        $from = Carbon::parse($request->input('from'))->utc();
        $to = Carbon::parse($request->input('to'))->utc();

        $dto = new ListAvailableSlotDTO(
            activity: $activity,
            resource: $resource,
            from: $from,
            to: $to,
        );
        return response()->json($action->handle($dto));
    }
}