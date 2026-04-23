<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = Auth::id();
        $notifications = NotificationService::getUnreadForUser($userId, 20);
        $count = NotificationService::getUnreadCount($userId);

        return response()->json([
            'notifications' => $notifications,
            'total' => $count,
        ]);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $userId = Auth::id();
        $notification = \App\Models\UserNotification::forUser($userId)->find($id);

        if (!$notification) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'total' => NotificationService::getUnreadCount($userId),
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        $userId = Auth::id();
        $marked = NotificationService::markAllAsRead($userId);

        return response()->json([
            'success' => true,
            'marked' => $marked,
            'total' => 0,
        ]);
    }
}
