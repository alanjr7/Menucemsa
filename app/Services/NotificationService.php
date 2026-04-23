<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;

class NotificationService
{
    public static function notify(
        int $userId,
        string $type,
        string $title,
        string $message,
        string $actionUrl = '',
        array $data = []
    ): UserNotification {
        return UserNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'data' => $data,
            'read_at' => null,
        ]);
    }

    public static function notifyRole(
        string $role,
        string $type,
        string $title,
        string $message,
        string $actionUrl = '',
        array $data = []
    ): void {
        $users = User::where('role', $role)->get();

        foreach ($users as $user) {
            self::notify($user->id, $type, $title, $message, $actionUrl, $data);
        }
    }

    public static function notifyAdmins(
        string $type,
        string $title,
        string $message,
        string $actionUrl = '',
        array $data = []
    ): void {
        self::notifyRole('admin', $type, $title, $message, $actionUrl, $data);
    }

    public static function markAsRead(int $notificationId): void
    {
        $notification = UserNotification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public static function markAllAsRead(int $userId): int
    {
        return UserNotification::forUser($userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public static function getUnreadCount(int $userId): int
    {
        return UserNotification::forUser($userId)->unread()->count();
    }

    public static function getUnreadForUser(int $userId, int $limit = 20): array
    {
        return UserNotification::forUser($userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'action_url' => $n->action_url,
                'data' => $n->data,
                'created_at' => $n->created_at->diffForHumans(),
                'icon' => self::getIconForType($n->type),
                'color' => self::getColorForType($n->type),
            ])
            ->toArray();
    }

    private static function getIconForType(string $type): string
    {
        return match ($type) {
            'paciente_nuevo' => 'user-plus',
            'emergencia' => 'ambulance',
            'cirugia' => 'calendar',
            'pago' => 'credit-card',
            'stock_bajo' => 'exclamation-triangle',
            'resultados' => 'clipboard-list',
            'turno_cambiado' => 'clock',
            'cuenta_vencida' => 'dollar-sign',
            'demora' => 'alert-circle',
            'derivacion' => 'arrow-right',
            default => 'bell',
        };
    }

    private static function getColorForType(string $type): string
    {
        return match ($type) {
            'emergencia', 'demora', 'stock_bajo' => 'danger',
            'paciente_nuevo', 'turno_cambiado', 'cuenta_vencida' => 'warning',
            'pago', 'resultados', 'cirugia' => 'success',
            'derivacion' => 'info',
            default => 'info',
        };
    }
}
