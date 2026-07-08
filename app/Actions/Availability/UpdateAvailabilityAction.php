<?php

namespace App\Actions\Availability;

use App\Models\Availability;

class UpdateAvailabilityAction
{
    public function handle(Availability $availability, array $data): Availability
    {
        $availability->update($data);

        return $availability->refresh();
    }
}
