<?php

namespace App\Actions\Activity;

use App\DTOs\ActivityDTO;
use App\Models\Activity;

class UpdateActivityAction
{
    public function handle(Activity $activity, ActivityDTO $dto): Activity
    {
        $activity->update($dto->toArray());

        return $activity->refresh()->load('resources');
    }
}
