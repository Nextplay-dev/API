<?php

namespace App\Http\Controllers\Booking;

use App\Actions\Booking\GetMyBookingsAction;
use App\Http\Controllers\Controller;
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
}