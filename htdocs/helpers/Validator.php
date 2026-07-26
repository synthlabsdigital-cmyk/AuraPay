<?php
/**
 * Validator Helper
 *
 * Centralised input validation routines.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Validator
{
    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function phone(string $value): bool
    {
        $value = preg_replace('/[\s\-\(\)]/', '', $value);
        return (bool) preg_match('/^(\+63|0)?9\d{9}$/', $value);
    }

    public static function password(string $value): bool
    {
        return strlen($value) >= 8
            && preg_match('/[A-Za-z]/', $value)
            && preg_match('/[0-9]/', $value);
    }

    public static function required(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }
        return $errors;
    }

    public static function minLength(string $value, int $min): bool
    {
        return strlen(trim($value)) >= $min;
    }

    public static function maxLength(string $value, int $max): bool
    {
        return strlen(trim($value)) <= $max;
    }

    public static function numeric($value): bool
    {
        return is_numeric($value);
    }

    public static function min($value, float $min): bool
    {
        return (float) $value >= $min;
    }

    public static function max($value, float $max): bool
    {
        return (float) $value <= $max;
    }

    public static function range($value, float $min, float $max): bool
    {
        $v = (float) $value;
        return $v >= $min && $v <= $max;
    }

    public static function date(string $value): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $value);
        return $d && $d->format('Y-m-d') === $value;
    }

    public static function ageAtLeast(string $dob, int $minAge): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$d) return false;
        $now = new DateTime();
        $age = $d->diff($now)->y;
        return $age >= $minAge;
    }

    public static function inList($value, array $list): bool
    {
        return in_array($value, $list, true);
    }

    public static function fileSize(int $bytes, int $maxBytes): bool
    {
        return $bytes <= $maxBytes;
    }

    public static function fileExtension(string $filename, string $allowedCsv): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = array_map('trim', explode(',', strtolower($allowedCsv)));
        return in_array($ext, $allowed, true);
    }

    public static function fileType(string $tmpName, array $allowedMimes): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpName);
        finfo_close($finfo);
        return in_array($mime, $allowedMimes, true);
    }

    public static function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public static function decimal($value): bool
    {
        return (bool) preg_match('/^\d+(\.\d{1,2})?$/', (string) $value);
    }
}
