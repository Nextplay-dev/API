<?php

namespace App\Services\Workflow;

use App\Events\WorkflowLogLineAdded;
use App\Events\WorkflowUpdated;
use Illuminate\Support\Facades\Cache;
use Workflow\Models\StoredWorkflow;

class WorkflowBroadcastService
{
    public function __construct(
        private WorkflowPresenter $presenter,
    ) {
    }

    public function notifyCreated(int $workflowId): void
    {
        $parentId = $this->presenter->resolveParentId($workflowId);

        if ($parentId !== $workflowId) {
            return;
        }

        $parent = StoredWorkflow::find($parentId);
        if (!$parent) {
            return;
        }

        $this->dispatchUpdated([
            'action' => 'created',
            'workflow_id' => $parentId,
            'summary' => $this->presenter->buildSummary($parent),
        ]);
    }

    public function notifyUpdated(int $workflowId, bool $force = false): void
    {
        $parentId = $this->presenter->resolveParentId($workflowId);
        $parent = StoredWorkflow::find($parentId);
        if (!$parent) {
            return;
        }

        $pendingKey = "workflow_broadcast_pending:{$parentId}";
        Cache::put($pendingKey, [
            'summary' => $this->presenter->buildSummary($parent),
        ], now()->addMinutes(10));

        $throttleKey = "workflow_broadcast_throttle:{$parentId}";

        if ($force) {
            Cache::forget($throttleKey);
            $this->flushPending($parentId);
            return;
        }

        if (Cache::has($throttleKey)) {
            return;
        }

        Cache::put($throttleKey, true, now()->addSeconds(2));
        $this->flushPending($parentId);
    }

    public function notifyLogLine(int $workflowId, string $logLine): void
    {
        $parentId = $this->presenter->resolveParentId($workflowId);

        event(new WorkflowLogLineAdded([
            'workflow_id' => $parentId,
            'log' => $logLine,
        ]));
    }

    public function notifyDeleted(int $parentId): void
    {
        Cache::forget("workflow_broadcast_pending:{$parentId}");
        Cache::forget("workflow_broadcast_throttle:{$parentId}");

        $this->dispatchUpdated([
            'action' => 'deleted',
            'workflow_id' => $parentId,
        ]);
    }

    public function notifyPendingReviewAdded(int $childId): void
    {
        $parentId = $this->presenter->resolveParentId($childId);
        $review = $this->presenter->buildPendingReview($childId);

        if (!$review) {
            return;
        }

        $this->dispatchUpdated([
            'action' => 'pending_review_added',
            'workflow_id' => $parentId,
            'child_id' => $childId,
            'pending_review' => $review,
        ]);
    }

    public function notifyPendingReviewRemoved(int $childId): void
    {
        $parentId = $this->presenter->resolveParentId($childId);

        $this->dispatchUpdated([
            'action' => 'pending_review_removed',
            'workflow_id' => $parentId,
            'child_id' => $childId,
        ]);
    }

    private function flushPending(int $parentId): void
    {
        $pendingKey = "workflow_broadcast_pending:{$parentId}";
        $pending = Cache::pull($pendingKey);

        if (!isset($pending['summary'])) {
            return;
        }

        $this->dispatchUpdated([
            'action' => 'updated',
            'workflow_id' => $parentId,
            'summary' => $pending['summary'],
        ]);
    }

    private function dispatchUpdated(array $payload): void
    {
        event(new WorkflowUpdated($payload));
    }
}
