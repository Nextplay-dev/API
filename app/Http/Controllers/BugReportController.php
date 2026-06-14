<?php

namespace App\Http\Controllers;

use App\Actions\BugReport\DeleteBugReportAction;
use App\Actions\BugReport\ListBugReportsAction;
use App\Actions\BugReport\StoreBugReportAction;
use App\DTOs\BugReportDTO;
use App\Http\Requests\StoreBugReportRequest;
use App\Http\Resources\BugReportResource;
use App\Models\BugReport;
use Illuminate\Http\JsonResponse;

class BugReportController extends Controller
{
    public function index(ListBugReportsAction $action): JsonResponse
    {
        $bugReports = $action->handle();

        return BugReportResource::collection($bugReports)->response();
    }

    public function store(StoreBugReportRequest $request, StoreBugReportAction $action): JsonResponse
    {
        $bugReport = $action->handle(BugReportDTO::fromRequest($request));

        $bugReport->load('user');

        return BugReportResource::make($bugReport)->response()->setStatusCode(201);
    }

    public function destroy(BugReport $bugReport, DeleteBugReportAction $action): JsonResponse
    {
        $action->handle($bugReport);

        return response()->json(null, 204);
    }
}
