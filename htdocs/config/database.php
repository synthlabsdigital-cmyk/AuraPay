<?php
/**
 * Database Configuration
 *
 * Central database connection settings for the Lending Platform Framework.
 * Values are loaded from environment variables with sensible defaults.
 */

declare(strict_types=1);

return [
    'host'    => getenv('DB_HOST') ?: '127.0.0.1',
    'port'    => (int)(getenv('DB_PORT') ?: 3306),
    'name'    => getenv('DB_NAME') ?: 'aurapay',
    'user'    => getenv('DB_USER') ?: 'root',
    'pass'    => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
];
