<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\NotificationCollection;
use App\Services\UserService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getNotifications(Request $request)
    {
        $notificationsData = $this->userService->getUserNotifications($request->user(), $request->input('per_page', 15));

        $data = new NotificationCollection(
            $notificationsData['notifications'],
            $notificationsData['unread_count']
        );

        return ApiResponse::success('Notifications retrieved successfully', $data);
    }

    public function markAsRead(Request $request)
    {

        if ($request->input('notification_id')) {
            $notification = $request->user()->notifications()->find($request->input('notification_id'));
            $notification->markAsRead();
            return ApiResponse::success('Notification marked as read');
        }
        $request->user()->unreadNotifications->markAsRead();
        return ApiResponse::success('All notifications marked as read');
    }
}
