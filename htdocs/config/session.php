<?php
/**
 * Session Configuration
 *
 * Secure session settings for the Lending Platform Framework.
 */

declare(strict_types=1);

return [
    'name'           => 'AURAPAY_SESS',
    'lifetime'        => 7200,
    'path'            => '/',
    'domain'          => '',
    'secure'          => false,
    'httponly'        => true,
    'samesite'        => 'Lax',
    'cookie_prefix'   => 'aurapay_',
    'regenerate'       => true,
    'regenerate_interval' => 1800,
];
