<?php
/**
 * Install Helper
 *
 * First-run installer: creates the default admin account with a real
 * bcrypt hash, ensures required directories exist, and verifies the
 * database is reachable and seeded.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Install
{
    public static function ensureDefaultAdmin(string $email, string $password): array
    {
        $existing = Database::fetch('SELECT id FROM users WHERE email = :e AND user_type = :t', [
            ':e' => strtolower(trim($email)),
            ':t' => USER_TYPE_ADMIN,
        ]);

        if ($existing) {
            return ['success' => true, 'message' => 'Admin account already exists.', 'created' => false];
        }

        Database::insert('users', [
            'first_name'        => 'System',
            'last_name'         => 'Administrator',
            'email'             => strtolower(trim($email)),
            'password_hash'     => password_hash($password, PASSWORD_BCRYPT),
            'phone'             => '+639000000000',
            'user_type'         => USER_TYPE_ADMIN,
            'role'              => ROLE_SUPER_ADMIN,
            'status'            => USER_STATUS_ACTIVE,
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'Default admin created.', 'created' => true];
    }

    public static function ensureDirectories(): void
    {
        $dirs = [
            UPLOAD_PATH . '/government_ids',
            UPLOAD_PATH . '/selfies',
            UPLOAD_PATH . '/proof_of_income',
            UPLOAD_PATH . '/proof_of_billing',
            UPLOAD_PATH . '/temporary',
            LOG_PATH . '/application',
            LOG_PATH . '/security',
            LOG_PATH . '/errors',
            STORAGE_PATH . '/cache',
            STORAGE_PATH . '/sessions',
            STORAGE_PATH . '/temp',
        ];
        foreach ($dirs as $d) {
            if (!is_dir($d)) {
                mkdir($d, 0755, true);
            }
        }
    }

    public static function isInstalled(): bool
    {
        if (!Database::tableExists('users')) return false;
        $admin = Database::fetch('SELECT id FROM users WHERE user_type = :t LIMIT 1', [':t' => USER_TYPE_ADMIN]);
        return (bool) $admin;
    }
}
