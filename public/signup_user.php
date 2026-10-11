<?php
/**
 * Patient Registration Page
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

// If already logged in, redirect
if (isLoggedIn()) {
    redirectByRole();
}

$pageTitle = 'Patient Registration';
$metaDescription = 'Create a secure patient account to schedule hospital treatments, book hotels, and obtain travel visas.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 580px; margin: 2rem auto;">
    <div class="card" style="box-shadow: var(--shadow-lg);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: var(--primary-light); color: var(--primary); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 0.75rem auto;">
                📝
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Patient Registration</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Create your profile for personal healthcare coordination abroad</p>
        </div>

        <form action="<?php echo url('actions/register.php'); ?>" method="POST" autocomplete="on">
            <?php echo csrfField(); ?>
            <input type="hidden" name="role" value="<?php echo ROLE_PATIENT; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control <?php echo fieldError('name') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('name')); ?>" placeholder="e.g. John Doe" required autofocus>
                    <?php if (fieldError('name')): ?><span class="form-error"><?php echo e(fieldError('name')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control <?php echo fieldError('email') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('email')); ?>" placeholder="e.g. john@example.com" required>
                    <?php if (fieldError('email')): ?><span class="form-error"><?php echo e(fieldError('email')); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" class="form-control <?php echo fieldError('password') ? 'is-invalid' : ''; ?>"
                        placeholder="Min 8 chars, 1 uppercase, 1 digit" required>
                    <?php if (fieldError('password')): ?>
                        <span class="form-error"><?php echo e(fieldError('password')); ?></span>
                    <?php else: ?>
                        <span class="form-hint">At least 8 characters with letters & numbers.</span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control <?php echo fieldError('confirm_password') ? 'is-invalid' : ''; ?>"
                        placeholder="Re-enter password" required>
                    <?php if (fieldError('confirm_password')): ?><span class="form-error"><?php echo e(fieldError('confirm_password')); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control"
                        value="<?php echo e(old('phone')); ?>" placeholder="e.g. +1 555-019-2834">
                </div>

                <div class="form-group">
                    <label for="country" class="form-label">Home Country</label>
                    <input type="text" id="country" name="country" class="form-control"
                        value="<?php echo e(old('country')); ?>" placeholder="e.g. United Kingdom, USA">
                </div>
            </div>

            <div class="form-group">
                <label for="passport_no" class="form-label">Passport Number (Optional)</label>
                <input type="text" id="passport_no" name="passport_no" class="form-control"
                    value="<?php echo e(old('passport_no')); ?>" placeholder="e.g. A12345678">
                <span class="form-hint">Can be added later during visa application.</span>
            </div>

            <div style="margin-top: 1.75rem;">
                <button type="submit" class="btn btn-primary btn-block btn-lg">Create Patient Account &rarr;</button>
            </div>
        </form>

        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.85rem; color: var(--text-muted);">
            Already have an account? <a href="<?php echo url('public/login_user.php'); ?>" style="font-weight: 600;">Sign in here &rarr;</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
