<?php
/**
 * Admin Applications Page
 *
 * Loan applications pending review. Admin can move to review, approve,
 * reject, or disburse.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Loan Applications';
require_once __DIR__ . '/../includes/admin_header.php';

$adminId = Session::userId();
$statusFilter = $_GET['status'] ?? 'pending';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = ADMIN_PER_PAGE;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $action = $_POST['action'] ?? '';
    $loanId = (int) ($_POST['loan_id'] ?? 0);

    if ($action === 'review') {
        $r = Loan::review($loanId, $adminId);
        Session::flash($r['success']?'success':'error', $r['message'] ?? 'Updated.');
    } elseif ($action === 'approve') {
        $notes = trim($_POST['notes'] ?? '');
        $r = Loan::approve($loanId, $adminId, $notes);
        Session::flash($r['success']?'success':'error', $r['message'] ?? 'Approved.');
    } elseif ($action === 'reject') {
        $reason = trim($_POST['reason'] ?? '');
        if (!$reason) { Session::flash('error', 'Please provide a rejection reason.'); }
        else { $r = Loan::reject($loanId, $adminId, $reason); Session::flash($r['success']?'success':'error', $r['message'] ?? 'Rejected.'); }
    } elseif ($action === 'disburse') {
        $method = $_POST['method'] ?? '';
        $ref = trim($_POST['reference'] ?? '');
        $r = Loan::disburse($loanId, $adminId, $method, $ref);
        Session::flash($r['success']?'success':'error', $r['message'] ?? 'Disbursed.');
    }
    Redirect::to('admin_apps');
}

$where = 'WHERE 1=1';
$params = [];
if ($statusFilter) {
    $where .= ' AND l.status = :s';
    $params[':s'] = $statusFilter;
}
$total = Database::count("SELECT COUNT(*) FROM loans l $where", $params);
$pagination = Util::paginate($total, $perPage, $page);
$sql = "SELECT l.*, u.first_name, u.last_name, u.email
        FROM loans l JOIN users u ON l.user_id = u.id
        $where ORDER BY l.created_at DESC LIMIT $perPage OFFSET {$pagination['offset']}";
$loans = Database::fetchAll($sql, $params);

$statuses = ['pending' => 'Pending', 'under_review' => 'Under Review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'active' => 'Active', 'completed' => 'Completed'];
?>

<div class="page-header">
    <?= section_label('Queue') ?>
    <h1 class="page-title">Loan Applications</h1>
    <p class="page-subtitle">Review and process loan applications.</p>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
            <?php foreach ($statuses as $k => $v): ?>
                <option value="<?= $k ?>" <?= $statusFilter===$k?'selected':'' ?>><?= $v ?></option>
            <?php endforeach; ?>
            <option value="" <?= $statusFilter===''?'selected':'' ?>>All</option>
        </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-ghost-gold btn-sm">Filter</button></div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Reference</th><th>Customer</th><th>Amount</th><th>Term</th><th>Status</th><th>Applied</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($loan['loan_reference']) ?></code></td>
                        <td><?= htmlspecialchars($loan['first_name'].' '.$loan['last_name']) ?></td>
                        <td><?= Util::formatMoney($loan['principal_amount']) ?></td>
                        <td><?= $loan['term_months'] ?>mo</td>
                        <td><?= Util::statusBadge($loan['status']) ?></td>
                        <td><?= Util::formatDate($loan['application_date']) ?></td>
                        <td>
                            <a href="loan_detail.php?id=<?= $loan['id'] ?>" class="btn btn-sm btn-ghost-gold">View</a>
                            <?php if ($loan['status'] === LOAN_PENDING): ?>
                            <form method="post" class="d-inline">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="action" value="review">
                                <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-ghost-gold" style="color: var(--sky); border-color: rgba(96,165,250,0.3);">Review</button>
                            </form>
                            <?php elseif (in_array($loan['status'], [LOAN_PENDING, LOAN_UNDER_REVIEW], true)): ?>
                            <button type="button" class="btn btn-sm btn-ghost-gold" style="color: var(--emerald); border-color: rgba(52,211,153,0.3);" data-bs-toggle="modal" data-bs-target="#approveModal<?= $loan['id'] ?>">Approve</button>
                            <button type="button" class="btn btn-sm btn-ghost-gold" style="color: var(--rose); border-color: rgba(251,113,133,0.3);" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $loan['id'] ?>">Reject</button>
                            <?php elseif ($loan['status'] === LOAN_APPROVED): ?>
                            <button type="button" class="btn btn-sm btn-gold" data-bs-toggle="modal" data-bs-target="#disburseModal<?= $loan['id'] ?>">Disburse</button>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Approve Modal -->
                    <?php if (in_array($loan['status'], [LOAN_PENDING, LOAN_UNDER_REVIEW], true)): ?>
                    <div class="modal fade" id="approveModal<?= $loan['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <?= Csrf::field() ?>
                                    <div class="modal-header"><h5 class="modal-title">Approve Loan <?= htmlspecialchars($loan['loan_reference']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <label class="form-label">Admin notes <small class="text-muted">(optional)</small></label>
                                        <textarea name="notes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-emerald">Approve</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="rejectModal<?= $loan['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <?= Csrf::field() ?>
                                    <div class="modal-header"><h5 class="modal-title">Reject Loan <?= htmlspecialchars($loan['loan_reference']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <label class="form-label">Rejection reason <span class="text-danger">*</span></label>
                                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Disburse Modal -->
                    <?php if ($loan['status'] === LOAN_APPROVED): ?>
                    <div class="modal fade" id="disburseModal<?= $loan['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <?= Csrf::field() ?>
                                    <div class="modal-header"><h5 class="modal-title">Disburse Loan <?= htmlspecialchars($loan['loan_reference']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <label class="form-label">Disbursement method</label>
                                        <select name="method" class="form-select" required>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="gcash">GCash</option>
                                            <option value="maya">Maya</option>
                                            <option value="cash">Cash</option>
                                            <option value="over_the_counter">Over the Counter</option>
                                        </select>
                                        <label class="form-label mt-2">Reference number <small class="text-muted">(optional)</small></label>
                                        <input type="text" name="reference" class="form-control">
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="action" value="disburse">
                                        <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-gold">Disburse</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (empty($loans)): ?>
                    <tr><td colspan="7" class="text-muted text-center py-4">No applications found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= !$pagination['has_prev']?'disabled':'' ?>"><a class="page-link" href="?status=<?= $statusFilter ?>&page=<?= $page-1 ?>">Previous</a></li>
        <?php for ($i=1; $i<=$pagination['total_pages']; $i++): ?>
            <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?status=<?= $statusFilter ?>&page=<?= $i ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <li class="page-item <?= !$pagination['has_next']?'disabled':'' ?>"><a class="page-link" href="?status=<?= $statusFilter ?>&page=<?= $page+1 ?>">Next</a></li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
