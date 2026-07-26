<?php
/**
 * OTP Helper
 *
 * One-time password generation and verification.
 * In development, the OTP is returned and displayed in a modal (no SMS).
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Otp
{
    public static function generate(int $userId, string $purpose = 'registration'): array
    {
        // Invalidate previous unused OTPs for this purpose
        Database::query(
            'UPDATE otp_codes SET consumed = 1 WHERE user_id = :uid AND purpose = :p AND consumed = 0',
            [':uid' => $userId, ':p' => $purpose]
        );

        $code = self::generateCode();
        $expiresAt = date('Y-m-d H:i:s', time() + (OTP_EXPIRY_MINUTES * 60));

        Database::insert('otp_codes', [
            'user_id'    => $userId,
            'code'       => $code,
            'purpose'    => $purpose,
            'attempts'   => 0,
            'expires_at' => $expiresAt,
            'consumed'   => 0,
        ]);

        return [
            'code'       => $code,
            'expires_at' => $expiresAt,
            'purpose'    => $purpose,
        ];
    }

    public static function verify(int $userId, string $code, string $purpose): array
    {
        $otp = Database::fetch(
            'SELECT * FROM otp_codes WHERE user_id = :uid AND purpose = :p AND consumed = 0 ORDER BY id DESC LIMIT 1',
            [':uid' => $userId, ':p' => $purpose]
        );

        if (!$otp) {
            return ['success' => false, 'message' => 'No OTP found. Please request a new one.'];
        }

        if (strtotime($otp['expires_at']) < time()) {
            return ['success' => false, 'message' => 'This OTP has expired. Please request a new one.'];
        }

        if ((int) $otp['attempts'] >= OTP_MAX_ATTEMPTS) {
            return ['success' => false, 'message' => 'Too many incorrect attempts. Please request a new OTP.'];
        }

        if (!hash_equals($otp['code'], $code)) {
            Database::update('otp_codes', ['attempts' => (int) $otp['attempts'] + 1], 'id = :id', [':id' => $otp['id']]);
            return ['success' => false, 'message' => 'Incorrect OTP. Please try again.'];
        }

        Database::update('otp_codes', [
            'consumed' => 1,
            'verified_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $otp['id']]);

        return ['success' => true];
    }

    public static function resend(int $userId, string $purpose): array
    {
        return self::generate($userId, $purpose);
    }

    private static function generateCode(): string
    {
        $code = '';
        for ($i = 0; $i < OTP_LENGTH; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }
}
