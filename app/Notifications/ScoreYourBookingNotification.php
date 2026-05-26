<?php

namespace App\Notifications;

use App\Http\Resources\BookingResource;
use App\Http\Resources\NotificationResource;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ScoreYourBookingNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking
    ) {
        $this->id = (string) Str::uuid();
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $notification = $notifiable->notifications()->where('id', $this->id)->first();

        return new BroadcastMessage(NotificationResource::make($notification)->resolve());
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking' => BookingResource::make($this->booking->load(['resource.venue', 'activity', 'guests', 'user']))->resolve(),
        ];
    }
}
