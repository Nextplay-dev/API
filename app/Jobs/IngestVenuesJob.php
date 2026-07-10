<?php

namespace App\Jobs;

use App\Services\VenueIngestion\VenueIngestionPipeline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IngestVenuesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(VenueIngestionPipeline $pipeline): void
    {
        $pipeline->execute();
    }
}
