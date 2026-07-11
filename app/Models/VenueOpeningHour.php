<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueOpeningHour extends Model
{
    protected $fillable = [
        'venue_id',
        'day_of_week',
        'exceptional_date',
        'opens_at',
        'closes_at',
        'is_closed',
        'label',
    ];

    protected $casts = [
        'exceptional_date' => 'date:Y-m-d',
        'opens_at' => 'string',
        'closes_at' => 'string',
        'is_closed' => 'boolean',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
