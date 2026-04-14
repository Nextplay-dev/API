<?php

namespace App\Http\Controllers;

use App\Http\Resources\SlotResource;
use App\Models\Slot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'resource_id' => ['required', 'exists:resources,id'],
            'date' => ['required', 'date'],
        ]);

        $slots = Slot::where('resource_id', $request->resource_id)
            ->whereDate('start_at', $request->date)
            ->with('bookings')
            ->get();

        return SlotResource::collection($slots)->response();
    }
}
