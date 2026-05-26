<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingScore extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'booking_guest_id',
        'score',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookingGuest(): BelongsTo
    {
        return $this->belongsTo(BookingGuest::class);
    }
}
