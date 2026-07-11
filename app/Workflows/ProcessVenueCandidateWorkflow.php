<?php

namespace App\Workflows;

use App\Services\Workflow\WorkflowBroadcastService;
use App\Workflows\Activities\CrawlAndClassifyVenueActivity;
use App\Workflows\Activities\SaveVenueActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;
use Workflow\SignalMethod;
use Workflow\Workflow;
use Workflow\QueryMethod;
use function Workflow\activity;
use function Workflow\await;

class ProcessVenueCandidateWorkflow extends Workflow
{
    public ?array $candidate = null;
    public ?array $enriched = null;
    public ?array $enrichedEdits = null;
    public ?bool $approved = null;
    public string $step = 'started';

    #[QueryMethod]
    public function getStep(): string
    {
        return $this->step;
    }

    #[QueryMethod]
    public function getCandidate(): ?array
    {
        return $this->candidate;
    }

    #[QueryMethod]
    public function getEnriched(): ?array
    {
        return $this->enriched;
    }

    #[QueryMethod]
    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    #[SignalMethod]
    public function approve(array $modifiedData = []): void
    {
        $this->approved = true;
        if (!empty($modifiedData)) {
            $this->enrichedEdits = $modifiedData;
            $this->enriched = array_merge($this->enriched ?? [], $modifiedData);
        }
        if (!$this->replaying) {
            Cache::forever("workflow:approved:{$this->storedWorkflow->id}", true);
            if (!empty($modifiedData)) {
                Cache::forever("workflow:enriched:{$this->storedWorkflow->id}", $this->enriched);
            }
        }
    }

    #[SignalMethod]
    public function reject(): void
    {
        $this->approved = false;
        if (!$this->replaying) {
            Cache::forever("workflow:approved:{$this->storedWorkflow->id}", false);
            Cache::forever("workflow:step:{$this->storedWorkflow->id}", 'rejecting');
        }
    }

    public function failed(Throwable $throwable): void
    {
        if (!$this->replaying) {
            $this->persistStep('failed');
            $this->writeLog('Child workflow failed: ' . $throwable->getMessage());
        }

        parent::failed($throwable);
    }

    public function execute(array $candidate)
    {
        $this->candidate = $candidate;
        if (!$this->replaying) {
            Cache::forever("workflow:candidate:{$this->storedWorkflow->id}", $candidate);
        }

        $name = $candidate['name'] ?? 'unknown';
        $placeId = $candidate['google_place_id'] ?? null;
        $website = $candidate['website'] ?? null;

        $alreadyImported = $placeId && DB::table('venues')->where('google_place_id', $placeId)->exists();
        if ($alreadyImported) {
            $this->writeLog("Candidate \"{$name}\" already imported. Skipping.");
            $this->persistStep('skipped');
            return 'already_imported';
        }

        $this->writeLog("Candidate \"{$name}\" added to crawl queue.");
        $this->persistStep('enriching');

        if (!$website) {
            $this->writeLog("Candidate \"{$name}\" has no website. Skipping.");
            $this->persistStep('skipped');
            return 'skipped';
        }

        $lowerName = mb_strtolower($name);
        $lowerWebsite = mb_strtolower($website);
        $retailIndicators = ['intersport', 'decathlon', 'go-sport', 'sports-world', 'boutique', 'shop', 'magasin'];
        foreach ($retailIndicators as $indicator) {
            if (str_contains($lowerName, $indicator) || str_contains($lowerWebsite, $indicator)) {
                $this->writeLog("Candidate \"{$name}\" appears to be a retail store. Skipping.");
                $this->persistStep('skipped');
                if (!$this->replaying) {
                    DB::table('rejected_venue_candidates')->upsert(
                        [
                            'osm_id' => $placeId,
                            'data' => json_encode($candidate),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        'osm_id',
                        ['data', 'updated_at']
                    );
                }
                return 'skipped';
            }
        }

        $websiteLabel = "({$website})";
        $this->writeLog("Crawling website {$websiteLabel} and running AI classification...");

        $enriched = yield activity(CrawlAndClassifyVenueActivity::class, $candidate);

        if (!($enriched['suitable'] ?? false)) {
            $reason = $enriched['reject_reason'] ?? 'not_suitable';
            $this->writeLog("Candidate \"{$name}\" skipped: {$reason}.");
            if (!$this->replaying) {
                $placeId = $this->candidate['google_place_id'];
                DB::table('rejected_venue_candidates')->upsert(
                    [
                        'osm_id' => $placeId,
                        'data' => json_encode($this->candidate),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    'osm_id',
                    ['data', 'updated_at']
                );
            }
            $this->persistStep('skipped');
            return 'skipped';
        }

        $this->enriched = $enriched;
        if ($this->enrichedEdits !== null) {
            $this->enriched = array_merge($this->enriched, $this->enrichedEdits);
        }
        if (!$this->replaying) {
            Cache::forever("workflow:enriched:{$this->storedWorkflow->id}", $this->enriched);
        }

        $this->writeLog("Candidate \"{$name}\" successfully crawled and classified. Awaiting admin approval.");
        $this->persistStep('pending_review');

        if (
            !$this->replaying
            && Cache::get("workflow:approved:{$this->storedWorkflow->id}") === null
            && !Cache::get("workflow:review_broadcasted:{$this->storedWorkflow->id}")
        ) {
            Cache::forever("workflow:review_broadcasted:{$this->storedWorkflow->id}", true);
            app(WorkflowBroadcastService::class)->notifyPendingReviewAdded($this->storedWorkflow->id);
        }

        yield await(fn () => $this->approved !== null);

        if ($this->approved === true) {
            $this->writeLog('Ingest approved by administrator.');
            $this->persistStep('saving');
            $this->writeLog('Saving venue and synchronizing activities to database...');

            $result = yield activity(SaveVenueActivity::class, $this->enriched);

            $this->writeLog("Venue \"{$name}\" successfully saved to database.");
            $this->persistStep('saved');
            return $result;
        }

        $this->writeLog('Ingest rejected by administrator.');
        if (!$this->replaying) {
            $placeId = $this->candidate['google_place_id'];
            DB::table('rejected_venue_candidates')->upsert(
                [
                    'osm_id' => $placeId,
                    'data' => json_encode($this->candidate),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'osm_id',
                ['data', 'updated_at']
            );
        }
        $this->persistStep('rejected');
        return 'rejected';
    }

    private function persistStep(string $step): void
    {
        $this->step = $step;
        if (!$this->replaying) {
            Cache::forever("workflow:step:{$this->storedWorkflow->id}", $step);
            $derivedStatus = match ($step) {
                'skipped', 'saved', 'rejected' => 'WorkflowCompletedStatus',
                'failed' => 'WorkflowFailedStatus',
                default => 'WorkflowRunningStatus',
            };
            Cache::forever("workflow:status:{$this->storedWorkflow->id}", $derivedStatus);

            $force = in_array($step, ['skipped', 'saved', 'rejected', 'failed'], true);
            app(WorkflowBroadcastService::class)->notifyUpdated($this->storedWorkflow->id, $force);
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
