<?php

namespace App\Http\Controllers;

use App\Actions\Workflow\ListWorkflowsAction;
use App\Actions\Workflow\ShowWorkflowAction;
use App\Actions\Workflow\ListAvailableWorkflowsAction;
use App\Actions\Workflow\ListPendingReviewsAction;
use App\Actions\Workflow\StartWorkflowAction;
use App\Actions\Workflow\SignalWorkflowAction;
use App\Actions\Workflow\AbortWorkflowAction;
use App\Actions\Workflow\DeleteWorkflowAction;
use App\Services\Workflow\WorkflowBroadcastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WorkflowController extends Controller
{
    public function index(ListWorkflowsAction $action): JsonResponse
    {
        return response()->json($action->execute());
    }

    public function show($id, ShowWorkflowAction $action): JsonResponse
    {
        return response()->json($action->execute((int) $id));
    }

    public function available(ListAvailableWorkflowsAction $action): JsonResponse
    {
        return response()->json($action->execute());
    }

    public function pendingReviews(ListPendingReviewsAction $action): JsonResponse
    {
        return response()->json($action->execute());
    }

    public function store(Request $request, StartWorkflowAction $action): JsonResponse
    {
        $request->validate([
            'class' => ['required', 'string'],
            'params' => ['required', 'array'],
        ]);

        $workflowId = $action->execute($request->class, $request->params);

        return response()->json([
            'message' => 'Workflow started successfully',
            'id' => $workflowId,
        ]);
    }

    public function signal(Request $request, $id, SignalWorkflowAction $action, WorkflowBroadcastService $broadcast): JsonResponse
    {
        $request->validate([
            'signal' => ['required', 'string', 'in:approve,reject'],
            'data' => ['nullable', 'array'],
        ]);

        $action->execute($id, $request->signal, $request->data);
        Cache::put("workflow:pending_review_handled:{$id}", true, now()->addMinutes(10));
        $broadcast->notifyPendingReviewRemoved((int) $id);
        $broadcast->notifyUpdated((int) $id);

        return response()->json([
            'message' => 'Signal sent successfully',
        ]);
    }

    public function abort(Request $request, $id, AbortWorkflowAction $action, WorkflowBroadcastService $broadcast): JsonResponse
    {
        $action->execute($id);
        $broadcast->notifyLogLine((int) $id, '[' . now()->toTimeString() . '] Workflow aborted by Administrator.');
        $broadcast->notifyUpdated((int) $id, true);

        return response()->json([
            'message' => 'Workflow aborted successfully',
        ]);
    }

    public function destroy(Request $request, $id, DeleteWorkflowAction $action, WorkflowBroadcastService $broadcast): JsonResponse
    {
        $action->execute($id);
        $broadcast->notifyDeleted((int) $id);

        return response()->json([
            'message' => 'Workflow deleted successfully',
        ]);
    }
}
