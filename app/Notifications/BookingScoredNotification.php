<?php

namespace App\Notifications;

use App\Http\Resources\BookingResource;
use App\Http\Resources\NotificationResource;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Expo\ExpoMessage;

class BookingScoredNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking
    ) {
        $this->id = (string) Str::uuid();
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'expo'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $notification = $notifiable->notifications()->where('id', $this->id)->first();

        return new BroadcastMessage(NotificationResource::make($notification)->resolve());
    }

    public function toExpo($notifiable): ExpoMessage
    {
        return ExpoMessage::create('Partie scorée !')
            ->body('Les scores ont été saisis pour votre partie de '.$this->booking->activity->name.' à '.$this->booking->resource->venue->name.'.')
            ->badge(1)
            ->priority('high')
            ->playSound();
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking' => BookingResource::make($this->booking->load(['resource.venue', 'activity', 'guests', 'user', 'scores']))->resolve(),
        ];
    }
}
