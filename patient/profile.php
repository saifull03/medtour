<?php
/**
 * Patient Profile Management
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/validation.php';

requirePatient();
$patientId = currentPatientId();
$user = currentUser();
$pdo = getDbConnection();

// Handle Profile Update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();

    $name       = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $country    = trim($_POST['country'] ?? '');
    $passportNo = trim($_POST['passport_no'] ?? '');
    $newPass    = $_POST['new_password'] ?? '';
    $confPass   = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }

    if (empty($email) || !isValidEmail($email)) {
        $errors['email'] = 'Valid email address is required.';
    } elseif (isEmailTaken($email, $user['id'])) {
        $errors['email'] = 'This email address is already in use by another account.';
    }

    if (!empty($passportNo) && !isValidPassport($passportNo)) {
        $errors['passport_no'] = 'Passport number must be 5 to 20 alphanumeric characters.';
    }

    if (!empty($newPass)) {
        if (!isStrongPassword($newPass)) {
            $errors['new_password'] = 'New password must be at least 8 characters with letters & numbers.';
        } elseif ($newPass !== $confPass) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }
    }

    if (!empty($errors)) {
        setFormFlash($_POST, $errors);
        setFlash('error', 'Please resolve the errors below.');
        header('Location: ' . url('patient/profile.php'));
        exit();
    }

    try {
        $pdo->beginTransaction();

        if (!empty($newPass)) {
            $stmtU = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmtU->execute([$name, $email, password_hash($newPass, PASSWORD_DEFAULT), $user['id']]);
        } else {
            $stmtU = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmtU->execute([$name, $email, $user['id']]);
        }

        $stmtP = $pdo->prepare("UPDATE patients SET phone = ?, country = ?, passport_no = ? WHERE id = ?");
        $stmtP->execute([$phone ?: null, $country ?: null, strtoupper($passportNo) ?: null, $patientId]);

        $pdo->commit();

        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;

        appLog('PROFILE_UPDATED', ['user_id' => $user['id']]);
        setFlash('success', 'Profile information updated successfully.');
        header('Location: ' . url('patient/profile.php'));
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        appLog('PROFILE_UPDATE_ERROR', ['error' => $e->getMessage()]);
        setFlash('error', 'Database error updating profile.');
        header('Location: ' . url('patient/profile.php'));
        exit();
    }
}

// Fetch current patient details
$stmt = $pdo->prepare("SELECT u.name, u.email, p.phone, p.country, p.passport_no FROM users u JOIN patients p ON u.id = p.user_id WHERE p.id = ?");
$stmt->execute([$patientId]);
$profile = $stmt->fetch();

$pageTitle = 'Patient Profile';
$heroTitle = 'Personal Profile & Travel Information';
$heroSubtitle = 'Keep your contact numbers, country of residence, and passport information up to date.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 640px; margin: 0 auto;">
    <div class="card" style="box-shadow: var(--shadow-md);">
        <form action="<?php echo url('patient/profile.php'); ?>" method="POST">
            <?php echo csrfField(); ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control <?php echo fieldError('name') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('name', $profile['name'] ?? '')); ?>" required>
                    <?php if (fieldError('name')): ?><span class="form-error"><?php echo e(fieldError('name')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control <?php echo fieldError('email') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('email', $profile['email'] ?? '')); ?>" required>
                    <?php if (fieldError('email')): ?><span class="form-error"><?php echo e(fieldError('email')); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control"
                        value="<?php echo e(old('phone', $profile['phone'] ?? '')); ?>" placeholder="e.g. +1 555-019-2834">
                </div>

                <div class="form-group">
                    <label for="country" class="form-label">Home Country</label>
                    <input type="text" id="country" name="country" class="form-control"
                        value="<?php echo e(old('country', $profile['country'] ?? '')); ?>" placeholder="e.g. United Kingdom">
                </div>
            </div>

            <div class="form-group">
                <label for="passport_no" class="form-label">Passport Number</label>
                <input type="text" id="passport_no" name="passport_no" class="form-control <?php echo fieldError('passport_no') ? 'is-invalid' : ''; ?>"
                    value="<?php echo e(old('passport_no', $profile['passport_no'] ?? '')); ?>" placeholder="e.g. A12345678">
                <?php if (fieldError('passport_no')): ?>
                    <span class="form-error"><?php echo e(fieldError('passport_no')); ?></span>
                <?php else: ?>
                    <span class="form-hint">Used automatically to populate medical visa applications.</span>
                <?php endif; ?>
            </div>

            <div style="margin: 2rem 0 1rem 0; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem;">Security Credentials (Optional)</h4>
                <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1rem;">Leave blank if you do not wish to change your password.</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control <?php echo fieldError('new_password') ? 'is-invalid' : ''; ?>"
                            placeholder="Min 8 characters">
                        <?php if (fieldError('new_password')): ?><span class="form-error"><?php echo e(fieldError('new_password')); ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control <?php echo fieldError('confirm_password') ? 'is-invalid' : ''; ?>"
                            placeholder="Re-enter new password">
                        <?php if (fieldError('confirm_password')): ?><span class="form-error"><?php echo e(fieldError('confirm_password')); ?></span><?php endif; ?>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Save Changes &rarr;</button>
                <a href="<?php echo url('patient/dashboard.php'); ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
