<?php
/**
 * Application Configuration
 *
 * Global application-level settings for AuraPay.
 * Product branding is loaded separately from the Product Layer (config/product.php).
 */
declare(strict_types=1);

return [
    'name'              => 'AuraPay',
    'env'               => 'production',
    'debug'             => false,
    'url'               => 'http://localhost',
    'timezone'          => 'Asia/Manila',
    'product_key'       => 'aurapay',
    'currency'          => 'PHP',
    'currency_symbol'   => '₱',
    'country'           => 'Philippines',
    'session_name'      => 'AURAPAY_SESS',
    'session_lifetime'  => 7200,
    'company'           => 'AuraPay Lending Inc.',
    'support_email'     => 'support@aurapay.ph',
    'support_phone'     => '+63 2 8888 8888',
    'business_hours'    => 'Mon - Fri, 8:00 AM - 5:00 PM (PHT)',
];
