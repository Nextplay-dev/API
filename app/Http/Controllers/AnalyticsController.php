<?php

namespace App\Http\Controllers;

use App\Actions\Analytics\ListAnalyticsOverviewAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request, ListAnalyticsOverviewAction $action): JsonResponse
    {
        abort_unless($request->user()?->hasPermissionTo('analytics.view'), 403, 'This action is unauthorized.');

        $page = $request->query('page', 1);
        $actionFilter = $request->query('action');

        $data = $action->handle((int) $page, $actionFilter);

        return response()->json($data);
    }
}
