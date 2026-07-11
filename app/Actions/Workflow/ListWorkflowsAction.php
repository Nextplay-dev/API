<?php

namespace App\Actions\Workflow;

use App\Services\Workflow\WorkflowPresenter;
use Workflow\Models\StoredWorkflow;

class ListWorkflowsAction
{
    public function __construct(
        private WorkflowPresenter $presenter,
    ) {
    }

    public function execute(): array
    {
        $parents = StoredWorkflow::whereNotIn('id', function ($query) {
            $query->select('child_workflow_id')->from('workflow_relationships');
        })->orderBy('created_at', 'desc')->paginate(15);

        $items = [];
        foreach ($parents as $parent) {
            $items[] = $this->presenter->buildSummary($parent);
        }

        return [
            'data' => $items,
            'meta' => [
                'current_page' => $parents->currentPage(),
                'last_page' => $parents->lastPage(),
                'total' => $parents->total(),
            ],
        ];
    }
}
