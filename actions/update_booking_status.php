<?php
/**
 * Action: Update Booking Status
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('public/index.php'));
    exit();
}

requireLogin();
requireCsrfToken();

$user = currentUser();
$id        = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
$type      = trim($_POST['type'] ?? '');
$newStatus = strtolower(trim($_POST['status'] ?? ''));

$tableMap = [
    'hospital'  => 'appointments',
    'hotel'     => 'hotel_bookings',
    'transport' => 'transport_bookings',
    'visa'      => 'visa_bookings',
];

if (!$id || !isset($tableMap[$type])) {
    setFlash('error', 'Invalid booking reference or type specified.');
    redirectByRole($user['role']);
}

$table = $tableMap[$type];
$pdo = getDbConnection();

// Fetch target booking
$stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    setFlash('error', 'The requested record could not be found.');
    redirectByRole($user['role']);
}

$currentStatus = strtolower($booking['status'] ?? 'pending');

// Authorization & Permission Checks
$allowed = false;
$role = $user['role'];

if ($role === ROLE_ADMIN) {
    // Admin has full authorized access to lifecycle updates
    $allowed = isStatusTransitionAllowed(ROLE_ADMIN, $currentStatus, $newStatus);
} elseif ($role === ROLE_DOCTOR && $type === 'hospital') {
    // Doctor can only update their own appointments
    $doctorId = currentDoctorId();
    if ((int)$booking['doctor_id'] === $doctorId) {
        $allowed = isStatusTransitionAllowed(ROLE_DOCTOR, $currentStatus, $newStatus);
    } else {
        appLog('UNAUTHORIZED_DOCTOR_ACCESS', ['user_id' => $user['id'], 'appointment_id' => $id]);
    }
} elseif ($role === ROLE_PATIENT) {
    // Patients can only cancel their own pending bookings
    $patientId = currentPatientId();
    if ((int)$booking['patient_id'] === $patientId && $currentStatus === 'pending' && $newStatus === 'cancelled') {
        $allowed = true;
    } else {
        appLog('UNAUTHORIZED_PATIENT_ACCESS', ['user_id' => $user['id'], 'booking_id' => $id, 'type' => $type]);
    }
}

if (!$allowed) {
    setFlash('error', 'You do not have permission to transition this record from ' . e(ucfirst($currentStatus)) . ' to ' . e(ucfirst($newStatus)) . '.');
    if ($role === ROLE_ADMIN) {
        header('Location: ' . url("admin/{$type}_bookings.php"));
    } elseif ($role === ROLE_DOCTOR) {
        header('Location: ' . url('doctor/appointments.php'));
    } else {
        header('Location: ' . url('patient/bookings.php'));
    }
    exit();
}

try {
    $stmtUp = $pdo->prepare("UPDATE `$table` SET status = ? WHERE id = ?");
    $stmtUp->execute([$newStatus, $id]);

    $ref = $booking['reference_no'] ?? "#{$id}";

    if ($role === ROLE_ADMIN) {
        adminLog($user['id'], "Updated {$type} booking {$ref} status from '{$currentStatus}' to '{$newStatus}'");
    } else {
        appLog('BOOKING_STATUS_UPDATED', [
            'user_id' => $user['id'],
            'role'    => $role,
            'type'    => $type,
            'id'      => $id,
            'old'     => $currentStatus,
            'new'     => $newStatus,
        ]);
    }

    setFlash('success', ucfirst($type) . " booking {$ref} status successfully updated to <strong>" . e(ucfirst($newStatus)) . "</strong>.");

    // Redirect to appropriate return URL
    $returnUrl = trim($_POST['return_url'] ?? '');
    if (!empty($returnUrl) && str_starts_with($returnUrl, '/')) {
        header('Location: ' . $returnUrl);
    } elseif ($role === ROLE_ADMIN) {
        header('Location: ' . url("admin/{$type}_bookings.php"));
    } elseif ($role === ROLE_DOCTOR) {
        header('Location: ' . url('doctor/appointments.php'));
    } else {
        header('Location: ' . url('patient/bookings.php'));
    }
    exit();
} catch (Exception $e) {
    appLog('BOOKING_UPDATE_ERROR', ['error' => $e->getMessage()]);
    setFlash('error', 'Database error updating booking status.');
    redirectByRole($role);
}
