<?php

namespace App\Notifications;

use App\Http\Resources\NotificationResource;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoMessage;

class BookingConfirmed extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast', 'expo'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $venueName = $this->booking->resource->venue->name;
        $activityName = $this->booking->activity->name;

        return (new MailMessage)
            ->subject('Réservation confirmée - '.$venueName)
            ->line('Votre réservation pour '.$activityName.' à '.$venueName.' a été confirmée.')
            ->line('Début : '.$this->booking->start_at->format('d/m/Y H:i'))
            ->line('Fin : '.$this->booking->end_at->format('d/m/Y H:i'))
            ->action('Voir la réservation', url('/bookings/'.$this->booking->id))
            ->line('Merci d\'utiliser notre application !');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $notification = $notifiable->notifications()->where('id', $this->id)->first();

        return new BroadcastMessage(NotificationResource::make($notification)->resolve());
    }

    public function toExpo($notifiable): ExpoMessage
    {
        return ExpoMessage::create('Réservation confirmée')
            ->body('Votre réservation pour '.$this->booking->activity->name.' à '.$this->booking->resource->venue->name.' a été confirmée.')
            ->badge(1)
            ->priority('high')
            ->playSound();
    }

    public function toArray(object $notifiable): array
    {
        return [
            'venueName' => $this->booking->resource->venue->name,
            'activityName' => $this->booking->activity->name,
        ];
    }
}
