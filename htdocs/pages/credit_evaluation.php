<?php
/**
 * Credit Evaluation Page
 *
 * Runs the credit evaluation engine and displays the score, rating,
 * breakdown, and recommendations.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Credit Evaluation';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();
$latestEval = Database::fetch('SELECT * FROM credit_evaluations WHERE user_id = :uid ORDER BY id DESC LIMIT 1', [':uid' => $userId]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $result = Credit::evaluate($userId);
    if ($result['success']) {
        Session::flash('success', 'Your credit evaluation has been completed.');
        Redirect::to('credit');
    } else {
        Session::flash('error', $result['message'] ?? 'Evaluation failed.');
    }
}

$product = product();
$minScore = $product['credit']['min_score'];
$maxScore = $product['credit']['max_score'];
$threshold = $product['credit']['approval_threshold'];
$scorePct = $latestEval ? min(100, max(0, (($latestEval['score'] - $minScore) / ($maxScore - $minScore)) * 100)) : 0;
?>

<div class="page-header">
    <?= section_label('Assessment') ?>
    <h1 class="page-title">Credit Evaluation</h1>
    <p class="page-subtitle">Our system assesses your creditworthiness based on your profile.</p>
</div>

<?php if (!$latestEval): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-graph-up-arrow" style="font-size:3rem;color:var(--gold)"></i>
            <h3 class="mt-3">No evaluation yet</h3>
            <p class="text-muted">Run a credit evaluation to see your score and loan eligibility.</p>
            <form method="post">
                <?= Csrf::field() ?>
                <button type="submit" class="btn btn-gold">Run credit evaluation</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <!-- Score gauge -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Your Credit Score</h5></div>
                <div class="card-body text-center py-4">
                    <div class="score-ring" style="--pct:<?= $scorePct ?>%">
                        <div class="score-inner">
                            <span class="score-number"><?= $latestEval['score'] ?></span>
                            <span class="score-rating"><?= htmlspecialchars($latestEval['rating']) ?></span>
                        </div>
                    </div>
                    <p class="text-muted mt-3">Range: <?= $minScore ?> - <?= $maxScore ?></p>
                    <span class="badge <?= $latestEval['score'] >= $threshold ? 'badge-emerald' : 'badge-rose' ?>">
                        <?= $latestEval['score'] >= $threshold ? 'Eligible for loans' : 'Below approval threshold' ?>
                    </span>
                    <div class="mt-3">
                        <form method="post">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn btn-ghost-gold btn-sm">Re-evaluate</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Score Breakdown</h5></div>
                <div class="card-body">
                    <?php
                    $factors = [
                        ['Employment', 'employment_score', $product['credit']['weights']['employment']],
                        ['Income', 'income_score', $product['credit']['weights']['income']],
                        ['Documents', 'documents_score', $product['credit']['weights']['documents']],
                        ['Identity', 'identity_score', $product['credit']['weights']['identity']],
                        ['Repayment History', 'history_score', $product['credit']['weights']['history']],
                    ];
                    foreach ($factors as $f):
                        $val = (int) $latestEval[$f[1]];
                    ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span><?= $f[0] ?> <span class="text-muted">(weight <?= $f[2] ?>%)</span></span>
                                <span class="fw-semibold" style="color: var(--gold-bright);"><?= $val ?>/100</span>
                            </div>
                            <div class="progress" style="height:6px">
                                <div class="progress-bar" style="width:<?= $val ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="gold-divider my-4"></div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="small text-muted">Recommended amount</div>
                            <div class="fw-semibold"><?= $latestEval['recommended_amount'] > 0 ? Util::formatMoney($latestEval['recommended_amount']) : '—' ?></div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Recommended term</div>
                            <div class="fw-semibold"><?= $latestEval['recommended_term'] > 0 ? $latestEval['recommended_term'] . ' months' : '—' ?></div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Risk level</div>
                            <div class="fw-semibold text-capitalize"><?= htmlspecialchars($latestEval['risk_level']) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Evaluated on</div>
                            <div class="fw-semibold"><?= Util::formatDateTime($latestEval['evaluated_at']) ?></div>
                        </div>
                    </div>
                    <?php if ($latestEval['remarks']): ?>
                        <div class="alert alert-info small mt-3 mb-0"><?= htmlspecialchars($latestEval['remarks']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
