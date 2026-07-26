<?php
/**
 * Admin Configuration Page
 *
 * Manage application settings: loan parameters, credit thresholds,
 * security, and branding. Restricted to super_admin and admin roles.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Configuration';
require_once __DIR__ . '/../includes/admin_header.php';

Session::requireRole(ROLE_SUPER_ADMIN, ROLE_ADMIN);

$adminId = Session::userId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $group = $_POST['group'] ?? 'general';
    $keys = [
        'loan' => ['min_loan_amount','max_loan_amount','min_loan_term','max_loan_term','default_interest_rate','default_processing_fee','late_penalty_rate','grace_days'],
        'credit' => ['credit_min_score','credit_max_score','credit_approval_threshold'],
        'security' => ['otp_expiry_minutes','otp_max_attempts','session_lifetime','dev_otp_enabled'],
        'contact' => ['support_email','support_phone'],
        'branding' => ['app_name'],
    ];

    foreach ($keys[$group] ?? [] as $key) {
        if (isset($_POST[$key])) {
            Settings::set($key, (string) $_POST[$key], $adminId);
        }
    }

    ActivityLog::record(type: LOG_CONFIG, description: 'Configuration updated: ' . $group, adminId: $adminId, severity: LOG_SEVERITY_WARNING);
    Session::flash('success', 'Configuration saved.');
    Redirect::to('admin_config');
}

$loanSettings = Settings::byGroup('loan');
$creditSettings = Settings::byGroup('credit');
$securitySettings = Settings::byGroup('security');
$contactSettings = Settings::byGroup('contact');
$brandingSettings = Settings::byGroup('branding');

function settingValue(array $settings, string $key): string {
    foreach ($settings as $s) { if ($s['key_name'] === $key) return htmlspecialchars($s['value']); }
    return '';
}
?>

<div class="page-header">
    <?= section_label('Settings') ?>
    <h1 class="page-title">Configuration</h1>
    <p class="page-subtitle">Manage platform-wide settings.</p>
</div>

<!-- Loan settings -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Loan Parameters</h5></div>
    <div class="card-body">
        <form method="post">
            <?= Csrf::field() ?>
            <input type="hidden" name="group" value="loan">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Min amount (₱)</label><input type="number" name="min_loan_amount" class="form-control" value="<?= settingValue($loanSettings,'min_loan_amount') ?>"></div>
                <div class="col-md-3"><label class="form-label">Max amount (₱)</label><input type="number" name="max_loan_amount" class="form-control" value="<?= settingValue($loanSettings,'max_loan_amount') ?>"></div>
                <div class="col-md-3"><label class="form-label">Min term (months)</label><input type="number" name="min_loan_term" class="form-control" value="<?= settingValue($loanSettings,'min_loan_term') ?>"></div>
                <div class="col-md-3"><label class="form-label">Max term (months)</label><input type="number" name="max_loan_term" class="form-control" value="<?= settingValue($loanSettings,'max_loan_term') ?>"></div>
                <div class="col-md-3"><label class="form-label">Interest rate (%/mo)</label><input type="number" step="0.1" name="default_interest_rate" class="form-control" value="<?= settingValue($loanSettings,'default_interest_rate') ?>"></div>
                <div class="col-md-3"><label class="form-label">Processing fee (₱)</label><input type="number" name="default_processing_fee" class="form-control" value="<?= settingValue($loanSettings,'default_processing_fee') ?>"></div>
                <div class="col-md-3"><label class="form-label">Late penalty rate (%)</label><input type="number" step="0.1" name="late_penalty_rate" class="form-control" value="<?= settingValue($loanSettings,'late_penalty_rate') ?>"></div>
                <div class="col-md-3"><label class="form-label">Grace days</label><input type="number" name="grace_days" class="form-control" value="<?= settingValue($loanSettings,'grace_days') ?>"></div>
            </div>
            <button type="submit" class="btn btn-gold mt-3">Save loan settings</button>
        </form>
    </div>
</div>

<!-- Credit settings -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Credit Evaluation</h5></div>
    <div class="card-body">
        <form method="post">
            <?= Csrf::field() ?>
            <input type="hidden" name="group" value="credit">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Min score</label><input type="number" name="credit_min_score" class="form-control" value="<?= settingValue($creditSettings,'credit_min_score') ?>"></div>
                <div class="col-md-4"><label class="form-label">Max score</label><input type="number" name="credit_max_score" class="form-control" value="<?= settingValue($creditSettings,'credit_max_score') ?>"></div>
                <div class="col-md-4"><label class="form-label">Approval threshold</label><input type="number" name="credit_approval_threshold" class="form-control" value="<?= settingValue($creditSettings,'credit_approval_threshold') ?>"></div>
            </div>
            <button type="submit" class="btn btn-gold mt-3">Save credit settings</button>
        </form>
    </div>
</div>

<!-- Security settings -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Security</h5></div>
    <div class="card-body">
        <form method="post">
            <?= Csrf::field() ?>
            <input type="hidden" name="group" value="security">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">OTP expiry (min)</label><input type="number" name="otp_expiry_minutes" class="form-control" value="<?= settingValue($securitySettings,'otp_expiry_minutes') ?>"></div>
                <div class="col-md-3"><label class="form-label">Max OTP attempts</label><input type="number" name="otp_max_attempts" class="form-control" value="<?= settingValue($securitySettings,'otp_max_attempts') ?>"></div>
                <div class="col-md-3"><label class="form-label">Session lifetime (sec)</label><input type="number" name="session_lifetime" class="form-control" value="<?= settingValue($securitySettings,'session_lifetime') ?>"></div>
                <div class="col-md-3">
                    <label class="form-label">Dev OTP popup</label>
                    <select name="dev_otp_enabled" class="form-select">
                        <option value="1" <?= settingValue($securitySettings,'dev_otp_enabled')==='1'?'selected':'' ?>>Enabled</option>
                        <option value="0" <?= settingValue($securitySettings,'dev_otp_enabled')==='0'?'selected':'' ?>>Disabled</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-gold mt-3">Save security settings</button>
        </form>
    </div>
</div>

<!-- Contact & Branding -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Contact & Branding</h5></div>
    <div class="card-body">
        <form method="post">
            <?= Csrf::field() ?>
            <input type="hidden" name="group" value="contact">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Support email</label><input type="email" name="support_email" class="form-control" value="<?= settingValue($contactSettings,'support_email') ?>"></div>
                <div class="col-md-6"><label class="form-label">Support phone</label><input type="text" name="support_phone" class="form-control" value="<?= settingValue($contactSettings,'support_phone') ?>"></div>
            </div>
            <button type="submit" class="btn btn-gold mt-3">Save contact settings</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
