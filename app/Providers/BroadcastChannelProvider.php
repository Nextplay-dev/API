<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastChannelProvider extends ServiceProvider
{
    public function boot(): void
    {
        Broadcast::routes(['prefix' => 'v1', 'middleware' => ['api', 'auth:sanctum']]);
        require base_path('routes/channels.php');
    }
}
