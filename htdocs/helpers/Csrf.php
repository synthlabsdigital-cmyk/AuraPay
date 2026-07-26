<?php
/**
 * CSRF Helper
 *
 * Cross-site request forgery token generation and verification.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Csrf
{
    public static function token(): string
    {
        Session::start();
        if (!Session::has('_csrf_token')) {
            Session::set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return (string) Session::get('_csrf_token');
    }

    public static function verify(?string $token = null): bool
    {
        Session::start();
        $sessionToken = Session::get('_csrf_token');
        $token = $token ?? ($_POST['_csrf_token'] ?? null);
        if (!$token || !$sessionToken) return false;
        return hash_equals($sessionToken, $token);
    }

    public static function check(): void
    {
        if (!self::verify()) {
            http_response_code(403);
            die('Invalid or missing CSRF token. Please try again.');
        }
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . self::token() . '">';
    }
}
