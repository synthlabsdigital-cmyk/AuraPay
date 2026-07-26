<?php
/**
 * Session Helper
 *
 * Secure session management with role-based access control helpers.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $cfg = require CONFIG_PATH . '/session.php';

        session_name($cfg['name']);
        session_set_cookie_params([
            'lifetime' => $cfg['lifetime'],
            'path'     => $cfg['path'],
            'domain'   => $cfg['domain'],
            'secure'   => $cfg['secure'],
            'httponly' => $cfg['httponly'],
            'samesite' => $cfg['samesite'],
        ]);

        session_start();
        self::$started = true;

        // Regenerate periodically to prevent fixation
        if ($cfg['regenerate'] && !isset($_SESSION['_last_regenerate'])) {
            self::regenerate();
        } elseif ($cfg['regenerate']) {
            $elapsed = time() - (int) $_SESSION['_last_regenerate'];
            if ($elapsed > $cfg['regenerate_interval']) {
                self::regenerate();
            }
        }
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
        $_SESSION['_last_regenerate'] = time();
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        self::$started = false;
    }

    // ---------------------------------------------------------------
    // Auth helpers
    // ---------------------------------------------------------------

    public static function login(array $user): void
    {
        self::start();
        self::regenerate();
        $_SESSION['user_id']    = (int) $user['id'];
        $_SESSION['user_type']  = $user['user_type'];
        $_SESSION['user_name']  = trim($user['first_name'] . ' ' . $user['last_name']);
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'] ?? null;
        $_SESSION['_last_regenerate'] = time();
    }

    public static function isLoggedIn(): bool
    {
        self::start();
        return isset($_SESSION['user_id']);
    }

    public static function isCustomer(): bool
    {
        return self::isLoggedIn() && self::get('user_type') === USER_TYPE_CUSTOMER;
    }

    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && self::get('user_type') === USER_TYPE_ADMIN;
    }

    public static function userId(): ?int
    {
        return self::isLoggedIn() ? (int) self::get('user_id') : null;
    }

    public static function userName(): string
    {
        return (string) self::get('user_name', '');
    }

    public static function userEmail(): string
    {
        return (string) self::get('user_email', '');
    }

    public static function userRole(): ?string
    {
        return self::get('user_role');
    }

    public static function requireCustomer(): void
    {
        if (!self::isCustomer()) {
            Redirect::to('login');
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            Redirect::to('admin_login');
        }
    }

    public static function requireRole(string ...$roles): void
    {
        self::requireAdmin();
        $role = self::userRole();
        if (!in_array($role, $roles, true)) {
            Redirect::to('admin_dashboard');
        }
    }

    public static function flash(string $key, $value = null)
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }
}
