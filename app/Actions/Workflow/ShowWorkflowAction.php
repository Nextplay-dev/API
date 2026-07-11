<?php

namespace App\Actions\Workflow;

use App\Services\Workflow\WorkflowPresenter;

class ShowWorkflowAction
{
    public function __construct(
        private WorkflowPresenter $presenter,
    ) {
    }

    public function execute(int $id): array
    {
        return $this->presenter->buildShow($id);
    }
}
