<?php
/**
 * Navigation helper functions.
 */

declare(strict_types=1);

function activeNav(string $page): string
{
    $script = $_SERVER['PHP_SELF'] ?? '';
    return str_contains($script, $page) ? 'active' : '';
}

function activeAdminNav(string $page): string
{
    $script = basename($_SERVER['PHP_SELF'] ?? '');
    return $script === $page ? 'active' : '';
}
