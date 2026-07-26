<?php
/**
 * Auth Helper
 *
 * Authentication, registration, OTP, and password helpers.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Auth
{
    public static function attempt(string $email, string $password, string $expectedType = USER_TYPE_CUSTOMER): array
    {
        $user = Database::fetch(
            'SELECT * FROM users WHERE email = :email LIMIT 1',
            [':email' => $email]
        );

        if (!$user) {
            return ['success' => false, 'message' => 'No account found with that email.'];
        }

        if ($user['user_type'] !== $expectedType) {
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        if ($user['status'] === USER_STATUS_SUSPENDED) {
            return ['success' => false, 'message' => 'Your account has been suspended. Contact support.'];
        }

        if ($user['status'] === USER_STATUS_INACTIVE) {
            return ['success' => false, 'message' => 'Your account is inactive. Contact support.'];
        }

        // Lockout check
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
            return ['success' => false, 'message' => "Too many failed attempts. Try again in {$remaining} minute(s)."];
        }

        if (!password_verify($password, $user['password_hash'])) {
            self::registerFailedLogin((int) $user['id']);
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        // Admin must be active and verified
        if ($expectedType === USER_TYPE_ADMIN && $user['status'] !== USER_STATUS_ACTIVE) {
            return ['success' => false, 'message' => 'Your admin account is not active.'];
        }

        // Customer must have verified email
        if ($expectedType === USER_TYPE_CUSTOMER && !$user['email_verified_at']) {
            return ['success' => false, 'message' => 'Please verify your email address first.'];
        }

        // Success
        Database::update('users', [
            'last_login_at' => date('Y-m-d H:i:s'),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ], 'id = :id', [':id' => $user['id']]);

        Session::login($user);

        ActivityLog::record(
            type: LOG_LOGIN,
            description: ($expectedType === USER_TYPE_ADMIN ? 'Admin' : 'Customer') . ' logged in: ' . $user['email'],
            userId: (int) $user['id'],
            severity: LOG_SEVERITY_INFO
        );

        return ['success' => true, 'user' => $user];
    }

    private static function registerFailedLogin(int $userId): void
    {
        $user = Database::fetch('SELECT failed_login_attempts FROM users WHERE id = :id', [':id' => $userId]);
        if (!$user) return;

        $attempts = (int) $user['failed_login_attempts'] + 1;
        $data = ['failed_login_attempts' => $attempts];

        if ($attempts >= 5) {
            $data['locked_until'] = date('Y-m-d H:i:s', time() + 900); // 15 min
        }

        Database::update('users', $data, 'id = :id', [':id' => $userId]);
    }

    public static function register(array $data): array
    {
        $email = strtolower(trim($data['email'] ?? ''));

        if (!Validator::email($email)) {
            return ['success' => false, 'message' => 'Please enter a valid email address.'];
        }

        $exists = Database::fetch('SELECT id FROM users WHERE email = :email', [':email' => $email]);
        if ($exists) {
            return ['success' => false, 'message' => 'An account with that email already exists.'];
        }

        if (!Validator::phone($data['phone'] ?? '')) {
            return ['success' => false, 'message' => 'Please enter a valid phone number.'];
        }

        if (!Validator::password($data['password'] ?? '')) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters with letters and numbers.'];
        }

        if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
            return ['success' => false, 'message' => 'Passwords do not match.'];
        }

        $userId = Database::insert('users', [
            'first_name'     => ucfirst(trim($data['first_name'])),
            'middle_name'    => !empty($data['middle_name']) ? ucfirst(trim($data['middle_name'])) : null,
            'last_name'      => ucfirst(trim($data['last_name'])),
            'email'          => $email,
            'password_hash'  => password_hash($data['password'], PASSWORD_BCRYPT),
            'phone'          => trim($data['phone']),
            'user_type'      => USER_TYPE_CUSTOMER,
            'status'         => USER_STATUS_PENDING,
        ]);

        Database::insert('user_profiles', ['user_id' => $userId]);

        $otp = Otp::generate($userId, 'registration');

        ActivityLog::record(
            type: LOG_CREATE,
            description: 'New customer registered: ' . $email,
            userId: $userId,
            severity: LOG_SEVERITY_INFO
        );

        return ['success' => true, 'user_id' => $userId, 'otp' => $otp];
    }

    public static function verifyEmail(int $userId, string $code): array
    {
        $otp = Database::fetch(
            'SELECT * FROM otp_codes WHERE user_id = :uid AND purpose = :p AND consumed = 0 ORDER BY id DESC LIMIT 1',
            [':uid' => $userId, ':p' => 'registration']
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

        Database::update('users', [
            'status' => USER_STATUS_ACTIVE,
            'email_verified_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $userId]);

        ActivityLog::record(
            type: LOG_SECURITY,
            description: 'Email verified for user ID ' . $userId,
            userId: $userId,
            severity: LOG_SEVERITY_INFO
        );

        return ['success' => true];
    }

    public static function changePassword(int $userId, string $current, string $new): array
    {
        $user = Database::fetch('SELECT password_hash FROM users WHERE id = :id', [':id' => $userId]);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if (!password_verify($current, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        }

        if (!Validator::password($new)) {
            return ['success' => false, 'message' => 'New password must be at least 8 characters with letters and numbers.'];
        }

        Database::update('users', [
            'password_hash' => password_hash($new, PASSWORD_BCRYPT),
        ], 'id = :id', [':id' => $userId]);

        ActivityLog::record(
            type: LOG_SECURITY,
            description: 'Password changed for user ID ' . $userId,
            userId: $userId,
            severity: LOG_SEVERITY_INFO
        );

        return ['success' => true];
    }

    public static function initiatePasswordReset(string $email): array
    {
        $user = Database::fetch('SELECT * FROM users WHERE email = :e AND user_type = :t', [
            ':e' => strtolower(trim($email)),
            ':t' => USER_TYPE_CUSTOMER,
        ]);

        if (!$user) {
            // Don't reveal whether the email exists
            return ['success' => true, 'otp' => null];
        }

        $otp = Otp::generate((int) $user['id'], 'password_reset');

        return ['success' => true, 'otp' => $otp, 'user_id' => (int) $user['id']];
    }

    public static function resetPassword(int $userId, string $code, string $newPassword): array
    {
        $otp = Database::fetch(
            'SELECT * FROM otp_codes WHERE user_id = :uid AND purpose = :p AND consumed = 0 ORDER BY id DESC LIMIT 1',
            [':uid' => $userId, ':p' => 'password_reset']
        );

        if (!$otp) {
            return ['success' => false, 'message' => 'No reset code found. Please request a new one.'];
        }

        if (strtotime($otp['expires_at']) < time()) {
            return ['success' => false, 'message' => 'This reset code has expired.'];
        }

        if (!hash_equals($otp['code'], $code)) {
            Database::update('otp_codes', ['attempts' => (int) $otp['attempts'] + 1], 'id = :id', [':id' => $otp['id']]);
            return ['success' => false, 'message' => 'Incorrect reset code.'];
        }

        if (!Validator::password($newPassword)) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters with letters and numbers.'];
        }

        Database::update('otp_codes', [
            'consumed' => 1,
            'verified_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $otp['id']]);

        Database::update('users', [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT),
        ], 'id = :id', [':id' => $userId]);

        ActivityLog::record(
            type: LOG_SECURITY,
            description: 'Password reset for user ID ' . $userId,
            userId: $userId,
            severity: LOG_SEVERITY_WARNING
        );

        return ['success' => true];
    }
}
