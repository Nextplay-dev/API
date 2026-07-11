<?php

namespace App\Services\Workflow;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Workflow\Models\StoredWorkflow;
use Workflow\WorkflowStub;

class WorkflowPresenter
{
    private const WORKFLOW_NAMES = [
        'App\Workflows\IngestVenuesWorkflow' => 'Ingest Venues',
        'App\Workflows\ImportVenueWorkflow' => 'Import Venue',
    ];

    public function resolveParentId(int $workflowId): int
    {
        $parentId = DB::table('workflow_relationships')
            ->where('child_workflow_id', $workflowId)
            ->value('parent_workflow_id');

        return $parentId ? (int) $parentId : $workflowId;
    }

    public function buildSummary(StoredWorkflow $parent): array
    {
        $parent->refresh();
        $status = $this->resolveStatus($parent);
        $step = $this->resolveStep($parent->id, $status);

        $childrenCount = DB::table('workflow_relationships')
            ->where('parent_workflow_id', $parent->id)
            ->count();

        return [
            'id' => $parent->id,
            'name' => self::WORKFLOW_NAMES[$parent->class] ?? class_basename($parent->class),
            'class' => $parent->class,
            'status' => $status,
            'step' => $step,
            'total_count' => (int) Cache::get("workflow:total_count:{$parent->id}", 0),
            'children_count' => $childrenCount,
            'created_at' => $parent->created_at->toIso8601String(),
            'updated_at' => $parent->updated_at->toIso8601String(),
        ];
    }

    public function buildShow(int $parentId): array
    {
        $parent = StoredWorkflow::findOrFail($parentId);

        $isChild = DB::table('workflow_relationships')
            ->where('child_workflow_id', $parentId)
            ->exists();

        if ($isChild) {
            abort(404);
        }

        $summary = $this->buildSummary($parent);

        $childrenStored = StoredWorkflow::whereIn('id', function ($query) use ($parent) {
            $query->select('child_workflow_id')
                ->from('workflow_relationships')
                ->where('parent_workflow_id', $parent->id);
        })->get();

        $exceptions = [];
        $parentStub = WorkflowStub::load($parent->id);
        foreach ($parentStub->exceptions() as $exc) {
            $exceptions[] = $this->parseException($exc);
        }

        $children = [];
        foreach ($childrenStored as $childStored) {
            $cachedStatus = Cache::get("workflow:status:{$childStored->id}");

            if ($cachedStatus) {
                $childStatus = str_replace(['Workflow', 'Status'], '', $cachedStatus);
                $childExceptions = StoredWorkflow::find($childStored->id)?->exceptions ?? collect();
                foreach ($childExceptions as $exc) {
                    $exceptions[] = $this->parseException($exc);
                }
            } else {
                $childStub = WorkflowStub::load($childStored->id);
                $childStatus = str_replace(['Workflow', 'Status'], '', class_basename($childStub->status()));
                foreach ($childStub->exceptions() as $exc) {
                    $exceptions[] = $this->parseException($exc);
                }
            }

            $childStep = $this->resolveStep($childStored->id, $childStatus);

            $children[] = [
                'id' => $childStored->id,
                'status' => $childStatus,
                'step' => $childStep,
                'candidate' => Cache::get("workflow:candidate:{$childStored->id}"),
                'enriched' => Cache::get("workflow:enriched:{$childStored->id}"),
                'approved' => Cache::get("workflow:approved:{$childStored->id}"),
                'created_at' => $childStored->created_at->toIso8601String(),
            ];
        }

        $storedIds = array_merge([$parent->id], $childrenStored->pluck('id')->toArray());
        $logs = DB::table('workflow_custom_logs')
            ->whereIn('stored_workflow_id', $storedIds)
            ->orderBy('id', 'asc')
            ->pluck('message')
            ->toArray();

        return array_merge($summary, [
            'children' => $children,
            'exceptions' => $exceptions,
            'logs' => $logs,
        ]);
    }

    public function buildPendingReview(int $childId): ?array
    {
        $childStored = StoredWorkflow::find($childId);
        if (!$childStored) {
            return null;
        }

        $step = Cache::get("workflow:step:{$childId}", 'started');
        if ($step !== 'pending_review') {
            return null;
        }

        $enriched = Cache::get("workflow:enriched:{$childId}");
        if (!$enriched) {
            $childStub = WorkflowStub::load($childId);
            $enriched = $childStub->enriched ?? [];
        }

        $categoryName = $enriched['category_name'] ?? 'Team';
        $category = Category::where('name', $categoryName)->first();
        $categoryId = $category?->id;

        $fields = [
            'name' => ['label' => 'Name', 'type' => 'text', 'value' => $enriched['name'] ?? 'Unknown'],
            'address' => ['label' => 'Address', 'type' => 'text', 'value' => $enriched['address'] ?? ''],
            'category_id' => ['label' => 'Category', 'type' => 'category', 'value' => $categoryId, 'category_name' => $categoryName],
            'website' => ['label' => 'Website', 'type' => 'url', 'value' => $enriched['website'] ?? ''],
            'external_booking_url' => ['label' => 'Booking URL', 'type' => 'url', 'value' => $enriched['external_booking_url'] ?? ''],
            'activities' => ['label' => 'Activities', 'type' => 'tags', 'value' => $enriched['activities'] ?? []],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'value' => $enriched['description'] ?? ''],
        ];

        return [
            'id' => $childId,
            'title' => 'Review Venue: ' . ($enriched['name'] ?? 'Unknown'),
            'fields' => $fields,
        ];
    }

    private function resolveStatus(StoredWorkflow $parent): string
    {
        $cachedStep = Cache::get("workflow:step:{$parent->id}");
        if ($cachedStep === 'failed') {
            return 'Failed';
        }

        $cachedStatus = Cache::get("workflow:status:{$parent->id}");
        if ($cachedStatus) {
            return str_replace(['Workflow', 'Status'], '', $cachedStatus);
        }

        return str_replace(['Workflow', 'Status'], '', class_basename($parent->status::class));
    }

    private function resolveStep(int $workflowId, string $status): string
    {
        $step = Cache::get("workflow:step:{$workflowId}", 'started');

        if ($status === 'Completed') {
            return 'completed';
        }

        if ($status === 'Failed') {
            return 'failed';
        }

        return $step;
    }

    public function parseException($exc): array
    {
        try {
            $unserialized = \Workflow\Serializers\Serializer::unserialize($exc->exception);
        } catch (\Throwable) {
            return [
                'class' => $exc->class ?? 'Unknown',
                'message' => 'Could not deserialize exception',
                'file' => '',
                'line' => 0,
                'trace' => '',
            ];
        }

        if ($unserialized instanceof \Throwable) {
            return [
                'class' => $exc->class,
                'message' => $unserialized->getMessage(),
                'file' => $unserialized->getFile(),
                'line' => $unserialized->getLine(),
                'trace' => $unserialized->getTraceAsString(),
            ];
        }

        if (is_array($unserialized)) {
            $traceStr = '';
            if (is_array($unserialized['trace'] ?? null)) {
                $traceStr = implode("\n", array_map(function ($t) {
                    return ($t['file'] ?? '') . ':' . ($t['line'] ?? '') . ' ' . ($t['function'] ?? '');
                }, $unserialized['trace']));
            } else {
                $traceStr = (string) ($unserialized['trace'] ?? '');
            }

            return [
                'class' => $exc->class,
                'message' => $unserialized['message'] ?? 'Unknown error',
                'file' => $unserialized['file'] ?? '',
                'line' => $unserialized['line'] ?? 0,
                'trace' => $traceStr,
            ];
        }

        return [
            'class' => $exc->class,
            'message' => is_string($unserialized) ? $unserialized : json_encode($unserialized),
            'file' => '',
            'line' => 0,
            'trace' => '',
        ];
    }
}
