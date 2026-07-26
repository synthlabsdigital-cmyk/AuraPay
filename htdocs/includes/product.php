<?php
/**
 * Product configuration accessor.
 * Provides global access to the active product branding/theme.
 */

declare(strict_types=1);

function product(): array
{
    static $product = null;
    if ($product === null) {
        $product = require CONFIG_PATH . '/product.php';
    }
    return $product;
}

function theme(string $key, $default = null)
{
    $p = product();
    return $p['theme'][$key] ?? $default;
}

function section_label(string $label): string
{
    return '<div class="section-label"><span class="ornament"></span><span>' . htmlspecialchars($label) . '</span><span class="ornament" style="transform: rotate(180deg);"></span></div>';
}

function page_header_block(string $title, string $subtitle, string $label = ''): string
{
    $html = '<div class="page-header">';
    if ($label) $html .= section_label($label);
    $html .= '<h1 class="page-title">' . htmlspecialchars($title) . '</h1>';
    $html .= '<p class="page-subtitle">' . htmlspecialchars($subtitle) . '</p>';
    $html .= '</div>';
    return $html;
}
