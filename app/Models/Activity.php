<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class)
            ->withTimestamps();
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
