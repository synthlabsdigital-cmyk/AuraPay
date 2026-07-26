<?php
/**
 * Error Helper
 *
 * Centralised error and exception logging.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class ErrorHelper
{
    public static function log(string $channel, string $message, ?string $context = null): void
    {
        $dir = LOG_PATH . '/' . $channel;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $dir . '/' . date('Y-m-d') . '.log';
        $entry = sprintf(
            "[%s] %s%s%s",
            date('Y-m-d H:i:s'),
            $message,
            $context ? " | Context: " . $context : '',
            PHP_EOL
        );
        file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
    }

    public static function exception(Throwable $e): void
    {
        self::log('errors', $e->getMessage(), $e->getTraceAsString());
    }
}
