<?php

namespace App\Actions\Workflow;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Workflow\Models\StoredWorkflow;
use Workflow\States\WorkflowFailedStatus;
use Workflow\WorkflowStub;

class AbortWorkflowAction
{
    private const STALE_CHILD_KEYS = [
        'workflow:enriched:%s',
        'workflow:candidate:%s',
        'workflow:approved:%s',
        'workflow:review_broadcasted:%s',
    ];

    public function execute(string $id): void
    {
        $parent = StoredWorkflow::findOrFail($id);

        $children = StoredWorkflow::whereIn('id', function ($query) use ($id) {
            $query->select('child_workflow_id')
                  ->from('workflow_relationships')
                  ->where('parent_workflow_id', $id);
        })->get();

        foreach ($children as $child) {
            if (Cache::get("workflow:step:{$child->id}") === 'pending_review') {
                Cache::put("workflow:pending_review_handled:{$child->id}", true, now()->addMinutes(10));
            }

            try {
                $childStub = WorkflowStub::load($child->id);
                if ($childStub->running()) {
                    $childStub->fail(new \Exception('Parent workflow aborted by Administrator'));
                    Cache::forever("workflow:step:{$child->id}", 'failed');
                    Cache::forever("workflow:status:{$child->id}", 'WorkflowFailedStatus');
                }
            } catch (\Throwable) {
            }

            foreach (self::STALE_CHILD_KEYS as $key) {
                Cache::forget(sprintf($key, $child->id));
            }
        }

        try {
            $parentStub = WorkflowStub::load($id);
            $parentStub->fail(new \Exception('Aborted by Administrator'));
            Cache::forever("workflow:step:{$id}", 'failed');
            Cache::forever("workflow:status:{$id}", 'WorkflowFailedStatus');
        } catch (\Throwable) {
            Cache::forever("workflow:step:{$id}", 'failed');
            Cache::forever("workflow:status:{$id}", 'WorkflowFailedStatus');
        }

        DB::table('workflow_custom_logs')->insert([
            'stored_workflow_id' => $id,
            'message' => '[' . now()->toTimeString() . '] Workflow aborted by Administrator.',
            'created_at' => now(),
        ]);
    }
}
