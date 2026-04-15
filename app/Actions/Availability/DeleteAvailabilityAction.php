<?php

namespace App\Actions\Availability;

use App\Models\Availability;

class DeleteAvailabilityAction
{
    public function handle(Availability $availability): void
    {
        $availability->delete();
    }
}
