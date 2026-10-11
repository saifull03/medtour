<?php
/**
 * Book Hospital Appointment
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

requirePatient();
$pdo = getDbConnection();

$hospitals = $pdo->query("SELECT id, name, location FROM hospitals ORDER BY name ASC")->fetchAll();
$doctors   = $pdo->query("
    SELECT d.id, u.name, d.specialization, d.consultation_fee, d.hospital_id, h.name as hospital_name
    FROM doctors d
    JOIN users u ON d.user_id = u.id
    LEFT JOIN hospitals h ON d.hospital_id = h.id
    ORDER BY u.name ASC
")->fetchAll();

$pageTitle = 'Book Hospital Appointment';
$heroTitle = 'Schedule Hospital Appointment & Consultation';
$heroSubtitle = 'Connect with premier accredited hospital facilities and medical specialists.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-header">
            <div>
                <h2 class="card-title">Hospital Appointment Request</h2>
                <span class="card-subtitle">Choose your preferred hospital, medical specialist, and appointment date</span>
            </div>
            <a href="<?php echo url('patient/bookings.php'); ?>" class="btn btn-outline btn-sm">&larr; My Bookings</a>
        </div>

        <form action="<?php echo url('actions/create_hospital_booking.php'); ?>" method="POST">
            <?php echo csrfField(); ?>

            <div class="form-group">
                <label for="hospital_id" class="form-label">Select Partner Hospital <span class="required">*</span></label>
                <select id="hospital_id" name="hospital_id" class="form-select <?php echo fieldError('hospital_id') ? 'is-invalid' : ''; ?>" required>
                    <option value="">— Choose an Accredited Hospital —</option>
                    <?php foreach ($hospitals as $h): ?>
                        <option value="<?php echo e($h['id']); ?>" <?php echo old('hospital_id') == $h['id'] ? 'selected' : ''; ?>>
                            <?php echo e($h['name']); ?> (<?php echo e($h['location']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (fieldError('hospital_id')): ?><span class="form-error"><?php echo e(fieldError('hospital_id')); ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="doctor_id" class="form-label">Select Medical Doctor / Specialist <span class="required">*</span></label>
                <select id="doctor_id" name="doctor_id" class="form-select <?php echo fieldError('doctor_id') ? 'is-invalid' : ''; ?>" required>
                    <option value="">— Choose a Board-Certified Doctor —</option>
                    <?php foreach ($doctors as $d): ?>
                        <option value="<?php echo e($d['id']); ?>" <?php echo old('doctor_id') == $d['id'] ? 'selected' : ''; ?>>
                            <?php echo e($d['name']); ?> — <?php echo e($d['specialization']); ?> ($<?php echo number_format($d['consultation_fee'], 2); ?>) <?php echo $d['hospital_name'] ? ' @ ' . e($d['hospital_name']) : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (fieldError('doctor_id')): ?><span class="form-error"><?php echo e(fieldError('doctor_id')); ?></span><?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="appointment_date" class="form-label">Appointment Date <span class="required">*</span></label>
                    <input type="date" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>"
                        class="form-control <?php echo fieldError('appointment_date') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('appointment_date')); ?>" required>
                    <?php if (fieldError('appointment_date')): ?><span class="form-error"><?php echo e(fieldError('appointment_date')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="appointment_time" class="form-label">Preferred Time (Optional)</label>
                    <input type="time" id="appointment_time" name="appointment_time"
                        class="form-control"
                        value="<?php echo e(old('appointment_time', '10:00')); ?>">
                    <span class="form-hint">Hospital visiting hours: 09:00 - 18:00</span>
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Medical Symptoms or Procedure Notes</label>
                <textarea id="notes" name="notes" rows="4" class="form-control"
                    placeholder="Describe relevant medical symptoms, prior diagnoses, or special accessibility requirements..."><?php echo e(old('notes')); ?></textarea>
            </div>

            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1rem; margin: 1.5rem 0;">
                <p style="font-size: 0.85rem; color: #475569; line-height: 1.5;">
                    ℹ️ <strong>Status Note:</strong> All appointments begin with status <strong>Pending</strong>. Our patient coordination team will confirm doctor availability within 24 business hours.
                </p>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center;">
                <button type="submit" class="btn btn-primary btn-lg">Submit Appointment Request &rarr;</button>
                <a href="<?php echo url('patient/dashboard.php'); ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
