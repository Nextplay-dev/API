<?php

namespace App\Http\Controllers;

use App\Actions\Booking\CreateBookingAction;
use App\DTOs\CreateBookingDTO;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function indexByResource(Resource $resource): JsonResponse
    {
        Gate::authorize('viewBookings', $resource->venue);

        $bookings = $resource->bookings()
            ->with(['activity', 'user'])
            ->orderBy('start_at')
            ->get();

        return BookingResource::collection($bookings)->response();
    }

    public function store(StoreBookingRequest $request, CreateBookingAction $action): JsonResponse
    {
        $dto = CreateBookingDTO::fromRequest($request);
        $booking = $action->handle($dto);

        return BookingResource::make($booking->load(['resource', 'activity']))
            ->response()
            ->setStatusCode(201);
    }
}