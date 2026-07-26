<?php
/**
 * Redirect Helper
 *
 * Centralised HTTP redirection using the route map.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Redirect
{
    public static function to(string $route, array $params = []): void
    {
        $routes = require CONFIG_PATH . '/routes.php';
        $path = $routes[$route] ?? $route;
        $url = BASE_PATH . '/' . ltrim($path, '/');

        if (!empty($params)) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($params);
        }

        self::send($url);
    }

    public static function toUrl(string $url): void
    {
        self::send($url);
    }

    public static function back(): void
    {
        self::send($_SERVER['HTTP_REFERER'] ?? '/');
    }

    private static function send(string $url): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Location: ' . $url, true, 302);
        exit;
    }
}
