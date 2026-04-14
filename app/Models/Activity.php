<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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

    public function getRule($key, $default = null)
    {
        return $this->rules_json[$key] ?? $default;
    }

    public function canUseSlot(Resource $resource, Carbon $start, Carbon $end): bool
    {
        $maxUnits = $resource->capacity;

        $bookings = $resource->bookings()
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->get();

        $used = $bookings->sum('units');

        return ($used + 1) <= $maxUnits;
    }
}
