<?php
/**
 * Apply for Medical Visa
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

requirePatient();
$patientId = currentPatientId();
$pdo = getDbConnection();

// Fetch patient passport number if saved
$stmt = $pdo->prepare("SELECT passport_no, country FROM patients WHERE id = ?");
$stmt->execute([$patientId]);
$pat = $stmt->fetch();
$existingPassport = $pat['passport_no'] ?? '';
$patientCountry   = $pat['country'] ?? '';

$popularCountries = ['Egypt', 'Thailand', 'Turkey', 'India', 'Germany', 'Malaysia', 'South Korea', 'UAE', 'Singapore', 'Jordan'];

$pageTitle = 'Medical Visa Assistance';
$heroTitle = 'Medical Travel Visa Application';
$heroSubtitle = 'Expedited consular assistance, official invitation letters, and medical entry clearance.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-header">
            <div>
                <h2 class="card-title">Visa Application Request</h2>
                <span class="card-subtitle">Submit passport and travel details for consular coordination</span>
            </div>
            <a href="<?php echo url('patient/bookings.php'); ?>" class="btn btn-outline btn-sm">&larr; My Bookings</a>
        </div>

        <form action="<?php echo url('actions/create_visa_application.php'); ?>" method="POST">
            <?php echo csrfField(); ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="visa_type" class="form-label">Visa Category <span class="required">*</span></label>
                    <select id="visa_type" name="visa_type" class="form-select <?php echo fieldError('visa_type') ? 'is-invalid' : ''; ?>" required>
                        <option value="Medical" <?php echo old('visa_type', 'Medical') === 'Medical' ? 'selected' : ''; ?>>🏥 Medical Treatment Visa (Recommended)</option>
                        <option value="Tourist" <?php echo old('visa_type') === 'Tourist' ? 'selected' : ''; ?>>🌍 Tourist Visa</option>
                        <option value="Business" <?php echo old('visa_type') === 'Business' ? 'selected' : ''; ?>>💼 Business Travel Visa</option>
                        <option value="Student" <?php echo old('visa_type') === 'Student' ? 'selected' : ''; ?>>🎓 Educational / Training Visa</option>
                    </select>
                    <?php if (fieldError('visa_type')): ?><span class="form-error"><?php echo e(fieldError('visa_type')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="country" class="form-label">Destination Country <span class="required">*</span></label>
                    <select id="country" name="country" class="form-select <?php echo fieldError('country') ? 'is-invalid' : ''; ?>" required>
                        <option value="">— Select Country of Medical Care —</option>
                        <?php foreach ($popularCountries as $c): ?>
                            <option value="<?php echo e($c); ?>" <?php echo old('country', 'Egypt') === $c ? 'selected' : ''; ?>>
                                <?php echo e($c); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (fieldError('country')): ?><span class="form-error"><?php echo e(fieldError('country')); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="passport_number" class="form-label">Passport Number <span class="required">*</span></label>
                    <input type="text" id="passport_number" name="passport_number"
                        class="form-control <?php echo fieldError('passport_number') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('passport_number', $existingPassport)); ?>"
                        placeholder="e.g. A12345678" required>
                    <?php if (fieldError('passport_number')): ?>
                        <span class="form-error"><?php echo e(fieldError('passport_number')); ?></span>
                    <?php else: ?>
                        <span class="form-hint">Must match your official passport document.</span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="application_date" class="form-label">Intended Travel Date <span class="required">*</span></label>
                    <input type="date" id="application_date" name="application_date" min="<?php echo date('Y-m-d'); ?>"
                        class="form-control <?php echo fieldError('application_date') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('application_date')); ?>" required>
                    <?php if (fieldError('application_date')): ?><span class="form-error"><?php echo e(fieldError('application_date')); ?></span><?php endif; ?>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1rem; margin: 1.5rem 0;">
                <p style="font-size: 0.85rem; color: #475569; line-height: 1.5;">
                    📄 <strong>Processing Information:</strong> Medical visas start in <strong>Pending</strong> status. An official consular letter of medical necessity will be drafted once your appointment is linked.
                </p>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center;">
                <button type="submit" class="btn btn-primary btn-lg">Submit Visa Application &rarr;</button>
                <a href="<?php echo url('patient/dashboard.php'); ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
