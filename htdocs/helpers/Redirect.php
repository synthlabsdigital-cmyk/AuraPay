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

        header('Location: ' . $url, true, 302);
        exit;
    }

    public static function toUrl(string $url): void
    {
        header('Location: ' . $url, true, 302);
        exit;
    }

    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer, true, 302);
        exit;
    }
}
