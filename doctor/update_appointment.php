<?php
/**
 * Doctor Update Appointment Details & Status
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

requireDoctor();
$doctorId = currentDoctorId();
$pdo      = getDbConnection();

$id = filter_var($_GET['id'] ?? ($_POST['id'] ?? ''), FILTER_VALIDATE_INT);
if (!$id) {
    setFlash('error', 'Invalid appointment record specified.');
    header('Location: ' . url('doctor/appointments.php'));
    exit();
}

// Fetch appointment ensuring doctor ownership
$stmt = $pdo->prepare("
    SELECT a.*, u.name as patient_name, u.email as patient_email, p.phone as patient_phone,
           h.name as hospital_name, h.location as hospital_location
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    LEFT JOIN hospitals h ON a.hospital_id = h.id
    WHERE a.id = ? AND a.doctor_id = ?
");
$stmt->execute([$id, $doctorId]);
$appointment = $stmt->fetch();

if (!$appointment) {
    appLog('DOCTOR_UNAUTHORIZED_APPOINTMENT_EDIT', ['doctor_id' => $doctorId, 'appointment_id' => $id]);
    setFlash('error', 'Appointment not found or you are not authorized to manage this record.');
    header('Location: ' . url('doctor/appointments.php'));
    exit();
}

$currentStatus = strtolower($appointment['status'] ?? 'pending');

// Handle POST Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();

    $newStatus = strtolower(trim($_POST['status'] ?? $currentStatus));
    $notes     = trim($_POST['notes'] ?? '');

    // Validate status transition
    if (!isStatusTransitionAllowed(ROLE_DOCTOR, $currentStatus, $newStatus)) {
        setFlash('error', 'Invalid status transition from ' . e(ucfirst($currentStatus)) . ' to ' . e(ucfirst($newStatus)) . '.');
        header('Location: ' . url('doctor/update_appointment.php?id=' . $id));
        exit();
    }

    try {
        $stmtUp = $pdo->prepare("UPDATE appointments SET status = ?, notes = ? WHERE id = ? AND doctor_id = ?");
        $stmtUp->execute([$newStatus, $notes ?: null, $id, $doctorId]);

        $ref = $appointment['reference_no'] ?: "#{$id}";
        appLog('DOCTOR_APPOINTMENT_UPDATED', [
            'doctor_id'      => $doctorId,
            'appointment_id' => $id,
            'old_status'     => $currentStatus,
            'new_status'     => $newStatus,
        ]);

        setFlash('success', "Appointment {$ref} successfully updated. Status is now <strong>" . e(ucfirst($newStatus)) . "</strong>.");
        header('Location: ' . url('doctor/appointments.php'));
        exit();
    } catch (Exception $e) {
        appLog('DOCTOR_APPOINTMENT_UPDATE_ERROR', ['error' => $e->getMessage()]);
        setFlash('error', 'Database error updating appointment.');
        header('Location: ' . url('doctor/update_appointment.php?id=' . $id));
        exit();
    }
}

$pageTitle = 'Manage Appointment #' . $id;
$heroTitle = 'Clinical Appointment Review';
$heroSubtitle = 'Update status and add clinical notes for ' . $appointment['patient_name'];
$heroClass = 'hero-doctor';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 640px; margin: 0 auto;">
    <div class="card" style="box-shadow: var(--shadow-lg);">
        <div class="card-header">
            <div>
                <h2 class="card-title">Appointment Details</h2>
                <span class="card-subtitle">Reference: <strong><?php echo e($appointment['reference_no'] ?: '#' . $appointment['id']); ?></strong></span>
            </div>
            <a href="<?php echo url('doctor/appointments.php'); ?>" class="btn btn-outline btn-sm">&larr; Back to Schedule</a>
        </div>

        <!-- Appointment Info Box -->
        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.875rem;">
                <div>
                    <span style="color: var(--text-muted); display: block;">Patient:</span>
                    <strong><?php echo e($appointment['patient_name']); ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">Patient Email:</span>
                    <span><?php echo e($appointment['patient_email']); ?></span>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">Hospital:</span>
                    <span><?php echo e($appointment['hospital_name'] ?? 'Partner Hospital'); ?></span>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">Scheduled Date & Time:</span>
                    <strong><?php echo formatDateTime($appointment['appointment_date']); ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">Current Status:</span>
                    <span><?php echo renderStatusBadge($appointment['status']); ?></span>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">Created On:</span>
                    <span><?php echo formatDateTime($appointment['created_at']); ?></span>
                </div>
            </div>
        </div>

        <form action="<?php echo url('doctor/update_appointment.php?id=' . $id); ?>" method="POST">
            <?php echo csrfField(); ?>
            <input type="hidden" name="id" value="<?php echo (int)$appointment['id']; ?>">

            <div class="form-group">
                <label for="status" class="form-label">Update Appointment Status <span class="required">*</span></label>
                <select id="status" name="status" class="form-select" required>
                    <option value="<?php echo e($currentStatus); ?>" selected>Keep Current Status (<?php echo e(ucfirst($currentStatus)); ?>)</option>
                    <?php if ($currentStatus === 'pending'): ?>
                        <option value="approved">✅ Approve Appointment</option>
                        <option value="cancelled">❌ Cancel Appointment</option>
                    <?php elseif ($currentStatus === 'approved'): ?>
                        <option value="completed">🎉 Mark as Completed</option>
                        <option value="cancelled">❌ Cancel Appointment</option>
                    <?php endif; ?>
                </select>
                <span class="form-hint">Doctors can approve pending consultations and mark conducted appointments as completed.</span>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Clinical & Preparation Notes</label>
                <textarea id="notes" name="notes" rows="4" class="form-control"
                    placeholder="Enter instructions for the patient or consultation notes..."><?php echo e($appointment['notes']); ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success btn-lg">Save Appointment Updates &rarr;</button>
                <a href="<?php echo url('doctor/appointments.php'); ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
