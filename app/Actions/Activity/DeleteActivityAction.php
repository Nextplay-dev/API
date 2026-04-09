<?php

namespace App\Actions\Activity;

use App\Models\Activity;

class DeleteActivityAction
{
    public function handle(Activity $activity): void
    {
        $activity->delete();
    }
}
