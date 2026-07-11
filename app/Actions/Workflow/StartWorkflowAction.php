<?php

namespace App\Actions\Workflow;

use Workflow\WorkflowStub;

class StartWorkflowAction
{
    public function execute(string $class, array $params): string
    {
        $workflow = WorkflowStub::make($class);

        $orderedArgs = [];
        if ($class === 'App\Workflows\IngestVenuesWorkflow') {
            $lat = 50.629250;
            $lon = 3.057256;

            if (isset($params['location']) && is_array($params['location'])) {
                $lat = (float) ($params['location']['lat'] ?? $lat);
                $lon = (float) ($params['location']['lon'] ?? $lon);
            }

            // Convert radius from km to meters for Google Places API
            $radiusKm = (int) ($params['radius'] ?? 2); // Default: 2 km
            $orderedArgs = [
                $lat,
                $lon,
                $radiusKm * 1000,
            ];
        } elseif ($class === 'App\Workflows\ImportVenueWorkflow') {
            $placeId = '';
            if (isset($params['google_place'])) {
                $placeId = is_array($params['google_place']) 
                    ? ($params['google_place']['placeId'] ?? '') 
                    : $params['google_place'];
            }
            $orderedArgs = [
                (string) $placeId,
            ];
        }

        $workflow->start(...$orderedArgs);
        return $workflow->id();
    }
}
