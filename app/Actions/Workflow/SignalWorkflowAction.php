<?php

namespace App\Actions\Workflow;

use Workflow\WorkflowStub;

class SignalWorkflowAction
{
    public function execute(string $workflowId, string $signal, ?array $data = null): void
    {
        $workflow = WorkflowStub::load($workflowId);
        if ($signal === 'approve') {
            $workflow->approve($data ?? []);
        } else {
            $workflow->reject();
        }
    }
}
