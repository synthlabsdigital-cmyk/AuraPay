<?php
/**
 * Documents Page
 *
 * Upload and manage required documents (government ID, proof of income,
 * proof of billing, selfie). Shows verification status.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'My Documents';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $action = $_POST['action'] ?? 'upload';

    if ($action === 'upload' && isset($_FILES['document'])) {
        $type = $_POST['document_type'] ?? '';
        $allowed = ['government_id','proof_of_income','proof_of_billing','selfie','supporting_document'];
        if (!in_array($type, $allowed, true)) {
            Session::flash('error', 'Invalid document type.');
        } else {
            $result = File::upload($_FILES['document'], $type === 'government_id' ? 'government_ids' : ($type === 'selfie' ? 'selfies' : $type . 's'));
            if ($result['success']) {
                Database::insert('documents', [
                    'user_id'       => $userId,
                    'document_type' => $type,
                    'file_name'     => $result['file_name'],
                    'file_path'     => $result['file_path'],
                    'file_size'     => $result['file_size'],
                    'mime_type'     => $result['mime_type'],
                    'status'        => DOC_STATUS_PENDING,
                ]);
                ActivityLog::record(type: LOG_CREATE, description: 'Document uploaded: ' . $type, userId: $userId, severity: LOG_SEVERITY_INFO);
                Notification::send($userId, NOTIF_DOCUMENT, 'Document uploaded', 'Your ' . str_replace('_', ' ', $type) . ' has been uploaded and is pending verification.', 'documents');
                Session::flash('success', 'Document uploaded successfully.');
            } else {
                Session::flash('error', $result['message']);
            }
        }
    } elseif ($action === 'delete') {
        $docId = (int) ($_POST['doc_id'] ?? 0);
        $doc = Database::fetch('SELECT * FROM documents WHERE id = :id AND user_id = :uid', [':id' => $docId, ':uid' => $userId]);
        if ($doc && $doc['status'] !== DOC_STATUS_VERIFIED) {
            File::delete($doc['file_path']);
            Database::delete('documents', 'id = :id AND user_id = :uid', [':id' => $docId, ':uid' => $userId]);
            Session::flash('success', 'Document deleted.');
        } else {
            Session::flash('error', 'Cannot delete a verified document.');
        }
    }
    Redirect::to('documents');
}

$docs = Database::fetchAll('SELECT * FROM documents WHERE user_id = :uid ORDER BY created_at DESC', [':uid' => $userId]);
$docTypes = [
    'government_id'    => 'Government ID',
    'proof_of_income'  => 'Proof of Income',
    'proof_of_billing' => 'Proof of Billing',
    'selfie'           => 'Selfie / Selfie with ID',
];
$byType = [];
foreach ($docTypes as $key => $label) {
    $byType[$key] = array_filter($docs, fn($d) => $d['document_type'] === $key);
}
?>

<div class="page-header">
    <?= section_label('Verification') ?>
    <h1 class="page-title">My Documents</h1>
    <p class="page-subtitle">Upload required documents. Accepted: JPG, PNG, PDF (max 5MB).</p>
</div>

<!-- Upload form -->
<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Upload a document</h5></div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" novalidate>
            <?= Csrf::field() ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Document type</label>
                    <select name="document_type" class="form-select" required>
                        <option value="">— Select —</option>
                        <?php foreach ($docTypes as $k => $l): ?>
                            <option value="<?= $k ?>"><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">File</label>
                    <input type="file" name="document" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-gold w-100">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Document grid -->
<div class="row g-3">
    <?php foreach ($docTypes as $typeKey => $typeLabel): ?>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><?= $typeLabel ?></h6>
                    <span class="text-muted small"><?= count($byType[$typeKey]) ?> file(s)</span>
                </div>
                <div class="card-body">
                    <?php if (empty($byType[$typeKey])): ?>
                        <p class="text-muted small mb-0">No document uploaded.</p>
                    <?php else: foreach ($byType[$typeKey] as $doc): ?>
                        <div class="doc-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark fs-4" style="color: <?= $doc['mime_type']==='application/pdf'?'var(--rose)':'var(--sky)' ?>"></i>
                                <div>
                                    <div class="small fw-semibold text-truncate" style="max-width:200px"><?= htmlspecialchars($doc['file_name']) ?></div>
                                    <div class="text-muted small"><?= File::humanSize((int)$doc['file_size']) ?> &middot; <?= Util::formatDate($doc['created_at']) ?></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <?= Util::statusBadge($doc['status']) ?>
                                <?php if ($doc['status'] !== DOC_STATUS_VERIFIED): ?>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this document?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($doc['status'] === DOC_STATUS_REJECTED && $doc['rejection_reason']): ?>
                            <div class="small text-danger mt-1">Reason: <?= htmlspecialchars($doc['rejection_reason']) ?></div>
                        <?php endif; ?>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
