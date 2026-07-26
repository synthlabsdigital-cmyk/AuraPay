<?php
/**
 * Transaction Helper
 *
 * Transaction reference generation and lookup helpers.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Transaction
{
    public static function generateReference(): string
    {
        return 'TXN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public static function get(int $transactionId): ?array
    {
        return Database::fetch('SELECT * FROM transactions WHERE id = :id', [':id' => $transactionId]);
    }

    public static function forUser(int $userId, int $limit = 50, int $offset = 0): array
    {
        return Database::fetchAll(
            'SELECT * FROM transactions WHERE user_id = :uid ORDER BY transaction_date DESC LIMIT :lim OFFSET :off',
            [':uid' => $userId, ':lim' => $limit, ':off' => $offset]
        );
    }

    public static function forLoan(int $loanId): array
    {
        return Database::fetchAll(
            'SELECT * FROM transactions WHERE loan_id = :lid ORDER BY transaction_date ASC',
            [':lid' => $loanId]
        );
    }

    public static function countForUser(int $userId): int
    {
        return Database::count('SELECT COUNT(*) FROM transactions WHERE user_id = :uid', [':uid' => $userId]);
    }

    public static function totalDisbursed(): float
    {
        $row = Database::fetch("SELECT COALESCE(SUM(amount),0) AS total FROM transactions WHERE type = :t AND status = :s", [
            ':t' => TX_DISBURSEMENT, ':s' => TX_STATUS_COMPLETED,
        ]);
        return (float) ($row['total'] ?? 0);
    }

    public static function totalRepaid(): float
    {
        $row = Database::fetch("SELECT COALESCE(SUM(amount),0) AS total FROM transactions WHERE type = :t AND status = :s", [
            ':t' => TX_REPAYMENT, ':s' => TX_STATUS_COMPLETED,
        ]);
        return (float) ($row['total'] ?? 0);
    }
}
