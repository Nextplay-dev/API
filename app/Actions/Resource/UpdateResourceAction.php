<?php

namespace App\Actions\Resource;

use App\Models\Resource;

class UpdateResourceAction
{
    public function handle(Resource $resource, array $data): Resource
    {
        $hasActivityIds = array_key_exists('activity_ids', $data);
        $activityIds = $data['activity_ids'] ?? null;

        unset($data['activity_ids']);

        $resource->update($data);

        if ($hasActivityIds) {
            $resource->activities()->sync($activityIds ?? []);
        }

        return $resource->refresh()->load('activities');
    }
}
