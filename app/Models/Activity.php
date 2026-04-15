<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'duration_minutes',
        'slot_interval_minutes',
        'rules_json',
        'venue_id',
    ];

    protected $casts = [
        'rules_json' => 'array',
    ];

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'venue_id', 'venue_id');
    }

    public function getRule($key, $default = null)
    {
        return $this->rules_json[$key] ?? $default;
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
