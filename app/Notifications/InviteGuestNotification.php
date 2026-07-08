<?php

namespace App\Notifications;

use App\Http\Resources\BookingResource;
use App\Http\Resources\NotificationResource;
use App\Models\Booking;
use App\Models\BookingGuest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use NotificationChannels\Expo\ExpoMessage;

class InviteGuestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected BookingGuest $guest,
    ) {
        $this->id = (string) Str::uuid();
    }

    public function via(object $notifiable): array
    {
        if ($notifiable instanceof User) {
            return ['mail', 'database', 'broadcast', 'expo'];
        }

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $venueName = $this->booking->resource->venue->name;
        $activityName = $this->booking->activity->name;
        $inviterName = Auth::user() ? Auth::user()->name : 'un ami';

        $dateStr = $this->booking->start_at->translatedFormat('l d F Y');
        $timeStr = $this->booking->start_at->format('H:i').' – '.$this->booking->end_at->format('H:i');

        $currentPlayers = 1 + $this->booking->guests()->where('status', '!=', 'rejected')->count();
        $capacity = $this->booking->resource ? $this->booking->resource->capacity : 6;

        $acceptUrl = url('/bookings/join?token='.$this->guest->token);
        $declineUrl = url('/bookings/decline?token='.$this->guest->token);

        $categoryIcon = $this->booking->resource?->venue?->category?->icon;
        $categoryColor = $this->booking->resource?->venue?->category?->color ?? '#6564DB';
        $categoryIconUrl = null;

        if ($categoryIcon && str_contains($categoryIcon, '/')) {
            [$library, $name] = explode('/', $categoryIcon, 2);
            $prefix = $library === 'MaterialCommunityIcons' ? 'mdi' : 'ion';
            $cleanColor = ltrim($categoryColor, '#');
            $categoryIconUrl = "https://api.iconify.design/{$prefix}:{$name}.svg?color={$cleanColor}";
        }

        return (new MailMessage)
            ->subject('Invitation à rejoindre une partie à '.$venueName)
            ->view('emails.invite', [
                'activityName' => $activityName,
                'venueName' => $venueName,
                'venueAddress' => $this->booking->resource?->venue?->address,
                'venueImageUrl' => $this->booking->resource?->venue?->media,
                'inviterName' => $inviterName,
                'dateStr' => $dateStr,
                'timeStr' => $timeStr,
                'currentPlayers' => $currentPlayers,
                'capacity' => $capacity,
                'acceptUrl' => $acceptUrl,
                'declineUrl' => $declineUrl,
                'categoryIconUrl' => $categoryIconUrl,
                'categoryColor' => $categoryColor,
            ]);
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $notification = $notifiable->notifications()->where('id', $this->id)->first();

        return new BroadcastMessage(NotificationResource::make($notification)->resolve());
    }

    public function toExpo($notifiable): ExpoMessage
    {
        $inviterName = Auth::user() ? Auth::user()->name : 'un ami';

        return ExpoMessage::create('Invitation à une partie')
            ->body($inviterName.' vous invite à rejoindre une partie de '.$this->booking->activity->name.' à '.$this->booking->resource->venue->name.'.')
            ->badge(1)
            ->priority('high')
            ->playSound();
    }

    public function toArray(object $notifiable): array
    {
        return [
            'guestId' => $this->guest->id,
            'booking' => BookingResource::make($this->booking->load(['resource.venue', 'activity', 'guests', 'user']))->resolve(),
        ];
    }
}
