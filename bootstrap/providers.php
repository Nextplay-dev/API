<?php

use App\Providers\AppServiceProvider;
use App\Providers\BroadcastChannelProvider;
use App\Providers\TelescopeServiceProvider;

return [
    AppServiceProvider::class,
    TelescopeServiceProvider::class,
    BroadcastChannelProvider::class,
];
