<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{

    protected $fillable = [
        'payment',
        'user_id',
        'resource_id',
        'activity_id',
        'slot_id',
        'start_at',
        'end_at',
        'units',
        'status',
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

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }
}
