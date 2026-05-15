<?php

namespace App\Actions\Activity;

use App\DTOs\ActivityDTO;
use App\Models\Activity;
use App\Models\Venue;

class CreateActivityAction
{
    public function handle(Venue $venue, ActivityDTO $dto): Activity
    {
        return $venue->activities()->create($dto->toArray())->load('resources');
    }
}
