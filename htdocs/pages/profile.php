<?php
/**
 * Profile Page
 *
 * Personal information, employment, financial details, and identity.
 * Supports both GET (display) and POST (update).
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'My Profile';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();
$profile = Database::fetch('SELECT * FROM user_profiles WHERE user_id = :uid', [':uid' => $userId]);
$user = Database::fetch('SELECT * FROM users WHERE id = :id', [':id' => $userId]);

if (!$profile) {
    Database::insert('user_profiles', ['user_id' => $userId]);
    $profile = Database::fetch('SELECT * FROM user_profiles WHERE user_id = :uid', [':uid' => $userId]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();

    $data = [
        'date_of_birth'       => ($_POST['date_of_birth'] ?? '') ?: null,
        'gender'              => ($_POST['gender'] ?? '') ?: null,
        'nationality'         => ($_POST['nationality'] ?? '') ?: 'Filipino',
        'civil_status'        => ($_POST['civil_status'] ?? '') ?: null,
        'present_address'     => ($_POST['present_address'] ?? '') ?: null,
        'permanent_address'   => ($_POST['permanent_address'] ?? '') ?: null,
        'region'              => ($_POST['region'] ?? '') ?: null,
        'province'            => ($_POST['province'] ?? '') ?: null,
        'city'                => ($_POST['city'] ?? '') ?: null,
        'barangay'            => ($_POST['barangay'] ?? '') ?: null,
        'postal_code'         => ($_POST['postal_code'] ?? '') ?: null,
        'employment_status'   => ($_POST['employment_status'] ?? '') ?: null,
        'employer'            => ($_POST['employer'] ?? '') ?: null,
        'job_title'           => ($_POST['job_title'] ?? '') ?: null,
        'monthly_income'      => (($_POST['monthly_income'] ?? '') !== '') ? (float) $_POST['monthly_income'] : null,
        'years_employed'      => (($_POST['years_employed'] ?? '') !== '') ? (float) $_POST['years_employed'] : null,
        'business_name'       => ($_POST['business_name'] ?? '') ?: null,
        'business_type'       => ($_POST['business_type'] ?? '') ?: null,
        'source_of_funds'     => ($_POST['source_of_funds'] ?? '') ?: null,
        'bank_name'           => ($_POST['bank_name'] ?? '') ?: null,
        'bank_account_number' => ($_POST['bank_account_number'] ?? '') ?: null,
        'bank_account_name'   => ($_POST['bank_account_name'] ?? '') ?: null,
        'ewallet_provider'    => ($_POST['ewallet_provider'] ?? '') ?: null,
        'ewallet_number'      => ($_POST['ewallet_number'] ?? '') ?: null,
        'id_type'             => ($_POST['id_type'] ?? '') ?: null,
        'id_number'           => ($_POST['id_number'] ?? '') ?: null,
        'id_issue_date'       => ($_POST['id_issue_date'] ?? '') ?: null,
        'id_expiry_date'      => ($_POST['id_expiry_date'] ?? '') ?: null,
        'mothers_maiden_name' => ($_POST['mothers_maiden_name'] ?? '') ?: null,
        'emergency_contact_name'     => ($_POST['emergency_contact_name'] ?? '') ?: null,
        'emergency_contact_phone'    => ($_POST['emergency_contact_phone'] ?? '') ?: null,
        'emergency_contact_relation'=> ($_POST['emergency_contact_relation'] ?? '') ?: null,
    ];

    // Check completion
    $required = ['date_of_birth','gender','civil_status','present_address','region','province','city','barangay','postal_code','employment_status','monthly_income','id_type','id_number'];
    $allFilled = true;
    foreach ($required as $r) {
        if (empty($data[$r])) { $allFilled = false; break; }
    }
    $data['profile_completed'] = $allFilled ? 1 : 0;
    if ($allFilled) {
        $data['completed_at'] = date('Y-m-d H:i:s');
    }

    Database::update('user_profiles', $data, 'user_id = :uid', [':uid' => $userId]);

    ActivityLog::record(
        type: LOG_UPDATE,
        description: 'Profile updated by user ID ' . $userId,
        userId: $userId,
        severity: LOG_SEVERITY_INFO
    );

    Session::flash('success', 'Your profile has been saved.');
    Redirect::to('profile');
}
?>

<div class="page-header">
    <?= section_label('Identity') ?>
    <h1 class="page-title">My Profile</h1>
    <p class="page-subtitle">Keep your details current for faster loan processing.</p>
</div>

<form method="post" novalidate>
    <?= Csrf::field() ?>

    <!-- Personal -->
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Personal Information</h5></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">First name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($profile['date_of_birth'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">—</option>
                        <option value="male" <?= ($profile['gender'] ?? '')==='male'?'selected':'' ?>>Male</option>
                        <option value="female" <?= ($profile['gender'] ?? '')==='female'?'selected':'' ?>>Female</option>
                        <option value="other" <?= ($profile['gender'] ?? '')==='other'?'selected':'' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Civil status</label>
                    <select name="civil_status" class="form-select">
                        <option value="">—</option>
                        <option value="single" <?= ($profile['civil_status'] ?? '')==='single'?'selected':'' ?>>Single</option>
                        <option value="married" <?= ($profile['civil_status'] ?? '')==='married'?'selected':'' ?>>Married</option>
                        <option value="divorced" <?= ($profile['civil_status'] ?? '')==='divorced'?'selected':'' ?>>Divorced</option>
                        <option value="widowed" <?= ($profile['civil_status'] ?? '')==='widowed'?'selected':'' ?>>Widowed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nationality</label>
                    <input type="text" name="nationality" class="form-control" value="<?= htmlspecialchars($profile['nationality'] ?? 'Filipino') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mother's maiden name</label>
                    <input type="text" name="mothers_maiden_name" class="form-control" value="<?= htmlspecialchars($profile['mothers_maiden_name'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Address -->
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Address</h5></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Present address</label>
                    <textarea name="present_address" class="form-control" rows="2"><?= htmlspecialchars($profile['present_address'] ?? '') ?></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Permanent address <small class="text-muted">(if different)</small></label>
                    <textarea name="permanent_address" class="form-control" rows="2"><?= htmlspecialchars($profile['permanent_address'] ?? '') ?></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Region</label>
                    <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($profile['region'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Province</label>
                    <input type="text" name="province" class="form-control" value="<?= htmlspecialchars($profile['province'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($profile['city'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Barangay</label>
                    <input type="text" name="barangay" class="form-control" value="<?= htmlspecialchars($profile['barangay'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Postal code</label>
                    <input type="text" name="postal_code" class="form-control" value="<?= htmlspecialchars($profile['postal_code'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Employment -->
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Employment & Income</h5></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employment status</label>
                    <select name="employment_status" class="form-select">
                        <option value="">—</option>
                        <option value="employed" <?= ($profile['employment_status'] ?? '')==='employed'?'selected':'' ?>>Employed</option>
                        <option value="self_employed" <?= ($profile['employment_status'] ?? '')==='self_employed'?'selected':'' ?>>Self-employed</option>
                        <option value="unemployed" <?= ($profile['employment_status'] ?? '')==='unemployed'?'selected':'' ?>>Unemployed</option>
                        <option value="retired" <?= ($profile['employment_status'] ?? '')==='retired'?'selected':'' ?>>Retired</option>
                        <option value="student" <?= ($profile['employment_status'] ?? '')==='student'?'selected':'' ?>>Student</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Employer</label>
                    <input type="text" name="employer" class="form-control" value="<?= htmlspecialchars($profile['employer'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Job title</label>
                    <input type="text" name="job_title" class="form-control" value="<?= htmlspecialchars($profile['job_title'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Monthly income (₱)</label>
                    <input type="number" name="monthly_income" class="form-control" step="0.01" value="<?= htmlspecialchars($profile['monthly_income'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Years employed</label>
                    <input type="number" name="years_employed" class="form-control" step="0.1" value="<?= htmlspecialchars($profile['years_employed'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Business name</label>
                    <input type="text" name="business_name" class="form-control" value="<?= htmlspecialchars($profile['business_name'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Business type</label>
                    <input type="text" name="business_type" class="form-control" value="<?= htmlspecialchars($profile['business_type'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Source of funds</label>
                    <input type="text" name="source_of_funds" class="form-control" value="<?= htmlspecialchars($profile['source_of_funds'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Financial -->
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Financial Details</h5></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Bank name</label>
                    <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($profile['bank_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Account number</label>
                    <input type="text" name="bank_account_number" class="form-control" value="<?= htmlspecialchars($profile['bank_account_number'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Account name</label>
                    <input type="text" name="bank_account_name" class="form-control" value="<?= htmlspecialchars($profile['bank_account_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">E-wallet provider</label>
                    <input type="text" name="ewallet_provider" class="form-control" placeholder="GCash, Maya..." value="<?= htmlspecialchars($profile['ewallet_provider'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">E-wallet number</label>
                    <input type="text" name="ewallet_number" class="form-control" value="<?= htmlspecialchars($profile['ewallet_number'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Identity -->
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Government ID</h5></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">ID type</label>
                    <select name="id_type" class="form-select">
                        <option value="">—</option>
                        <option value="passport" <?= ($profile['id_type'] ?? '')==='passport'?'selected':'' ?>>Passport</option>
                        <option value="drivers_license" <?= ($profile['id_type'] ?? '')==='drivers_license'?'selected':'' ?>>Driver's License</option>
                        <option value="sss" <?= ($profile['id_type'] ?? '')==='sss'?'selected':'' ?>>SSS ID</option>
                        <option value="umid" <?= ($profile['id_type'] ?? '')==='umid'?'selected':'' ?>>UMID</option>
                        <option value="philsys" <?= ($profile['id_type'] ?? '')==='philsys'?'selected':'' ?>>PhilSys ID</option>
                        <option value="voters_id" <?= ($profile['id_type'] ?? '')==='voters_id'?'selected':'' ?>>Voter's ID</option>
                        <option value="postal_id" <?= ($profile['id_type'] ?? '')==='postal_id'?'selected':'' ?>>Postal ID</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ID number</label>
                    <input type="text" name="id_number" class="form-control" value="<?= htmlspecialchars($profile['id_number'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Issue date</label>
                    <input type="date" name="id_issue_date" class="form-control" value="<?= htmlspecialchars($profile['id_issue_date'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Expiry date</label>
                    <input type="date" name="id_expiry_date" class="form-control" value="<?= htmlspecialchars($profile['id_expiry_date'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency contact -->
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Emergency Contact</h5></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Name</label>
                    <input type="text" name="emergency_contact_name" class="form-control" value="<?= htmlspecialchars($profile['emergency_contact_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="emergency_contact_phone" class="form-control" value="<?= htmlspecialchars($profile['emergency_contact_phone'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Relationship</label>
                    <input type="text" name="emergency_contact_relation" class="form-control" value="<?= htmlspecialchars($profile['emergency_contact_relation'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-gold">Save profile</button>
        <a href="dashboard.php" class="btn btn-ghost">Cancel</a>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
