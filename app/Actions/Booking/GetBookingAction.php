<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetBookingAction
{
    public function handle(int $id, ?User $user = null): Booking
    {
        $user = $user ?? Auth::user();

        return Booking::where('user_id', $user->id)
            ->with(['resource.venue', 'activity', 'guests.user', 'user', 'scores'])
            ->findOrFail($id);
    }
}
