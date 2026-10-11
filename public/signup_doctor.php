<?php
/**
 * Doctor Registration Page
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

// If already logged in, redirect
if (isLoggedIn()) {
    redirectByRole();
}

$pageTitle = 'Physician Registration';
$metaDescription = 'Join MedTour partner medical specialists network.';

$pdo = getDbConnection();
$hospitals = $pdo->query("SELECT id, name, location FROM hospitals ORDER BY name ASC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 620px; margin: 2rem auto;">
    <div class="card" style="box-shadow: var(--shadow-lg);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: #dcfce7; color: #166534; border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 0.75rem auto;">
                🩺
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Medical Specialist Registration</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Register your credentials to receive international patient appointments</p>
        </div>

        <form action="<?php echo url('actions/register.php'); ?>" method="POST" autocomplete="on">
            <?php echo csrfField(); ?>
            <input type="hidden" name="role" value="<?php echo ROLE_DOCTOR; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Doctor Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control <?php echo fieldError('name') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('name')); ?>" placeholder="e.g. Dr. Ahmed Hassan" required autofocus>
                    <?php if (fieldError('name')): ?><span class="form-error"><?php echo e(fieldError('name')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Professional Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control <?php echo fieldError('email') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('email')); ?>" placeholder="e.g. drahmed@hospital.com" required>
                    <?php if (fieldError('email')): ?><span class="form-error"><?php echo e(fieldError('email')); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password <span class="required">*</span></label>
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

            <div class="form-row">
                <div class="form-group">
                    <label for="specialization" class="form-label">Specialization / Department <span class="required">*</span></label>
                    <input type="text" id="specialization" name="specialization" class="form-control <?php echo fieldError('specialization') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('specialization')); ?>" placeholder="e.g. Cardiology, Orthopedics, Oncology" required>
                    <?php if (fieldError('specialization')): ?><span class="form-error"><?php echo e(fieldError('specialization')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="hospital_id" class="form-label">Primary Hospital Affiliation</label>
                    <select id="hospital_id" name="hospital_id" class="form-select">
                        <option value="">— Select Affiliated Hospital —</option>
                        <?php foreach ($hospitals as $h): ?>
                            <option value="<?php echo e($h['id']); ?>" <?php echo old('hospital_id') == $h['id'] ? 'selected' : ''; ?>>
                                <?php echo e($h['name']); ?> (<?php echo e($h['location']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="experience" class="form-label">Years of Experience <span class="required">*</span></label>
                    <input type="number" id="experience" name="experience" min="0" max="60" class="form-control <?php echo fieldError('experience') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('experience', '5')); ?>" required>
                    <?php if (fieldError('experience')): ?><span class="form-error"><?php echo e(fieldError('experience')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="consultation_fee" class="form-label">Consultation Fee ($ USD) <span class="required">*</span></label>
                    <input type="number" id="consultation_fee" name="consultation_fee" step="0.01" min="0" class="form-control <?php echo fieldError('consultation_fee') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('consultation_fee', '150.00')); ?>" required>
                    <?php if (fieldError('consultation_fee')): ?><span class="form-error"><?php echo e(fieldError('consultation_fee')); ?></span><?php endif; ?>
                </div>
            </div>

            <div style="margin-top: 1.75rem;">
                <button type="submit" class="btn btn-success btn-block btn-lg">Complete Doctor Registration &rarr;</button>
            </div>
        </form>

        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.85rem; color: var(--text-muted);">
            Already have a doctor profile? <a href="<?php echo url('public/login.php'); ?>" style="font-weight: 600;">Sign in here &rarr;</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
