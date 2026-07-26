<?php
/**
 * Hash Helper
 *
 * Password hashing and verification utilities.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Hash
{
    public static function make(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT);
    }

    public static function token(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
