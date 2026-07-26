<?php
/**
 * Settings Helper
 *
 * Key-value application configuration backed by the settings table.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Settings
{
    private static array $cache = [];

    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $row = Database::fetch('SELECT value FROM settings WHERE key_name = :k', [':k' => $key]);
        $value = $row ? $row['value'] : $default;
        self::$cache[$key] = $value;
        return $value;
    }

    public static function set(string $key, string $value, ?int $adminId = null): void
    {
        $exists = Database::fetch('SELECT id FROM settings WHERE key_name = :k', [':k' => $key]);
        if ($exists) {
            Database::update('settings', ['value' => $value, 'updated_by' => $adminId], 'key_name = :k', [':k' => $key]);
        } else {
            Database::insert('settings', [
                'key_name' => $key,
                'value' => $value,
                'updated_by' => $adminId,
            ]);
        }
        self::$cache[$key] = $value;
    }

    public static function all(): array
    {
        $rows = Database::fetchAll('SELECT key_name, value, group_name, description FROM settings ORDER BY group_name, key_name');
        $out = [];
        foreach ($rows as $r) {
            $out[$r['key_name']] = $r;
        }
        return $out;
    }

    public static function byGroup(string $group): array
    {
        return Database::fetchAll(
            'SELECT key_name, value, description FROM settings WHERE group_name = :g ORDER BY key_name',
            [':g' => $group]
        );
    }

    public static function isMaintenance(): bool
    {
        return self::get('app_status', 'active') === 'maintenance';
    }
}
