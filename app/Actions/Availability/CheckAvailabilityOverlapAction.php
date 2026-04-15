<?php

namespace App\Actions\Availability;

use App\Models\Availability;
use Illuminate\Validation\ValidationException;

class CheckAvailabilityOverlapAction
{
    public function handle(int $resourceId, array $data, ?int $ignoreId = null): void
    {
        $dayOfWeek = $data['day_of_week'];
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];

        $overlap = Availability::where('resource_id', $resourceId)
            ->where('day_of_week', $dayOfWeek)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'availability' => ['This availability window overlaps with an existing one.'],
            ]);
        }
    }
}
