<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Notification\DeleteUserNotificationAction;
use App\Actions\Notification\GetUserNotificationsAction;
use App\Actions\Notification\MarkNotificationAsReadAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeNotificationController extends Controller
{
    public function index(Request $request, GetUserNotificationsAction $getUserNotificationsAction): JsonResponse
    {
        $notifications = $getUserNotificationsAction->handle($request->user());

        return NotificationResource::collection($notifications)->response();
    }

    public function read(Request $request, Notification $notification, MarkNotificationAsReadAction $markNotificationAsReadAction): JsonResponse
    {
        $notification = $markNotificationAsReadAction->handle($request->user(), $notification);

        return response()->json(NotificationResource::make($notification));
    }

    public function destroy(Request $request, Notification $notification, DeleteUserNotificationAction $deleteUserNotificationAction): JsonResponse
    {
        $deleteUserNotificationAction->handle($request->user(), $notification);

        return response()->json(null, 204);
    }
}
