<?php

namespace App\Actions\Resource;

use App\Models\Resource;
use App\Models\Venue;

class StoreResourceAction
{
    public function handle(Venue $venue, array $data): Resource
    {
        $hasActivityIds = array_key_exists('activity_ids', $data);
        $activityIds = $data['activity_ids'] ?? null;

        unset($data['activity_ids']);

        $resource = $venue->resources()->create($data);

        if ($hasActivityIds) {
            $resource->activities()->sync($activityIds ?? []);
        }

        return $resource->load('activities');
    }
}
