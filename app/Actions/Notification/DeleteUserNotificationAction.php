<?php

namespace App\Actions\Notification;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class DeleteUserNotificationAction
{
    public function handle(User $user, Notification $notification): void
    {
        if ($notification->notifiable_type !== User::class || $notification->notifiable_id !== $user->id) {
            throw new AuthorizationException('Not authorized to delete this notification');
        }

        $notification->delete();
    }
}
