<?php

namespace App\Http\Controllers\Booking;

use App\Actions\Booking\GetMyBookingsAction;
use App\Actions\Booking\GetBookingAction;
use App\Actions\Booking\InviteGuestAction;
use App\DTOs\InviteGuestDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInviteGuestRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingGuestResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MeBookingController extends Controller
{
    public function index(GetMyBookingsAction $getMyBookingsAction): JsonResponse
    {
        $bookings = $getMyBookingsAction->handle(Auth::user());

        return BookingResource::collection($bookings)->response();
    }

    public function show(int $id, GetBookingAction $getBookingAction): JsonResponse
    {
        $booking = $getBookingAction->handle($id);

        return (new BookingResource($booking))->response();
    }

    public function invite(int $id, StoreInviteGuestRequest $request, InviteGuestAction $inviteGuestAction): JsonResponse
    {
        $dto = InviteGuestDTO::fromRequest($request);
        $guest = $inviteGuestAction->handle($id, $dto);

        return (new BookingGuestResource($guest))->response();
    }
}