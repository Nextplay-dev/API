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

class InvitationDeclinedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected string $guestName
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
        return ExpoMessage::create('Invitation refusée')
            ->body($this->guestName.' a refusé votre invitation pour la partie de '.$this->booking->activity->name.'.')
            ->badge(1)
            ->priority('high')
            ->playSound();
    }

    public function toArray(object $notifiable): array
    {
        return [
            'guestName' => $this->guestName,
            'booking' => BookingResource::make($this->booking->load(['resource.venue', 'activity', 'guests', 'user']))->resolve(),
        ];
    }
}
