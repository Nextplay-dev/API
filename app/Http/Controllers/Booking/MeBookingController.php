<?php

namespace App\Http\Controllers\Booking;

use App\Actions\Booking\GetBookingAction;
use App\Actions\Booking\GetJoinedBookingAction;
use App\Actions\Booking\GetJoinedBookingsAction;
use App\Actions\Booking\GetMyBookingsAction;
use App\Actions\Booking\InviteGuestAction;
use App\Actions\Booking\StoreBookingScoreAction;
use App\DTOs\InviteGuestDTO;
use App\DTOs\StoreBookingScoreDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingScoreRequest;
use App\Http\Requests\StoreInviteGuestRequest;
use App\Http\Resources\BookingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MeBookingController extends Controller
{
    public function index(GetMyBookingsAction $getMyBookingsAction): JsonResponse
    {
        $bookings = $getMyBookingsAction->handle(Auth::user());

        return BookingResource::collection($bookings)->response();
    }

    public function joined(GetJoinedBookingsAction $getJoinedBookingsAction): JsonResponse
    {
        $bookings = $getJoinedBookingsAction->handle(Auth::user());

        return BookingResource::collection($bookings)->response();
    }

    public function showJoined(int $id, GetJoinedBookingAction $getJoinedBookingAction): JsonResponse
    {
        $booking = $getJoinedBookingAction->handle($id);

        return (new BookingResource($booking))->response();
    }

    public function show(int $id, GetBookingAction $getBookingAction): JsonResponse
    {
        $booking = $getBookingAction->handle($id);

        return (new BookingResource($booking))->response();
    }

    public function invite(int $id, StoreInviteGuestRequest $request, InviteGuestAction $inviteGuestAction): JsonResponse
    {
        $dto = InviteGuestDTO::fromRequest($request);
        $inviteGuestAction->handle($id, $dto);

        return response()->json([], 201);
    }

    public function score(int $id, StoreBookingScoreRequest $request, StoreBookingScoreAction $storeBookingScoreAction): JsonResponse
    {
        $dto = StoreBookingScoreDTO::fromRequest($request);
        $storeBookingScoreAction->handle($id, $dto);

        return response()->json([], 201);
    }
}
