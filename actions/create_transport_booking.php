<?php
/**
 * Action: Create Transport Booking
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/validation.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('patient/transport.php'));
    exit();
}

requirePatient();
requireCsrfToken();

$patientId = currentPatientId();
$errors = [];

$type   = trim($_POST['transport_type'] ?? '');
$pickup = trim($_POST['pickup_location'] ?? '');
$dest   = trim($_POST['destination'] ?? '');
$date   = trim($_POST['date'] ?? '');
$time   = trim($_POST['time'] ?? '');

$allowedTypes = ['Taxi', 'Private Car', 'Bus', 'Train', 'Ambulance'];
if (empty($type) || !in_array($type, $allowedTypes, true)) {
    $errors['transport_type'] = 'Please select a valid transport type.';
}

if (empty($pickup)) {
    $errors['pickup_location'] = 'Pickup location is required.';
} elseif (strlen($pickup) < 2) {
    $errors['pickup_location'] = 'Pickup location must be at least 2 characters.';
}

if (empty($dest)) {
    $errors['destination'] = 'Destination is required.';
} elseif (strlen($dest) < 2) {
    $errors['destination'] = 'Destination must be at least 2 characters.';
}

// Pickup and destination cannot be identical
if (!empty($pickup) && !empty($dest) && strtolower($pickup) === strtolower($dest)) {
    $errors['destination'] = 'Pickup and destination locations cannot be identical.';
}

if (empty($date)) {
    $errors['date'] = 'Transport date is required.';
} elseif (!isDateNotInPast($date)) {
    $errors['date'] = 'Transport date cannot be in the past.';
}

if (!empty($errors)) {
    setFormFlash($_POST, $errors);
    setFlash('error', 'Please resolve the transport booking errors below.');
    header('Location: ' . url('patient/transport.php'));
    exit();
}

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("INSERT INTO transport_bookings (patient_id, transport_type, pickup_location, destination, date, time, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $timeVal = !empty($time) ? $time . ':00' : null;
    $stmt->execute([$patientId, $type, $pickup, $dest, $date, $timeVal]);
    $bookingId = (int)$pdo->lastInsertId();

    $refNo = generateReferenceNumber('TRN', $bookingId);
    $upStmt = $pdo->prepare("UPDATE transport_bookings SET reference_no = ? WHERE id = ?");
    $upStmt->execute([$refNo, $bookingId]);

    appLog('BOOKING_TRANSPORT_CREATED', [
        'patient_id' => $patientId,
        'booking_id' => $bookingId,
        'reference_no' => $refNo,
    ]);

    setFlash('success', "Transport booking submitted! Reference: <strong>" . e($refNo) . "</strong> (" . e($type) . " on " . e($date) . "). Status: Pending confirmation.");
    header('Location: ' . url('patient/bookings.php'));
    exit();
} catch (Exception $e) {
    appLog('BOOKING_TRANSPORT_ERROR', ['error' => $e->getMessage()]);
    setFormFlash($_POST, ['general' => 'Database error while saving transport booking.']);
    setFlash('error', 'Unable to book transport. Please try again.');
    header('Location: ' . url('patient/transport.php'));
    exit();
}
