<?php
/**
 * Unified Admin Booking Update Page
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/validation.php';

requireAdmin();
$user = currentUser();
$pdo  = getDbConnection();

$id   = filter_var($_GET['id'] ?? ($_POST['id'] ?? ''), FILTER_VALIDATE_INT);
$type = trim($_GET['type'] ?? ($_POST['type'] ?? ''));

$tableMap = [
    'hospital'  => 'appointments',
    'hotel'     => 'hotel_bookings',
    'transport' => 'transport_bookings',
    'visa'      => 'visa_bookings',
];

if (!$id || !isset($tableMap[$type])) {
    setFlash('error', 'Invalid booking record or service type.');
    header('Location: ' . url('admin/dashboard.php'));
    exit();
}

$table = $tableMap[$type];

// Fetch current booking record
$stmt = $pdo->prepare("
    SELECT b.*, u.name as patient_name, u.email as patient_email
    FROM `$table` b
    JOIN patients p ON b.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE b.id = ?
");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    setFlash('error', 'Record not found.');
    header('Location: ' . url("admin/{$type}_bookings.php"));
    exit();
}

$currentStatus = strtolower($booking['status'] ?? 'pending');
$refNo = $booking['reference_no'] ?? "#{$id}";

// Handle POST Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();

    $newStatus = strtolower(trim($_POST['status'] ?? $currentStatus));
    $errors = [];

    // Check status transition
    if (!isStatusTransitionAllowed(ROLE_ADMIN, $currentStatus, $newStatus)) {
        $errors['status'] = 'Invalid status transition from ' . ucfirst($currentStatus) . ' to ' . ucfirst($newStatus) . '.';
    }

    try {
        if ($type === 'hospital') {
            $notes = trim($_POST['notes'] ?? '');
            $stmtUp = $pdo->prepare("UPDATE appointments SET status = ?, notes = ? WHERE id = ?");
            $stmtUp->execute([$newStatus, $notes ?: null, $id]);
        } elseif ($type === 'hotel') {
            $hotelName = trim($_POST['hotel_name'] ?? $booking['hotel_name']);
            $roomType  = trim($_POST['room_type'] ?? $booking['room_type']);
            $numGuests = max(1, (int)($_POST['num_guests'] ?? $booking['num_guests']));
            $stmtUp = $pdo->prepare("UPDATE hotel_bookings SET status = ?, hotel_name = ?, room_type = ?, num_guests = ? WHERE id = ?");
            $stmtUp->execute([$newStatus, $hotelName, $roomType, $numGuests, $id]);
        } elseif ($type === 'transport') {
            $transType = trim($_POST['transport_type'] ?? $booking['transport_type']);
            $pickup    = trim($_POST['pickup_location'] ?? $booking['pickup_location']);
            $dest      = trim($_POST['destination'] ?? $booking['destination']);
            $stmtUp = $pdo->prepare("UPDATE transport_bookings SET status = ?, transport_type = ?, pickup_location = ?, destination = ? WHERE id = ?");
            $stmtUp->execute([$newStatus, $transType, $pickup, $dest, $id]);
        } elseif ($type === 'visa') {
            $visaType = trim($_POST['visa_type'] ?? $booking['visa_type']);
            $country  = trim($_POST['country'] ?? $booking['country']);
            $passport = trim($_POST['passport_number'] ?? $booking['passport_number']);
            $stmtUp = $pdo->prepare("UPDATE visa_bookings SET status = ?, visa_type = ?, country = ?, passport_number = ? WHERE id = ?");
            $stmtUp->execute([$newStatus, $visaType, $country, strtoupper($passport), $id]);
        }

        adminLog($user['id'], "Updated {$type} record {$refNo}: Status changed from '{$currentStatus}' to '{$newStatus}'");
        setFlash('success', ucfirst($type) . " record {$refNo} updated successfully.");
        header('Location: ' . url("admin/{$type}_bookings.php"));
        exit();
    } catch (Exception $e) {
        appLog('ADMIN_UPDATE_BOOKING_ERROR', ['error' => $e->getMessage()]);
        setFlash('error', 'Database error updating record.');
        header('Location: ' . url("admin/update_booking.php?type={$type}&id={$id}"));
        exit();
    }
}

$pageTitle = 'Update ' . ucfirst($type) . ' Record #' . $id;
$heroTitle = 'Update ' . ucfirst($type) . ' Booking';
$heroSubtitle = 'Modify status, service parameters, and notes for reference ' . $refNo;
$heroClass = 'hero-admin';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 680px; margin: 0 auto;">
    <div class="card" style="box-shadow: var(--shadow-lg);">
        <div class="card-header">
            <div>
                <h2 class="card-title"><?php echo e(ucfirst($type)); ?> Record Editor</h2>
                <span class="card-subtitle">Patient: <strong><?php echo e($booking['patient_name']); ?></strong> (<?php echo e($booking['patient_email']); ?>)</span>
            </div>
            <a href="<?php echo url("admin/{$type}_bookings.php"); ?>" class="btn btn-outline btn-sm">&larr; Back to List</a>
        </div>

        <!-- Details Summary Box -->
        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div>
                    <span style="color: var(--text-muted); display: block;">Booking Reference:</span>
                    <strong style="font-family: monospace; color: var(--primary);"><?php echo e($refNo); ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">Current Status:</span>
                    <span><?php echo renderStatusBadge($booking['status']); ?></span>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">Patient ID:</span>
                    <span>#PAT-<?php echo $booking['patient_id']; ?></span>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">Submitted Date:</span>
                    <span><?php echo formatDateTime($booking['created_at']); ?></span>
                </div>
            </div>
        </div>

        <form action="<?php echo url("admin/update_booking.php?type={$type}&id={$id}"); ?>" method="POST">
            <?php echo csrfField(); ?>
            <input type="hidden" name="id" value="<?php echo (int)$booking['id']; ?>">
            <input type="hidden" name="type" value="<?php echo e($type); ?>">

            <!-- Status Dropdown -->
            <div class="form-group">
                <label for="status" class="form-label">Booking Status <span class="required">*</span></label>
                <select id="status" name="status" class="form-select" required>
                    <option value="pending" <?php echo $currentStatus === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $currentStatus === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="confirmed" <?php echo $currentStatus === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo $currentStatus === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="rejected" <?php echo $currentStatus === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    <option value="cancelled" <?php echo $currentStatus === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <!-- Type Specific Fields -->
            <?php if ($type === 'hospital'): ?>
                <div class="form-group">
                    <label for="notes" class="form-label">Clinical / Admin Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="form-control"><?php echo e($booking['notes']); ?></textarea>
                </div>
            <?php elseif ($type === 'hotel'): ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hotel_name" class="form-label">Hotel Name</label>
                        <input type="text" id="hotel_name" name="hotel_name" class="form-control" value="<?php echo e($booking['hotel_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="room_type" class="form-label">Room Type</label>
                        <input type="text" id="room_type" name="room_type" class="form-control" value="<?php echo e($booking['room_type']); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="num_guests" class="form-label">Number of Guests</label>
                    <input type="number" id="num_guests" name="num_guests" min="1" max="20" class="form-control" value="<?php echo e($booking['num_guests']); ?>" required>
                </div>
            <?php elseif ($type === 'transport'): ?>
                <div class="form-group">
                    <label for="transport_type" class="form-label">Vehicle Type</label>
                    <input type="text" id="transport_type" name="transport_type" class="form-control" value="<?php echo e($booking['transport_type']); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="pickup_location" class="form-label">Pickup Location</label>
                        <input type="text" id="pickup_location" name="pickup_location" class="form-control" value="<?php echo e($booking['pickup_location']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="destination" class="form-label">Destination</label>
                        <input type="text" id="destination" name="destination" class="form-control" value="<?php echo e($booking['destination']); ?>" required>
                    </div>
                </div>
            <?php elseif ($type === 'visa'): ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="visa_type" class="form-label">Visa Type</label>
                        <input type="text" id="visa_type" name="visa_type" class="form-control" value="<?php echo e($booking['visa_type']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="country" class="form-label">Destination Country</label>
                        <input type="text" id="country" name="country" class="form-control" value="<?php echo e($booking['country']); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="passport_number" class="form-label">Passport Number</label>
                    <input type="text" id="passport_number" name="passport_number" class="form-control" value="<?php echo e($booking['passport_number']); ?>" required>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1.75rem;">
                <button type="submit" class="btn btn-secondary btn-lg" style="background: #4338ca;">Save Changes &rarr;</button>
                <a href="<?php echo url("admin/{$type}_bookings.php"); ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
