<?php

namespace App\Actions\Venue;

use App\Models\Venue;

class DeleteVenueAction
{
    public function handle(Venue $venue): void
    {
        $venue->delete();
    }
}
