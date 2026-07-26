<?php
/**
 * File Helper
 *
 * Secure file upload handling with validation and storage.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class File
{
    private const MIME_MAP = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'pdf'  => 'application/pdf',
    ];

    public static function upload(array $file, string $subDir, string $allowedTypes = ALLOWED_DOC_TYPES): array
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload failed. Please try again.'];
        }

        if (!Validator::fileSize((int) $file['size'], MAX_UPLOAD_SIZE)) {
            return ['success' => false, 'message' => 'File is too large. Maximum size is 5MB.'];
        }

        if (!Validator::fileExtension($file['name'], $allowedTypes)) {
            return ['success' => false, 'message' => 'File type not allowed. Accepted: JPG, PNG, PDF.'];
        }

        $allowedMimes = array_values(array_intersect_key(self::MIME_MAP, array_flip(explode(',', $allowedTypes))));
        if (!Validator::fileType($file['tmp_name'], $allowedMimes)) {
            return ['success' => false, 'message' => 'File content does not match its extension.'];
        }

        $destDir = UPLOAD_PATH . '/' . trim($subDir, '/');
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
        $destPath = $destDir . '/' . $filename;
        $relPath = 'uploads/' . trim($subDir, '/') . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['success' => false, 'message' => 'Could not save the uploaded file.'];
        }

        return [
            'success'   => true,
            'file_name' => $filename,
            'file_path' => $relPath,
            'file_size' => (int) $file['size'],
            'mime_type' => self::MIME_MAP[$ext] ?? mime_content_type($destPath),
        ];
    }

    public static function delete(string $relativePath): bool
    {
        $abs = ROOT_PATH . '/' . ltrim($relativePath, '/');
        if (is_file($abs)) {
            return unlink($abs);
        }
        return false;
    }

    public static function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = (float) $bytes;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 1) . ' ' . $units[$i];
    }
}
