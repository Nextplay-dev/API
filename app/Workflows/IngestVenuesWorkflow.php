<?php

namespace App\Workflows;

use App\Services\Workflow\WorkflowBroadcastService;
use App\Workflows\Activities\FetchGooglePlacesVenuesActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;
use Workflow\Workflow;
use Workflow\QueryMethod;
use function Workflow\activity;
use function Workflow\all;
use function Workflow\child;

class IngestVenuesWorkflow extends Workflow
{
    public int $totalCount = 0;
    public string $step = 'started';
    public array $candidates = [];

    #[QueryMethod]
    public function getStep(): string
    {
        return $this->step;
    }

    #[QueryMethod]
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function failed(Throwable $throwable): void
    {
        if (!$this->replaying) {
            $this->persistStep('failed');
            $this->writeLog('Workflow failed: ' . $throwable->getMessage());
        }

        parent::failed($throwable);
    }

    public function execute(float $lat = 50.629250, float $lon = 3.057256, int $radius = 2000)
    {
        $this->writeLog('Starting venue ingestion workflow...');
        $this->persistStep('fetching_venues');
        $this->writeLog("Querying Google Places API (lat: {$lat}, lon: {$lon}, radius: {$radius})...");

        $candidates = yield activity(FetchGooglePlacesVenuesActivity::class, $lat, $lon, $radius);

        $existingPlaceIds = DB::table('venues')->whereNotNull('google_place_id')->pluck('google_place_id')->toArray();
        $rejectedIds = DB::table('rejected_venue_candidates')->pluck('osm_id')->toArray();

        $skipReasons = ['already_imported' => 0, 'previously_rejected' => 0];
        $candidates = array_values(array_filter(
            $candidates,
            function ($c) use ($existingPlaceIds, $rejectedIds, &$skipReasons) {
                if (in_array($c['google_place_id'], $existingPlaceIds)) {
                    $skipReasons['already_imported']++;
                    return false;
                }
                if (in_array($c['google_place_id'], $rejectedIds)) {
                    $skipReasons['previously_rejected']++;
                    return false;
                }
                return true;
            }
        ));

        $this->candidates = $candidates;
        $this->totalCount = count($candidates);

        if (!$this->replaying) {
            Cache::forever("workflow:total_count:{$this->storedWorkflow->id}", $this->totalCount);
        }

        if (!$this->replaying) {
            if ($skipReasons['already_imported'] > 0) {
                $this->writeLog("Skipped {$skipReasons['already_imported']} already imported venue(s).");
            }
            if ($skipReasons['previously_rejected'] > 0) {
                $this->writeLog("Skipped {$skipReasons['previously_rejected']} previously rejected candidate(s).");
            }
        }

        if (empty($candidates)) {
            $this->writeLog('No candidates found. Aborting.');
            $this->persistStep('completed');
            return 'no_candidates';
        }

        $this->writeLog('Fetched ' . count($candidates) . ' candidates from Google Places.');
        $this->persistStep('processing_candidates');
        $this->writeLog('Spawning child workflows for parallel candidate crawling and classification...');

        $children = array_map(fn ($c) => child(ProcessVenueCandidateWorkflow::class, $c), $candidates);

        yield all($children);

        $this->writeLog('All child workflows completed.');
        $this->persistStep('completed');
        $this->writeLog('Ingestion workflow completed successfully.');

        return 'completed';
    }

    private function persistStep(string $step): void
    {
        $this->step = $step;
        if (!$this->replaying) {
            Cache::forever("workflow:step:{$this->storedWorkflow->id}", $step);
            $derivedStatus = match ($step) {
                'completed' => 'WorkflowCompletedStatus',
                'failed' => 'WorkflowFailedStatus',
                default => 'WorkflowRunningStatus',
            };
            Cache::forever("workflow:status:{$this->storedWorkflow->id}", $derivedStatus);

            if (in_array($step, ['completed', 'failed'], true)) {
                $this->clearPendingChildren();
            }

            $force = in_array($step, ['completed', 'failed'], true);
            app(WorkflowBroadcastService::class)->notifyUpdated($this->storedWorkflow->id, $force);
        }
    }

    private function clearPendingChildren(): void
    {
        $childIds = DB::table('workflow_relationships')
            ->where('parent_workflow_id', $this->storedWorkflow->id)
            ->pluck('child_workflow_id');

        foreach ($childIds as $childId) {
            if (Cache::get("workflow:step:{$childId}") === 'pending_review') {
                Cache::put("workflow:pending_review_handled:{$childId}", true, now()->addMinutes(10));
            }
        }
    }

    private function writeLog(string $message): void
    {
        if ($this->replaying) {
            return;
        }

        $formatted = '[' . now()->toTimeString() . '] ' . $message;

        DB::table('workflow_custom_logs')->insert([
            'stored_workflow_id' => $this->storedWorkflow->id,
            'message' => $formatted,
            'created_at' => now(),
        ]);

        app(WorkflowBroadcastService::class)->notifyLogLine($this->storedWorkflow->id, $formatted);
    }
}
