<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteGuestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $venueName = $this->booking->resource->venue->name;
        $activityName = $this->booking->activity->name;

        return (new MailMessage)
            ->subject('Invitation to join a booking at ' . $venueName)
            ->line('You have been invited to join a booking for ' . $activityName . ' at ' . $venueName . '.')
            ->line('Start: ' . $this->booking->start_at->format('M j, Y H:i'))
            ->line('End: ' . $this->booking->end_at->format('M j, Y H:i'))
            ->action('Accept Invitation', url('/bookings/join?token=' . $this->token))
            ->line('Thank you!');
    }
}
