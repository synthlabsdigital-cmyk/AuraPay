<?php
/**
 * Utility Helper
 *
 * General-purpose helpers: formatting, dates, IDs, etc.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Util
{
    public static function formatMoney($amount): string
    {
        $app = require CONFIG_PATH . '/app.php';
        return $app['currency_symbol'] . number_format((float) $amount, 2);
    }

    public static function formatDate(?string $date, string $format = 'M j, Y'): string
    {
        if (!$date) return '—';
        $ts = strtotime($date);
        return $ts ? date($format, $ts) : '—';
    }

    public static function formatDateTime(?string $datetime, string $format = 'M j, Y g:i A'): string
    {
        if (!$datetime) return '—';
        $ts = strtotime($datetime);
        return $ts ? date($format, $ts) : '—';
    }

    public static function timeAgo(?string $datetime): string
    {
        if (!$datetime) return '—';
        $ts = strtotime($datetime);
        if (!$ts) return '—';
        $diff = time() - $ts;
        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff / 60) . ' min ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        return date('M j, Y', $ts);
    }

    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        if (count($parts) < 2) return strtoupper(substr($name, 0, 2));
        return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
    }

    public static function truncate(string $text, int $max = 100): string
    {
        if (strlen($text) <= $max) return $text;
        return substr($text, 0, $max) . '...';
    }

    public static function slug(string $text): string
    {
        $text = preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
        $text = trim($text, '-');
        return strtolower($text);
    }

    public static function randomString(int $length = 16): string
    {
        return substr(bin2hex(random_bytes((int) ceil($length / 2))), 0, $length);
    }

    public static function statusBadge(string $status): string
    {
        $colors = [
            'pending'      => 'bg-warning-subtle text-warning-emphasis',
            'active'        => 'bg-success-subtle text-success-emphasis',
            'inactive'      => 'bg-secondary-subtle text-secondary-emphasis',
            'suspended'     => 'bg-danger-subtle text-danger-emphasis',
            'under_review'  => 'bg-info-subtle text-info-emphasis',
            'approved'      => 'bg-primary-subtle text-primary-emphasis',
            'rejected'      => 'bg-danger-subtle text-danger-emphasis',
            'disbursed'     => 'bg-success-subtle text-success-emphasis',
            'completed'    => 'bg-success-subtle text-success-emphasis',
            'defaulted'    => 'bg-danger-subtle text-danger-emphasis',
            'closed'       => 'bg-secondary-subtle text-secondary-emphasis',
            'verified'     => 'bg-success-subtle text-success-emphasis',
            'paid'         => 'bg-success-subtle text-success-emphasis',
            'overdue'      => 'bg-danger-subtle text-danger-emphasis',
            'partial'      => 'bg-warning-subtle text-warning-emphasis',
            'failed'       => 'bg-danger-subtle text-danger-emphasis',
        ];
        $cls = $colors[$status] ?? 'bg-secondary-subtle text-secondary-emphasis';
        $label = ucwords(str_replace('_', ' ', $status));
        return '<span class="badge rounded-pill ' . $cls . '">' . $label . '</span>';
    }

    public static function paginate(int $total, int $perPage, int $currentPage): array
    {
        $totalPages = max(1, (int) ceil($total / $perPage));
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $perPage;
        return [
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $currentPage,
            'total_pages'  => $totalPages,
            'offset'       => $offset,
            'has_prev'     => $currentPage > 1,
            'has_next'     => $currentPage < $totalPages,
        ];
    }
}
