<?php

namespace App\Console\Commands;

use App\Services\VenueIngestion\VenueIngestionPipeline;
use Illuminate\Console\Command;

class IngestVenuesCommand extends Command
{
    protected $signature = 'venues:ingest {--lat=50.6297} {--lon=3.0573} {--radius=15}';

    protected $description = 'Ingest leisure venues from Google Places and enrich them by crawling websites';

    public function handle(VenueIngestionPipeline $pipeline): int
    {
        $this->info('Starting venue ingestion around coordinates...');

        $lat = (float) $this->option('lat');
        $lon = (float) $this->option('lon');
        $radiusKm = (int) $this->option('radius');
        $radiusMeters = $radiusKm * 1000;

        $this->line(sprintf('Target: Lat %f, Lon %f, Radius %d km (%d meters)', $lat, $lon, $radiusKm, $radiusMeters));

        try {
            $result = $pipeline->execute($lat, $lon, $radiusMeters);
            $this->info(sprintf('Ingestion completed. Imported: %d, Updated: %d', $result['imported'], $result['updated']));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Ingestion failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
