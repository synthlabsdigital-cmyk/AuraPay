<?php
/**
 * Loan Helper
 *
 * Loan application, approval, rejection, disbursal, amortization generation,
 * repayment processing, and status transitions.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Loan
{
    public static function apply(int $userId, int $creditEvalId, float $amount, int $termMonths, ?string $purpose = null): array
    {
        $product = require CONFIG_PATH . '/product.php';
        $loan = $product['loan'];

        if ($amount < $loan['min_amount'] || $amount > $loan['max_amount']) {
            return ['success' => false, 'message' => 'Loan amount is out of range.'];
        }
        if ($termMonths < $loan['min_term'] || $termMonths > $loan['max_term']) {
            return ['success' => false, 'message' => 'Loan term is out of range.'];
        }

        // Check for existing active loan
        $active = Database::fetch(
            'SELECT id FROM loans WHERE user_id = :uid AND status IN (:s1,:s2,:s3,:s4)',
            [':uid' => $userId, ':s1' => LOAN_PENDING, ':s2' => LOAN_UNDER_REVIEW, ':s3' => LOAN_APPROVED, ':s4' => LOAN_ACTIVE]
        );
        if ($active) {
            return ['success' => false, 'message' => 'You already have an active or pending loan.'];
        }

        $interestRate = (float) $loan['default_interest_rate'];
        $processingFee = (float) $loan['default_processing_fee'];

        $totalInterest = round($amount * ($interestRate / 100) * $termMonths, 2);
        $totalPayable = round($amount + $totalInterest + $processingFee, 2);
        $monthlyPayment = round($totalPayable / $termMonths, 2);

        $reference = self::generateReference();

        $loanId = Database::insert('loans', [
            'loan_reference'       => $reference,
            'user_id'              => $userId,
            'credit_evaluation_id' => $creditEvalId,
            'principal_amount'     => $amount,
            'interest_rate'        => $interestRate,
            'term_months'          => $termMonths,
            'processing_fee'       => $processingFee,
            'total_interest'       => $totalInterest,
            'total_payable'        => $totalPayable,
            'monthly_payment'      => $monthlyPayment,
            'outstanding_balance'  => $totalPayable,
            'amount_paid'          => 0,
            'status'               => LOAN_PENDING,
            'purpose'              => $purpose,
            'application_date'     => date('Y-m-d H:i:s'),
        ]);

        ActivityLog::record(
            type: LOG_CREATE,
            description: 'Loan application submitted: ' . $reference . ' (₱' . number_format($amount, 2) . ')',
            userId: $userId,
            severity: LOG_SEVERITY_INFO
        );

        Notification::send($userId, NOTIF_LOAN, 'Loan application received', 'Your loan application ' . $reference . ' has been received and is pending review.', 'loan_history');

        return ['success' => true, 'loan_id' => $loanId, 'reference' => $reference];
    }

    public static function review(int $loanId, int $adminId): array
    {
        $loan = self::get($loanId);
        if (!$loan) return ['success' => false, 'message' => 'Loan not found.'];
        if ($loan['status'] !== LOAN_PENDING) {
            return ['success' => false, 'message' => 'Only pending loans can be moved to review.'];
        }

        Database::update('loans', [
            'status' => LOAN_UNDER_REVIEW,
            'reviewed_by' => $adminId,
        ], 'id = :id', [':id' => $loanId]);

        ActivityLog::record(
            type: LOG_STATUS_CHANGE,
            description: 'Loan ' . $loan['loan_reference'] . ' moved to under review',
            adminId: $adminId,
            userId: (int) $loan['user_id'],
            severity: LOG_SEVERITY_INFO
        );

        Notification::send((int) $loan['user_id'], NOTIF_LOAN, 'Loan under review', 'Your loan application ' . $loan['loan_reference'] . ' is now under review.', 'loan_history');

        return ['success' => true];
    }

    public static function approve(int $loanId, int $adminId, ?string $notes = null): array
    {
        $loan = self::get($loanId);
        if (!$loan) return ['success' => false, 'message' => 'Loan not found.'];
        if (!in_array($loan['status'], [LOAN_PENDING, LOAN_UNDER_REVIEW], true)) {
            return ['success' => false, 'message' => 'Only pending or under-review loans can be approved.'];
        }

        Database::update('loans', [
            'status' => LOAN_APPROVED,
            'approval_date' => date('Y-m-d H:i:s'),
            'approved_by' => $adminId,
            'admin_notes' => $notes,
        ], 'id = :id', [':id' => $loanId]);

        ActivityLog::record(
            type: LOG_APPROVE,
            description: 'Loan ' . $loan['loan_reference'] . ' approved',
            adminId: $adminId,
            userId: (int) $loan['user_id'],
            severity: LOG_SEVERITY_INFO
        );

        Notification::send((int) $loan['user_id'], NOTIF_SUCCESS, 'Loan approved!', 'Your loan application ' . $loan['loan_reference'] . ' has been approved. Disbursement will follow.', 'loan_history');

        return ['success' => true];
    }

    public static function reject(int $loanId, int $adminId, string $reason): array
    {
        $loan = self::get($loanId);
        if (!$loan) return ['success' => false, 'message' => 'Loan not found.'];
        if (in_array($loan['status'], [LOAN_DISBURSED, LOAN_ACTIVE, LOAN_COMPLETED], true)) {
            return ['success' => false, 'message' => 'Cannot reject a loan that has been disbursed.'];
        }

        Database::update('loans', [
            'status' => LOAN_REJECTED,
            'rejection_date' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason,
            'reviewed_by' => $adminId,
        ], 'id = :id', [':id' => $loanId]);

        ActivityLog::record(
            type: LOG_REJECT,
            description: 'Loan ' . $loan['loan_reference'] . ' rejected: ' . $reason,
            adminId: $adminId,
            userId: (int) $loan['user_id'],
            severity: LOG_SEVERITY_WARNING
        );

        Notification::send((int) $loan['user_id'], NOTIF_ERROR, 'Loan application rejected', 'Your loan application ' . $loan['loan_reference'] . ' was rejected. Reason: ' . $reason, 'loan_history');

        return ['success' => true];
    }

    public static function disburse(int $loanId, int $adminId, string $method, ?string $reference = null): array
    {
        $loan = self::get($loanId);
        if (!$loan) return ['success' => false, 'message' => 'Loan not found.'];
        if ($loan['status'] !== LOAN_APPROVED) {
            return ['success' => false, 'message' => 'Only approved loans can be disbursed.'];
        }

        $disbursementDate = date('Y-m-d H:i:s');
        $firstPaymentDate = date('Y-m-d', strtotime('+1 month'));
        $maturityDate = date('Y-m-d', strtotime('+' . $loan['term_months'] . ' months'));

        Database::transaction(function (PDO $pdo) use ($loanId, $adminId, $method, $reference, $disbursementDate, $firstPaymentDate, $maturityDate, $loan) {
            Database::update('loans', [
                'status' => LOAN_ACTIVE,
                'disbursement_date' => $disbursementDate,
                'disbursement_method' => $method,
                'disbursement_reference' => $reference,
                'first_payment_date' => $firstPaymentDate,
                'maturity_date' => $maturityDate,
                'disbursed_by' => $adminId,
            ], 'id = :id', [':id' => $loanId]);

            // Generate amortization schedule
            self::generateAmortization($loanId, (float) $loan['total_payable'], (int) $loan['term_months'], $firstPaymentDate);

            // Record disbursement transaction
            Database::insert('transactions', [
                'transaction_reference' => Transaction::generateReference(),
                'loan_id' => $loanId,
                'user_id' => (int) $loan['user_id'],
                'type' => TX_DISBURSEMENT,
                'amount' => (float) $loan['principal_amount'],
                'payment_method' => $method,
                'payment_reference' => $reference,
                'status' => TX_STATUS_COMPLETED,
                'description' => 'Loan disbursement for ' . $loan['loan_reference'],
                'processed_by' => $adminId,
                'transaction_date' => $disbursementDate,
            ]);
        });

        ActivityLog::record(
            type: LOG_DISBURSE,
            description: 'Loan ' . $loan['loan_reference'] . ' disbursed via ' . $method,
            adminId: $adminId,
            userId: (int) $loan['user_id'],
            severity: LOG_SEVERITY_INFO
        );

        Notification::send((int) $loan['user_id'], NOTIF_SUCCESS, 'Loan disbursed!', 'Your loan ' . $loan['loan_reference'] . ' has been disbursed. First payment is due on ' . $firstPaymentDate . '.', 'payments');

        return ['success' => true, 'first_payment_date' => $firstPaymentDate];
    }

    public static function generateAmortization(int $loanId, float $totalPayable, int $termMonths, string $firstPaymentDate): void
    {
        $monthlyPayment = round($totalPayable / $termMonths, 2);
        $interestComponent = round($monthlyPayment * 0.4, 2); // approximate split
        $principalComponent = round($monthlyPayment - $interestComponent, 2);
        $balance = $totalPayable;

        for ($i = 1; $i <= $termMonths; $i++) {
            $dueDate = date('Y-m-d', strtotime($firstPaymentDate . ' +' . ($i - 1) . ' months'));
            $balance = round($balance - $principalComponent, 2);
            if ($i === $termMonths) {
                $principalComponent = round($principalComponent + $balance, 2);
                $balance = 0;
            }
            Database::insert('loan_amortization', [
                'loan_id' => $loanId,
                'installment_number' => $i,
                'due_date' => $dueDate,
                'principal_component' => $principalComponent,
                'interest_component' => $interestComponent,
                'installment_amount' => $monthlyPayment,
                'balance_after' => $balance,
                'status' => 'pending',
                'paid_amount' => 0,
                'penalty_amount' => 0,
            ]);
        }
    }

    public static function processPayment(int $loanId, float $amount, string $method, ?string $reference = null, ?int $adminId = null): array
    {
        $loan = self::get($loanId);
        if (!$loan) return ['success' => false, 'message' => 'Loan not found.'];
        if (!in_array($loan['status'], [LOAN_ACTIVE, LOAN_DEFAULTED], true)) {
            return ['success' => false, 'message' => 'Payments can only be made on active loans.'];
        }

        $newAmountPaid = (float) $loan['amount_paid'] + $amount;
        $newOutstanding = max(0, (float) $loan['outstanding_balance'] - $amount);

        Database::transaction(function (PDO $pdo) use ($loanId, $amount, $method, $reference, $adminId, $newAmountPaid, $newOutstanding, $loan) {
            Database::update('loans', [
                'amount_paid' => $newAmountPaid,
                'outstanding_balance' => $newOutstanding,
            ], 'id = :id', [':id' => $loanId]);

            // Mark amortization installments as paid
            $installments = Database::fetchAll(
                'SELECT * FROM loan_amortization WHERE loan_id = :lid AND status IN (:s1,:s2) ORDER BY installment_number',
                [':lid' => $loanId, ':s1' => 'pending', ':s2' => 'partial']
            );

            $remaining = $amount;
            foreach ($installments as $inst) {
                if ($remaining <= 0) break;
                $due = (float) $inst['installment_amount'] + (float) $inst['penalty_amount'];
                $alreadyPaid = (float) $inst['paid_amount'];
                $owing = $due - $alreadyPaid;
                if ($owing <= 0) continue;

                $pay = min($remaining, $owing);
                $newPaid = $alreadyPaid + $pay;
                $status = $newPaid >= $due ? 'paid' : 'partial';
                Database::update('loan_amortization', [
                    'paid_amount' => $newPaid,
                    'paid_date' => date('Y-m-d'),
                    'status' => $status,
                ], 'id = :id', [':id' => $inst['id']]);
                $remaining -= $pay;
            }

            // Record repayment transaction
            Database::insert('transactions', [
                'transaction_reference' => Transaction::generateReference(),
                'loan_id' => $loanId,
                'user_id' => (int) $loan['user_id'],
                'type' => TX_REPAYMENT,
                'amount' => $amount,
                'payment_method' => $method,
                'payment_reference' => $reference,
                'status' => TX_STATUS_COMPLETED,
                'description' => 'Repayment for ' . $loan['loan_reference'],
                'processed_by' => $adminId,
                'transaction_date' => date('Y-m-d H:i:s'),
            ]);
        });

        // Check if loan is fully paid
        if ($newOutstanding <= 0) {
            Database::update('loans', [
                'status' => LOAN_COMPLETED,
                'completed_date' => date('Y-m-d'),
            ], 'id = :id', [':id' => $loanId]);

            ActivityLog::record(
                type: LOG_STATUS_CHANGE,
                description: 'Loan ' . $loan['loan_reference'] . ' completed (fully paid)',
                userId: (int) $loan['user_id'],
                severity: LOG_SEVERITY_INFO
            );

            Notification::send((int) $loan['user_id'], NOTIF_SUCCESS, 'Loan fully paid!', 'Congratulations! Your loan ' . $loan['loan_reference'] . ' is now fully paid.', 'loan_history');
        } else {
            Notification::send((int) $loan['user_id'], NOTIF_PAYMENT, 'Payment received', 'We received your payment of ₱' . number_format($amount, 2) . ' for loan ' . $loan['loan_reference'] . '.', 'payments');
        }

        ActivityLog::record(
            type: LOG_PAYMENT,
            description: 'Payment of ₱' . number_format($amount, 2) . ' for loan ' . $loan['loan_reference'],
            userId: (int) $loan['user_id'],
            severity: LOG_SEVERITY_INFO
        );

        return ['success' => true, 'outstanding' => $newOutstanding];
    }

    public static function applyPenalties(): int
    {
        $product = require CONFIG_PATH . '/product.php';
        $penaltyRate = (float) $product['loan']['late_penalty_rate'];
        $graceDays = (int) $product['loan']['grace_days'];

        $overdue = Database::fetchAll(
            'SELECT * FROM loan_amortization WHERE status = :s AND due_date < :d',
            [':s' => 'pending', ':d' => date('Y-m-d', strtotime("-$graceDays days"))]
        );

        $count = 0;
        foreach ($overdue as $inst) {
            $daysLate = (time() - strtotime($inst['due_date'])) / 86400;
            if ($daysLate <= $graceDays) continue;

            $penalty = round((float) $inst['installment_amount'] * ($penaltyRate / 100), 2);
            Database::update('loan_amortization', [
                'penalty_amount' => $penalty,
                'status' => 'overdue',
            ], 'id = :id', [':id' => $inst['id']]);
            $count++;
        }

        if ($count > 0) {
            ActivityLog::record(
                type: LOG_STATUS_CHANGE,
                description: "Applied late penalties to $count overdue installments",
                severity: LOG_SEVERITY_WARNING
            );
        }

        return $count;
    }

    public static function get(int $loanId): ?array
    {
        return Database::fetch('SELECT * FROM loans WHERE id = :id', [':id' => $loanId]);
    }

    public static function getByReference(string $reference): ?array
    {
        return Database::fetch('SELECT * FROM loans WHERE loan_reference = :r', [':r' => $reference]);
    }

    public static function getAmortization(int $loanId): array
    {
        return Database::fetchAll(
            'SELECT * FROM loan_amortization WHERE loan_id = :lid ORDER BY installment_number',
            [':lid' => $loanId]
        );
    }

    public static function generateReference(): string
    {
        return 'AP-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }
}
