<?php
/**
 * Administrator Login Page
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

// If already logged in as admin, redirect to admin dashboard
$user = currentUser();
if ($user && $user['role'] === ROLE_ADMIN) {
    header('Location: ' . url('admin/dashboard.php'));
    exit();
}

$pageTitle = 'Administrator Login';
$metaDescription = 'Secure administrative control center authentication for MedTour Services.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 440px; margin: 2rem auto;">
    <div class="card" style="box-shadow: var(--shadow-lg); border-top: 4px solid #6366f1;">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: #ede9fe; color: #4338ca; border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 0.75rem auto;">
                🛡️
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Administrator Access</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Restricted medical tourism administration console</p>
        </div>

        <form action="<?php echo url('actions/login.php'); ?>" method="POST" autocomplete="on">
            <?php echo csrfField(); ?>
            <input type="hidden" name="role" value="<?php echo ROLE_ADMIN; ?>">

            <div class="form-group">
                <label for="identifier" class="form-label">Admin Email or Username <span class="required">*</span></label>
                <input type="text" id="identifier" name="identifier" class="form-control" placeholder="e.g. admin@medtour.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Security Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter administrative password" required>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-secondary btn-block btn-lg" style="background: #4338ca;">Sign In to Administration &rarr;</button>
            </div>
        </form>

        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.8rem; color: var(--text-muted);">
            Administrative access is strictly restricted to authorized staff.
        </div>
    </div>

    <div style="text-align: center; margin-top: 1.25rem; font-size: 0.8rem; color: var(--text-muted);">
        <a href="<?php echo url('public/login_user.php'); ?>">Patient Login</a> &middot; <a href="<?php echo url('public/login_doctor.php'); ?>">Doctor Login</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
