<?php
/**
 * Admin Credit Evaluations Page
 *
 * Lists all credit evaluations across customers.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Credit Evaluations';
require_once __DIR__ . '/../includes/admin_header.php';

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = ADMIN_PER_PAGE;
$total = Database::count("SELECT COUNT(*) FROM credit_evaluations");
$pagination = Util::paginate($total, $perPage, $page);
$evals = Database::fetchAll(
    "SELECT ce.*, u.first_name, u.last_name, u.email
     FROM credit_evaluations ce JOIN users u ON ce.user_id = u.id
     ORDER BY ce.created_at DESC LIMIT $perPage OFFSET {$pagination['offset']}"
);
?>

<div class="page-header">
    <?= section_label('Risk') ?>
    <h1 class="page-title">Credit Evaluations</h1>
    <p class="page-subtitle">All credit evaluations across the platform.</p>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Customer</th><th>Score</th><th>Rating</th><th>Risk</th><th>Recommended</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($evals as $ev): ?>
                    <tr>
                        <td><?= htmlspecialchars($ev['first_name'].' '.$ev['last_name']) ?><br><small class="text-muted"><?= htmlspecialchars($ev['email']) ?></small></td>
                        <td><?= $ev['score'] ?></td>
                        <td><?= htmlspecialchars($ev['rating']) ?></td>
                        <td><span class="badge <?= $ev['risk_level']==='low'?'badge-emerald':($ev['risk_level']==='medium'?'badge-amber':'badge-rose') ?>"><?= ucfirst($ev['risk_level']) ?></span></td>
                        <td><?= Util::formatMoney($ev['recommended_amount']) ?> / <?= $ev['recommended_term'] ?>mo</td>
                        <td><?= Util::statusBadge($ev['status']) ?></td>
                        <td><?= Util::formatDate($ev['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($evals)): ?><tr><td colspan="7" class="text-muted text-center py-4">No evaluations found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= !$pagination['has_prev']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page-1 ?>">Previous</a></li>
        <?php for ($i=1;$i<=$pagination['total_pages'];$i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li><?php endfor; ?>
        <li class="page-item <?= !$pagination['has_next']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page+1 ?>">Next</a></li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
