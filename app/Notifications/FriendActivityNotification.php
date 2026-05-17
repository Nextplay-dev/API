<?php

namespace App\Notifications;

use App\Http\Resources\NotificationResource;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class FriendActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected string $friendName
    ) {}

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
            'friendName' => $this->friendName,
            'activityName' => $this->booking->activity->name,
            'venueName' => $this->booking->resource->venue->name,
        ];
    }
}
