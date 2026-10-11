<?php
/**
 * Administrator Provisioning Page
 * MedTour Services
 *
 * Security: Public self-registration of administrators is strictly blocked.
 * Only authenticated administrators can provision a new administrator.
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$user = currentUser();
$isAdmin = $user && $user['role'] === ROLE_ADMIN;

$pageTitle = 'Administrator Provisioning';
$metaDescription = 'Secure administrator provisioning console.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 540px; margin: 2rem auto;">
    <?php if (!$isAdmin): ?>
        <!-- Restricted Access Notice for Non-Admins / Guests -->
        <div class="card" style="box-shadow: var(--shadow-lg); border-top: 4px solid #ef4444; text-align: center; padding: 2.5rem 2rem;">
            <div style="width: 4rem; height: 4rem; background: #fee2e2; color: #b91c1c; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.25rem auto;">
                🔒
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Public Admin Registration Restricted</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.75rem;">
                In accordance with MedTour security policy, administrative accounts cannot be created publicly. New administrators can only be provisioned by an existing system administrator logged into the administration panel.
            </p>
            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo url('public/login_admin.php'); ?>" class="btn btn-secondary">Admin Login &rarr;</a>
                <a href="<?php echo url('public/index.php'); ?>" class="btn btn-outline">Return to Home</a>
            </div>
        </div>
    <?php else: ?>
        <!-- Provisioning Form for Logged-In Administrator -->
        <div class="card" style="box-shadow: var(--shadow-lg); border-top: 4px solid #4338ca;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 3.5rem; height: 3.5rem; background: #ede9fe; color: #4338ca; border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 0.75rem auto;">
                    🛡️
                </div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Provision New Administrator</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Authorized by: <strong><?php echo e($user['name']); ?></strong></p>
            </div>

            <form action="<?php echo url('actions/register.php'); ?>" method="POST" autocomplete="off">
                <?php echo csrfField(); ?>
                <input type="hidden" name="role" value="<?php echo ROLE_ADMIN; ?>">

                <div class="form-group">
                    <label for="name" class="form-label">Administrator Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control <?php echo fieldError('name') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('name')); ?>" placeholder="e.g. Operations Officer" required autofocus>
                    <?php if (fieldError('name')): ?><span class="form-error"><?php echo e(fieldError('name')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Official Admin Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control <?php echo fieldError('email') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('email')); ?>" placeholder="e.g. officer@medtour.com" required>
                    <?php if (fieldError('email')): ?><span class="form-error"><?php echo e(fieldError('email')); ?></span><?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Initial Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" class="form-control <?php echo fieldError('password') ? 'is-invalid' : ''; ?>"
                            placeholder="Min 8 chars, 1 uppercase, 1 digit" required>
                        <?php if (fieldError('password')): ?><span class="form-error"><?php echo e(fieldError('password')); ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password <span class="required">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control <?php echo fieldError('confirm_password') ? 'is-invalid' : ''; ?>"
                            placeholder="Re-enter password" required>
                        <?php if (fieldError('confirm_password')): ?><span class="form-error"><?php echo e(fieldError('confirm_password')); ?></span><?php endif; ?>
                    </div>
                </div>

                <div style="margin-top: 1.75rem;">
                    <button type="submit" class="btn btn-secondary btn-block btn-lg" style="background: #4338ca;">Create Administrator Account &rarr;</button>
                </div>
            </form>

            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="<?php echo url('admin/dashboard.php'); ?>" class="btn btn-outline btn-sm">&larr; Back to Admin Dashboard</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
