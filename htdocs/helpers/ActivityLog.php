<?php
/**
 * Activity Log Helper
 *
 * Audit trail recording for admin and system actions.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class ActivityLog
{
    public static function record(
        string $type,
        string $description,
        ?int $adminId = null,
        ?int $userId = null,
        string $severity = LOG_SEVERITY_INFO,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $metadata = null
    ): int {
        return Database::insert('activity_logs', [
            'admin_id'    => $adminId,
            'user_id'     => $userId,
            'type'        => $type,
            'severity'    => $severity,
            'description'=> $description,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'ip_address'  => self::ip(),
            'user_agent'  => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'metadata'    => $metadata ? json_encode($metadata) : null,
        ]);
    }

    public static function all(int $limit = 50, int $offset = 0, ?string $type = null, ?string $severity = null): array
    {
        $sql = 'SELECT a.*, u.first_name AS admin_first, u.last_name AS admin_last
                FROM activity_logs a
                LEFT JOIN users u ON a.admin_id = u.id
                WHERE 1=1';
        $params = [];
        if ($type) {
            $sql .= ' AND a.type = :t';
            $params[':t'] = $type;
        }
        if ($severity) {
            $sql .= ' AND a.severity = :s';
            $params[':s'] = $severity;
        }
        $sql .= ' ORDER BY a.created_at DESC LIMIT :lim OFFSET :off';
        $params[':lim'] = $limit;
        $params[':off'] = $offset;
        return Database::fetchAll($sql, $params);
    }

    public static function forUser(int $userId, int $limit = 50): array
    {
        return Database::fetchAll(
            'SELECT * FROM activity_logs WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim',
            [':uid' => $userId, ':lim' => $limit]
        );
    }

    public static function forAdmin(int $adminId, int $limit = 50): array
    {
        return Database::fetchAll(
            'SELECT * FROM activity_logs WHERE admin_id = :aid ORDER BY created_at DESC LIMIT :lim',
            [':aid' => $adminId, ':lim' => $limit]
        );
    }

    public static function count(?string $type = null, ?string $severity = null): int
    {
        $sql = 'SELECT COUNT(*) FROM activity_logs WHERE 1=1';
        $params = [];
        if ($type) { $sql .= ' AND type = :t'; $params[':t'] = $type; }
        if ($severity) { $sql .= ' AND severity = :s'; $params[':s'] = $severity; }
        return Database::count($sql, $params);
    }

    private static function ip(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP', 'REMOTE_ADDR',
        ];
        foreach ($headers as $h) {
            if (!empty($_SERVER[$h])) {
                $ip = trim(explode(',', $_SERVER[$h])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return '0.0.0.0';
    }
}
