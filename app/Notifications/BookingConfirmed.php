<?php

namespace App\Notifications;

use App\Http\Resources\NotificationResource;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $venueName = $this->booking->resource->venue->name;
        $activityName = $this->booking->activity->name;

        return (new MailMessage)
            ->subject('Booking Confirmed - ' . $venueName)
            ->line('Your booking for ' . $activityName . ' at ' . $venueName . ' has been confirmed.')
            ->line('Start: ' . $this->booking->start_at->format('M j, Y H:i'))
            ->line('End: ' . $this->booking->end_at->format('M j, Y H:i'))
            ->action('View Booking', url('/bookings/' . $this->booking->id))
            ->line('Thank you for using our application!');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $notification = $notifiable->notifications()->where('id', $this->id)->first();

        return new BroadcastMessage(NotificationResource::make($notification)->resolve());
    }

    public function toArray(object $notifiable): array
    {
        return [
            'venueName' => $this->booking->resource->venue->name,
            'activityName' => $this->booking->activity->name,
        ];
    }
}
