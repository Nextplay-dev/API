<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    protected $fillable = [
        'venue_id',
        'name',
        'type',
        'capacity',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(Exception::class);
    }

    public function getAvailabilityRanges(Carbon $from, Carbon $to): array
    {
        $ranges = [];

        $current = $from->copy()->startOfDay();

        while ($current <= $to) {

            $availabilities = $this->availabilities
                ->where('day_of_week', $current->dayOfWeek);

            foreach ($availabilities as $availability) {

                $start = $current->copy()->setTimeFromTimeString($availability->start_time);
                $end = $current->copy()->setTimeFromTimeString($availability->end_time);

                $ranges[] = [
                    'start' => $start,
                    'end' => $end,
                ];
            }

            $current->addDay();
        }   

        return $ranges;
    }

    public function availableSlots(Activity $activity, Carbon $from, Carbon $to): array
    {
        $ranges = $this->getAvailabilityRanges($from, $to);

        $slots = [];

        foreach ($ranges as $range) {

            $cursor = $range["start"];

            while ($cursor->copy()->addMinutes($activity->duration_minutes) <= $range["end"]) {

                $end = $cursor->copy()->addMinutes($activity->duration_minutes);

                if ($activity->canUseSlot($this, $cursor, $end)) {
                    $slots[] = [
                        'start_at' => $cursor,
                        'end_at' => $end,
                    ];
                }

                $cursor->addMinutes($activity->slot_interval_minutes);
            }
        }

        return $slots;
    }
}
