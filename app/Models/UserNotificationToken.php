<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationToken extends Model
{
    protected $fillable = [
        'user_id',
        'push_token',
        'device_token',
        'device_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
