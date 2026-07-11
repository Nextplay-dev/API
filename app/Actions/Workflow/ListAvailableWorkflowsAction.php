<?php

namespace App\Actions\Workflow;

use App\Workflows\IngestVenuesWorkflow;

class ListAvailableWorkflowsAction
{
    public function execute(): array
    {
        return [
            [
                'class' => IngestVenuesWorkflow::class,
                'name' => 'Ingest Venues',
                'description' => 'Import leisure venues from Google Places and enrich them using AI.',
                'parameters' => [
                    [
                        'name' => 'location',
                        'type' => 'geo',
                        'label' => 'City',
                        'default' => null,
                        'required' => true,
                    ],
                    [
                        'name' => 'radius',
                        'type' => 'number',
                        'label' => 'Radius',
                        'default' => 2,
                        'required' => true,
                    ],
                ]
            ],
            [
                'class' => \App\Workflows\ImportVenueWorkflow::class,
                'name' => 'Import Venue',
                'description' => 'Import a specific leisure venue from Google Places using its Google Place ID.',
                'parameters' => [
                    [
                        'name' => 'google_place',
                        'type' => 'google_place',
                        'label' => 'Google Place ID',
                        'default' => null,
                        'required' => true,
                    ],
                ]
            ]
        ];
    }
}
