<?php
/**
 * Credit Helper
 *
 * Credit evaluation engine — computes a score from profile completeness,
 * employment, income, documents, identity, and history.
 *
 * @package AuraPay\Helpers
 */

declare(strict_types=1);

final class Credit
{
    public static function evaluate(int $userId): array
    {
        $profile = Database::fetch('SELECT * FROM user_profiles WHERE user_id = :uid', [':uid' => $userId]);
        $user    = Database::fetch('SELECT * FROM users WHERE id = :uid', [':uid' => $userId]);
        $docs    = Database::fetchAll(
            'SELECT document_type, status FROM documents WHERE user_id = :uid',
            [':uid' => $userId]
        );

        if (!$profile || !$user) {
            return ['success' => false, 'message' => 'Profile not found.'];
        }

        $product = require CONFIG_PATH . '/product.php';
        $weights = $product['credit']['weights'];

        // --- Employment score (0-100) ---
        $employmentScore = 0;
        if (!empty($profile['employment_status'])) {
            $employmentScore += 30;
            if (in_array($profile['employment_status'], ['employed', 'self_employed'], true)) {
                $employmentScore += 30;
            }
            if (!empty($profile['employer']) || !empty($profile['business_name'])) {
                $employmentScore += 20;
            }
            if (!empty($profile['job_title'])) {
                $employmentScore += 10;
            }
            if ((float) ($profile['years_employed'] ?? 0) >= 1) {
                $employmentScore += 10;
            }
        }

        // --- Income score (0-100) ---
        $incomeScore = 0;
        $income = (float) ($profile['monthly_income'] ?? 0);
        if ($income > 0) {
            $incomeScore += 20;
            if ($income >= 10000)  $incomeScore += 20;
            if ($income >= 25000)  $incomeScore += 20;
            if ($income >= 50000)  $incomeScore += 20;
            if (!empty($profile['source_of_funds'])) $incomeScore += 20;
        }

        // --- Documents score (0-100) ---
        $docsScore = 0;
        $required = ['government_id', 'proof_of_income', 'proof_of_billing', 'selfie'];
        $docMap = [];
        foreach ($docs as $d) {
            $docMap[$d['document_type']] = $d['status'];
        }
        foreach ($required as $req) {
            if (isset($docMap[$req])) {
                $docsScore += 20;
                if ($docMap[$req] === DOC_STATUS_VERIFIED) {
                    $docsScore += 5;
                }
            }
        }

        // --- Identity score (0-100) ---
        $identityScore = 0;
        if (!empty($profile['id_type']))      $identityScore += 25;
        if (!empty($profile['id_number']))     $identityScore += 25;
        if (!empty($profile['date_of_birth'])) $identityScore += 20;
        if (!empty($profile['mothers_maiden_name'])) $identityScore += 15;
        if (!empty($user['email_verified_at'])) $identityScore += 15;

        // --- History score (0-100) ---
        $historyScore = self::historyScore($userId);

        // Weighted total (0-100)
        $weighted = (
            ($employmentScore * $weights['employment']) +
            ($incomeScore * $weights['income']) +
            ($docsScore * $weights['documents']) +
            ($identityScore * $weights['identity']) +
            ($historyScore * $weights['history'])
        ) / 100;

        // Map to credit score range (300-850)
        $min = $product['credit']['min_score'];
        $max = $product['credit']['max_score'];
        $score = (int) round($min + ($weighted / 100) * ($max - $min));
        $score = max($min, min($max, $score));

        // Rating
        $rating = self::rating($score);

        // Risk level
        $risk = $score >= 700 ? 'low' : ($score >= 580 ? 'medium' : 'high');

        // Recommended amount and term
        $income = (float) ($profile['monthly_income'] ?? 0);
        $recommendedAmount = self::recommendedAmount($score, $income, $product);
        $recommendedTerm = self::recommendedTerm($score, $product);

        $remarks = self::remarks($score, $employmentScore, $incomeScore, $docsScore, $identityScore, $historyScore);

        $evalId = Database::insert('credit_evaluations', [
            'user_id'           => $userId,
            'score'             => $score,
            'rating'            => $rating,
            'employment_score'  => $employmentScore,
            'income_score'      => $incomeScore,
            'documents_score'   => $docsScore,
            'identity_score'    => $identityScore,
            'history_score'     => $historyScore,
            'recommended_amount' => $recommendedAmount,
            'recommended_term'  => $recommendedTerm,
            'risk_level'        => $risk,
            'remarks'           => $remarks,
            'status'            => CREDIT_COMPLETED,
            'evaluated_at'      => date('Y-m-d H:i:s'),
        ]);

        ActivityLog::record(
            type: LOG_CREATE,
            description: 'Credit evaluation completed for user ID ' . $userId . ' (score: ' . $score . ')',
            userId: $userId,
            severity: LOG_SEVERITY_INFO
        );

        return [
            'success' => true,
            'evaluation_id' => $evalId,
            'score' => $score,
            'rating' => $rating,
            'risk_level' => $risk,
            'employment_score' => $employmentScore,
            'income_score' => $incomeScore,
            'documents_score' => $docsScore,
            'identity_score' => $identityScore,
            'history_score' => $historyScore,
            'recommended_amount' => $recommendedAmount,
            'recommended_term' => $recommendedTerm,
            'remarks' => $remarks,
        ];
    }

    private static function historyScore(int $userId): int
    {
        $loans = Database::fetchAll(
            'SELECT status, amount_paid, total_payable FROM loans WHERE user_id = :uid',
            [':uid' => $userId]
        );

        if (empty($loans)) {
            return 50; // neutral for first-time borrowers
        }

        $score = 0;
        $completed = 0;
        $total = count($loans);
        foreach ($loans as $loan) {
            if ($loan['status'] === LOAN_COMPLETED) $completed++;
        }
        $completionRate = $total > 0 ? ($completed / $total) * 100 : 0;
        $score = (int) $completionRate;
        return min(100, max(0, $score));
    }

    private static function rating(int $score): string
    {
        if ($score >= 800) return 'Excellent';
        if ($score >= 740) return 'Very Good';
        if ($score >= 670) return 'Good';
        if ($score >= 580) return 'Fair';
        return 'Poor';
    }

    private static function recommendedAmount(int $score, float $income, array $product): float
    {
        $min = $product['loan']['min_amount'];
        $max = $product['loan']['max_amount'];

        if ($score < 580) return 0;
        if ($income <= 0) return 0;

        // Base on score percentage
        $scorePct = ($score - 580) / (850 - 580);
        $scorePct = max(0, min(1, $scorePct));

        // Cap at 6x monthly income
        $incomeCap = $income * 6;
        $amount = $min + ($max - $min) * $scorePct;
        $amount = min($amount, $incomeCap, $max);
        $amount = max($amount, $min);

        return round($amount, 2);
    }

    private static function recommendedTerm(int $score, array $product): int
    {
        if ($score >= 700) return 12;
        if ($score >= 640) return 9;
        if ($score >= 580) return 6;
        return 3;
    }

    private static function remarks(int $score, int $emp, int $inc, int $doc, int $iden, int $hist): string
    {
        $parts = [];
        $parts[] = "Credit score: {$score}.";
        if ($emp < 50)  $parts[] = 'Employment details need improvement.';
        if ($inc < 50)  $parts[] = 'Income verification could be stronger.';
        if ($doc < 80)  $parts[] = 'Some required documents are missing or unverified.';
        if ($iden < 80) $parts[] = 'Identity verification incomplete.';
        if ($hist < 50) $parts[] = 'Limited or poor repayment history.';
        if ($score >= 700) $parts[] = 'Strong overall profile.';
        return implode(' ', $parts);
    }
}
