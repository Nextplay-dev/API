<?php

namespace App\Actions\Booking;

use App\DTOs\StoreBookingScoreDTO;
use App\Events\NotificationDeleted;
use App\Models\Booking;
use App\Models\Notification;
use App\Notifications\BookingScoredNotification;
use Illuminate\Support\Facades\Auth;

class StoreBookingScoreAction
{
    public function handle(int $bookingId, StoreBookingScoreDTO $dto): void
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['guests'])
            ->findOrFail($bookingId);

        $booking->scores()->delete();

        foreach ($dto->scores as $item) {
            $booking->scores()->create([
                'user_id' => $item['user_id'] ?? null,
                'booking_guest_id' => $item['booking_guest_id'] ?? null,
                'score' => $item['score'],
            ]);
        }

        $booking->update(['status' => 'ended']);

        if ($booking->scoring_notification_id) {
            NotificationDeleted::dispatch($booking->user_id, $booking->scoring_notification_id);
            Notification::where('id', $booking->scoring_notification_id)->delete();
            $booking->update(['scoring_notification_id' => null]);
        }

        $booking->load(['resource.venue', 'activity', 'guests.user', 'user', 'scores']);

        $recipients = collect([$booking->user])
            ->concat($booking->guests->whereNotNull('user_id')->pluck('user'))
            ->filter()
            ->unique('id');

        foreach ($recipients as $recipient) {
            $recipient->notify(new BookingScoredNotification($booking));
        }
    }
}
