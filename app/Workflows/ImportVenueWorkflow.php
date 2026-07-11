<?php

namespace App\Workflows;

use App\Services\Workflow\WorkflowBroadcastService;
use App\Workflows\Activities\FetchSingleGooglePlaceActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;
use Workflow\Workflow;
use Workflow\QueryMethod;
use function Workflow\activity;
use function Workflow\child;

class ImportVenueWorkflow extends Workflow
{
    public int $totalCount = 0;
    public string $step = 'started';

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

    public function execute(string $googlePlaceId)
    {
        $this->writeLog('Starting single venue import workflow...');
        $this->persistStep('fetching_venue');
        $this->writeLog("Querying Google Places API for place ID: {$googlePlaceId}...");

        $candidate = yield activity(FetchSingleGooglePlaceActivity::class, $googlePlaceId);

        if (!$candidate) {
            $this->writeLog('Could not retrieve details for Google Place ID: ' . $googlePlaceId);
            $this->persistStep('failed');
            return 'failed';
        }

        $this->totalCount = 1;
        if (!$this->replaying) {
            Cache::forever("workflow:total_count:{$this->storedWorkflow->id}", 1);
        }

        $this->writeLog("Successfully fetched venue details for \"{$candidate['name']}\".");
        $this->persistStep('processing_candidate');
        $this->writeLog('Spawning child workflow for candidate crawling and classification...');

        yield child(ProcessVenueCandidateWorkflow::class, $candidate);

        $this->writeLog('Child workflow completed.');
        $this->persistStep('completed');
        $this->writeLog('Venue import workflow completed successfully.');

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
