<?php
/**
 * Application Configuration
 *
 * Global application-level settings for the Lending Platform Framework.
 * Product branding is loaded separately from the Product Layer.
 */

declare(strict_types=1);

return [
    'name'              => getenv('APP_NAME') ?: 'AuraPay',
    'env'               => getenv('APP_ENV') ?: 'production',
    'debug'             => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
    'url'               => rtrim(getenv('APP_URL') ?: 'http://localhost', '/'),
    'timezone'          => 'UTC',
    'product_key'       => 'aurapay',
    'currency'          => 'PHP',
    'currency_symbol'   => '₱',
    'country'           => 'Philippines',
    'session_name'      => 'AURAPAY_SESS',
    'session_lifetime'  => (int)(getenv('SESSION_LIFETIME') ?: 7200),
    'company'           => 'AuraPay Lending Inc.',
    'support_email'     => getenv('SUPPORT_EMAIL') ?: 'support@aurapay.ph',
    'support_phone'     => getenv('SUPPORT_PHONE') ?: '+63 2 8888 8888',
    'business_hours'    => 'Mon - Fri, 8:00 AM - 5:00 PM (PHT)',
];
