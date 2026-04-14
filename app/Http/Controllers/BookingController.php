<?php

namespace App\Http\Controllers;

use App\Actions\Booking\CreateBookingAction;
use App\DTOs\CreateBookingDTO;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request, CreateBookingAction $action): JsonResponse
    {
        $dto = CreateBookingDTO::fromRequest($request);
        $booking = $action->handle($dto);

        return BookingResource::make($booking->load(['resource', 'activity', 'slot']))
            ->response()
            ->setStatusCode(201);
    }
}
