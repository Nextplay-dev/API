<?php

namespace App\Http\Controllers;

use App\Actions\Exception\DeleteExceptionAction;
use App\Actions\Exception\StoreExceptionAction;
use App\Actions\Exception\UpdateExceptionAction;
use App\Http\Requests\StoreExceptionRequest;
use App\Http\Requests\UpdateExceptionRequest;
use App\Http\Resources\ExceptionResource;
use App\Models\Exception;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ExceptionController extends Controller
{
    public function index(Resource $resource): JsonResponse
    {
        Gate::authorize("view", $resource->venue);
        return ExceptionResource::collection($resource->exceptions)->response();
    }

    public function store(StoreExceptionRequest $request, Resource $resource, StoreExceptionAction $action): JsonResponse
    {
        Gate::authorize('update', $resource->venue);

        $exception = $action->handle($resource, $request->validated());

        return ExceptionResource::make($exception)->response()->setStatusCode(201);
    }

    public function update(UpdateExceptionRequest $request, Exception $exception, UpdateExceptionAction $action): JsonResponse
    {
        Gate::authorize('update', $exception->resource->venue);

        $exception = $action->handle($exception, $request->validated());

        return ExceptionResource::make($exception)->response();
    }

    public function destroy(Exception $exception, DeleteExceptionAction $action): JsonResponse
    {
        Gate::authorize('update', $exception->resource->venue);

        $action->handle($exception);

        return response()->json(null, 204);
    }
}
