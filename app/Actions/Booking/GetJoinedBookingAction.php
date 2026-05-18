<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetJoinedBookingAction
{
    public function handle(int $id, ?User $user = null): Booking
    {
        $user = $user ?? Auth::user();

        return Booking::whereHas('guests', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 'accepted');
        })
        ->with(['resource.venue', 'activity', 'guests.user', 'user'])
        ->findOrFail($id);
    }
}
