<?php

namespace App\Actions\Availability;

use App\Models\Availability;
use App\Models\Resource;

class StoreAvailabilityAction
{
    public function handle(Resource $resource, array $data): Availability
    {
        return $resource->availabilities()->create($data);
    }
}
