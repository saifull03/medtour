<?php
/**
 * Cancel Booking Confirmation Page
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

$id   = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
$type = trim($_GET['type'] ?? '');

$tableMap = [
    'hospital'  => 'appointments',
    'hotel'     => 'hotel_bookings',
    'transport' => 'transport_bookings',
    'visa'      => 'visa_bookings',
];

if (!$id || !isset($tableMap[$type])) {
    setFlash('error', 'Invalid booking requested for cancellation.');
    header('Location: ' . url('patient/bookings.php'));
    exit();
}

$table = $tableMap[$type];

// Verify ownership and pending status
$stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ? AND patient_id = ?");
$stmt->execute([$id, $patientId]);
$booking = $stmt->fetch();

if (!$booking) {
    setFlash('error', 'Booking record not found or you do not have permission to access it.');
    header('Location: ' . url('patient/bookings.php'));
    exit();
}

$currentStatus = strtolower($booking['status'] ?? 'pending');
if ($currentStatus !== 'pending') {
    setFlash('error', 'Only pending bookings can be cancelled. Current status is ' . e(ucfirst($currentStatus)) . '.');
    header('Location: ' . url('patient/bookings.php'));
    exit();
}

$refNo = $booking['reference_no'] ?? "#{$id}";

$pageTitle = 'Confirm Booking Cancellation';
$heroTitle = 'Cancellation Confirmation';
$heroSubtitle = 'Please review your booking details before proceeding with cancellation.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 580px; margin: 2rem auto;">
    <div class="card" style="box-shadow: var(--shadow-lg); border-top: 4px solid #ef4444;">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: #fee2e2; color: #dc2626; border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 0.75rem auto;">
                ⚠️
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Cancel <?php echo e(ucfirst($type)); ?> Booking?</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Are you sure you want to cancel this pending booking?</p>
        </div>

        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                <span style="color: var(--text-muted);">Reference:</span>
                <strong style="font-family: monospace; color: var(--primary);"><?php echo e($refNo); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                <span style="color: var(--text-muted);">Service:</span>
                <span><?php echo e(ucfirst($type)); ?> Service</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                <span style="color: var(--text-muted);">Current Status:</span>
                <span><?php echo renderStatusBadge($booking['status']); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                <span style="color: var(--text-muted);">Submitted On:</span>
                <span><?php echo formatDateTime($booking['created_at']); ?></span>
            </div>
        </div>

        <p style="color: #64748b; font-size: 0.85rem; line-height: 1.5; margin-bottom: 1.5rem;">
            Once cancelled, this request will be archived and will no longer be processed by our coordination team. You can always submit a new booking at a later date.
        </p>

        <form action="<?php echo url('actions/update_booking_status.php'); ?>" method="POST">
            <?php echo csrfField(); ?>
            <input type="hidden" name="id" value="<?php echo (int)$booking['id']; ?>">
            <input type="hidden" name="type" value="<?php echo e($type); ?>">
            <input type="hidden" name="status" value="cancelled">
            <input type="hidden" name="return_url" value="<?php echo url('patient/bookings.php'); ?>">

            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-danger btn-block btn-lg">Yes, Cancel Booking</button>
                <a href="<?php echo url('patient/bookings.php'); ?>" class="btn btn-outline btn-block btn-lg">Keep Booking</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
