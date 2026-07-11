<?php

namespace App\Actions\Workflow;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DeleteWorkflowAction
{
    private const CACHE_KEYS = [
        'workflow:step:%s',
        'workflow:enriched:%s',
        'workflow:candidate:%s',
        'workflow:approved:%s',
        'workflow:review_broadcasted:%s',
        'workflow:status:%s',
        'workflow:total_count:%s',
        'workflow:pending_review_handled:%s',
    ];

    public function execute(string $id): void
    {
        DB::transaction(function () use ($id) {
            $childIds = DB::table('workflow_relationships')
                ->where('parent_workflow_id', $id)
                ->pluck('child_workflow_id')
                ->toArray();

            $allIds = array_merge([$id], $childIds);

            DB::table('workflow_logs')->whereIn('stored_workflow_id', $allIds)->delete();
            DB::table('workflow_signals')->whereIn('stored_workflow_id', $allIds)->delete();
            DB::table('workflow_timers')->whereIn('stored_workflow_id', $allIds)->delete();
            DB::table('workflow_exceptions')->whereIn('stored_workflow_id', $allIds)->delete();
            DB::table('workflow_custom_logs')->whereIn('stored_workflow_id', $allIds)->delete();
            DB::table('workflow_relationships')
                ->whereIn('parent_workflow_id', $allIds)
                ->orWhereIn('child_workflow_id', $allIds)
                ->delete();
            DB::table('workflows')->whereIn('id', $allIds)->delete();

            foreach ($allIds as $wid) {
                foreach (self::CACHE_KEYS as $key) {
                    Cache::forget(sprintf($key, $wid));
                }
            }
        });
    }
}
