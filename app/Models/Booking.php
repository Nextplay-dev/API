<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{

    protected $fillable = [
        'payment',
        'user_id',
        'resource_id',
        'activity_id',
        'start_at',
        'end_at',
        'units',
        'status',
        'scoring_notification_id',
    ];

    protected $casts = [
        'payment' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeOverlapping($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->where('start_at', '<', $end)
              ->where('end_at', '>', $start);
        });
    }
    public function scopeUpcoming($query)
    {
        return $query->where('end_at', '>=', Carbon::now('UTC'));
    }

    public function guests(): HasMany
    {
        return $this->hasMany(BookingGuest::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(BookingScore::class);
    }
}
