<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NotificationChannels\Expo\ExpoPushToken;

class UserNotificationToken extends Model
{
    protected $fillable = [
        'user_id',
        'push_token',
        'device_token',
        'device_type',
    ];

    protected function casts(): array
    {
        return [
            'push_token' => ExpoPushToken::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
