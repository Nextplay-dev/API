<?php

namespace App\Actions\Venue;

use App\Actions\Analytics\LogUserAnalyticAction;
use App\Models\User;
use App\Models\Venue;

class GetVenueExternalBookingAction
{
    public function __construct(
        protected LogUserAnalyticAction $logAnalytic
    ) {}

    public function handle(User $user, Venue $venue): string
    {
        abort_unless($venue->is_virtual && $venue->external_booking_url, 404, 'External booking not available for this venue.');

        $this->logAnalytic->handle(
            $user,
            'venue.click_external_booking',
            [],
            [$venue]
        );

        $url = $venue->external_booking_url;
        $separator = parse_url($url, PHP_URL_QUERY) ? '&' : '?';
        return $url . $separator . 'utm_source=Nextplay';
    }
}
