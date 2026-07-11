<?php

namespace App\Actions\Workflow;

use App\Services\VenueIngestion\VenueTypeInferenceService;
use App\Services\Workflow\WorkflowPresenter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Workflow\Models\StoredWorkflow;

class ListPendingReviewsAction
{
    public function __construct(
        private WorkflowPresenter $presenter,
        private VenueTypeInferenceService $typeInference,
    ) {
    }

    public function execute(): array
    {
        $childrenStored = StoredWorkflow::where('class', 'App\Workflows\ProcessVenueCandidateWorkflow')
            ->whereIn('status', ['running', 'waiting'])
            ->get();

        $reviews = [];
        foreach ($childrenStored as $childStored) {
            if (Cache::get("workflow:step:{$childStored->id}") !== 'pending_review') {
                continue;
            }

            if (Cache::get("workflow:pending_review_handled:{$childStored->id}")) {
                continue;
            }

            $enriched = Cache::get("workflow:enriched:{$childStored->id}");
            if ($enriched && $this->typeInference->isBlacklisted($enriched)) {
                Cache::put("workflow:pending_review_handled:{$childStored->id}", true, now()->addMinutes(10));
                $candidate = Cache::get("workflow:candidate:{$childStored->id}", []);
                $placeId = $candidate['google_place_id'] ?? null;
                if (!empty($placeId)) {
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
                continue;
            }

            $review = $this->presenter->buildPendingReview($childStored->id);
            if ($review) {
                $reviews[] = $review;
            }
        }

        return $reviews;
    }
}
