<?php

namespace App\Actions\User;

use App\Models\Booking;
use App\Models\User;

class GetMeStatsAction
{
    public function handle(User $user): array
    {
        $bookingsCount = $user->bookings()->count();
        $joinedBookingsCount = $user->joinedBookings()->count();
        $totalBookings = $bookingsCount + $joinedBookingsCount;

        $allBookings = Booking::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereHas('guests', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->where('status', 'accepted');
                });
        })
            ->whereHas('scores')
            ->with('scores')
            ->get();

        $wins = 0;
        $totalXp = 0;

        foreach ($allBookings as $booking) {
            $scores = $booking->scores;
            if ($scores->isEmpty()) {
                continue;
            }

            $N = $scores->count();
            $sortedScores = $scores->pluck('score')->sortDesc()->values()->all();
            $myScore = $scores->firstWhere('user_id', $user->id)?->score;

            if ($myScore === null) {
                continue;
            }

            $rank = array_search($myScore, $sortedScores) + 1;

            if ($rank === 1) {
                $wins++;
            }

            if ($N === 1) {
                $xpGained = 1;
            } else {
                if ($rank === 1) {
                    $xpGained = $N;
                } elseif ($rank === $N) {
                    $xpGained = 0;
                } else {
                    $xpGained = $N - $rank;
                }
            }

            $totalXp += $xpGained * 10;
        }

        $winRate = $totalBookings > 0 ? (int) round(($wins / $totalBookings) * 100) : 0;

        $level = (int) floor($totalXp / 100) + 1;
        $currentXp = $totalXp % 100;

        return [
            'bookings' => $bookingsCount,
            'joined_bookings' => $joinedBookingsCount,
            'wins' => $wins,
            'win_rate' => $winRate,
            'level' => $level,
            'current_xp' => $currentXp,
            'total_xp' => $totalXp,
        ];
    }
}
