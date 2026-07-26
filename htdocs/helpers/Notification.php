<?php
/**
 * Notification Helper
 *
 * In-app notification creation and retrieval.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Notification
{
    public static function send(int $userId, string $type, string $title, string $message, ?string $link = null): int
    {
        return Database::insert('notifications', [
            'user_id' => $userId,
            'type'   => $type,
            'title'  => $title,
            'message'=> $message,
            'link'   => $link,
            'is_read'=> 0,
        ]);
    }

    public static function forUser(int $userId, int $limit = 20): array
    {
        return Database::fetchAll(
            'SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim',
            [':uid' => $userId, ':lim' => $limit]
        );
    }

    public static function unreadCount(int $userId): int
    {
        return Database::count(
            'SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND is_read = 0',
            [':uid' => $userId]
        );
    }

    public static function markRead(int $notificationId, int $userId): void
    {
        Database::update('notifications', [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ], 'id = :id AND user_id = :uid', [':id' => $notificationId, ':uid' => $userId]);
    }

    public static function markAllRead(int $userId): void
    {
        Database::update('notifications', [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ], 'user_id = :uid AND is_read = 0', [':uid' => $userId]);
    }
}
