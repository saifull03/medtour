<?php
/**
 * Doctor Login Page
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

// If already logged in as doctor, redirect to doctor dashboard
$user = currentUser();
if ($user && $user['role'] === ROLE_DOCTOR) {
    header('Location: ' . url('doctor/dashboard.php'));
    exit();
}

$pageTitle = 'Doctor Portal Login';
$metaDescription = 'Physician and specialist login for MedTour Services.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 440px; margin: 2rem auto;">
    <div class="card" style="box-shadow: var(--shadow-lg);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: #dcfce7; color: #166534; border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 0.75rem auto;">
                👨‍⚕️
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Doctor Portal</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Access patient appointments and consultation schedules</p>
        </div>

        <form action="<?php echo url('actions/login.php'); ?>" method="POST" autocomplete="on">
            <?php echo csrfField(); ?>
            <input type="hidden" name="role" value="<?php echo ROLE_DOCTOR; ?>">

            <div class="form-group">
                <label for="identifier" class="form-label">Email or Doctor Name <span class="required">*</span></label>
                <input type="text" id="identifier" name="identifier" class="form-control" placeholder="e.g. siddik@gmail.com or Dr Siddik" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your doctor password" required>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success btn-block btn-lg">Sign In as Doctor &rarr;</button>
            </div>
        </form>

        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.85rem; color: var(--text-muted);">
            New physician? <a href="<?php echo url('public/signup_doctor.php'); ?>" style="font-weight: 600;">Register doctor profile &rarr;</a>
        </div>
    </div>

    <div style="text-align: center; margin-top: 1.25rem; font-size: 0.8rem; color: var(--text-muted);">
        <a href="<?php echo url('public/login_user.php'); ?>">Patient Login</a> &middot; <a href="<?php echo url('public/login_admin.php'); ?>">Admin Portal</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
