<?php
/**
 * Unified Common Login Page
 * MedTour Services
 *
 * Single common login for all users (Patients, Doctors, Administrators).
 * Automatically redirects to the corresponding dashboard upon successful authentication.
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

// If already authenticated, redirect to their role-specific dashboard
if (isLoggedIn()) {
    redirectByRole();
}

$pageTitle = 'Sign In';
$metaDescription = 'Sign in to your MedTour Services account to access your personal healthcare portal.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 440px; margin: 2rem auto;">
    <div class="card" style="box-shadow: var(--shadow-lg);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: var(--primary-light); color: var(--primary); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 0.75rem auto;">
                🔐
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Sign In</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Access your MedTour portal & services</p>
        </div>

        <form action="<?php echo url('actions/login.php'); ?>" method="POST" autocomplete="on">
            <?php echo csrfField(); ?>

            <div class="form-group">
                <label for="identifier" class="form-label">Email or Username <span class="required">*</span></label>
                <input type="text" id="identifier" name="identifier" class="form-control"
                    placeholder="Enter your registered email or username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control"
                    placeholder="Enter your password" required>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary btn-block btn-lg">Sign In &rarr;</button>
            </div>
        </form>

        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.85rem; color: var(--text-muted);">
            Don't have an account? <a href="<?php echo url('public/signup_user.php'); ?>" style="font-weight: 600;">Sign up as a new patient &rarr;</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
