<?php
/**
 * Action: Create Hotel Booking
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
    header('Location: ' . url('patient/hotel.php'));
    exit();
}

requirePatient();
requireCsrfToken();

$patientId = currentPatientId();
$errors = [];

$hotelId      = filter_var($_POST['hotel_id'] ?? '', FILTER_VALIDATE_INT);
$checkinDate  = trim($_POST['checkin_date'] ?? '');
$checkoutDate = trim($_POST['checkout_date'] ?? '');
$numGuests    = filter_var($_POST['num_guests'] ?? 1, FILTER_VALIDATE_INT);
$roomType     = trim($_POST['room_type'] ?? 'Double');

// Date validations
if (empty($checkinDate)) {
    $errors['checkin_date'] = 'Check-in date is required.';
} elseif (!isDateNotInPast($checkinDate)) {
    $errors['checkin_date'] = 'Check-in date cannot be in the past.';
}

if (empty($checkoutDate)) {
    $errors['checkout_date'] = 'Check-out date is required.';
} elseif ($checkinDate && strtotime($checkoutDate) <= strtotime($checkinDate)) {
    $errors['checkout_date'] = 'Check-out date must be strictly after the check-in date.';
}

if ($numGuests === false || $numGuests < 1 || $numGuests > 20) {
    $errors['num_guests'] = 'Number of guests must be a valid number between 1 and 20.';
}

$allowedRooms = ['Single Room', 'Double Room', 'Deluxe Room', 'Executive Suite', 'Single', 'Double', 'Suite'];
if (empty($roomType) || !in_array($roomType, $allowedRooms, true)) {
    $errors['room_type'] = 'Please select a valid room type.';
}

$pdo = getDbConnection();
$hotelName = '';

if ($hotelId) {
    $stmtH = $pdo->prepare("SELECT name FROM hotels WHERE id = ?");
    $stmtH->execute([$hotelId]);
    $hotelName = $stmtH->fetchColumn();
    if (!$hotelName) {
        $errors['hotel_id'] = 'Selected hotel not found in database.';
    }
} else {
    $hotelName = trim($_POST['hotel_name'] ?? '');
    if (empty($hotelName)) {
        $errors['hotel_id'] = 'Please select a hotel.';
    }
}

if (!empty($errors)) {
    setFormFlash($_POST, $errors);
    setFlash('error', 'Please resolve the hotel reservation errors below.');
    header('Location: ' . url('patient/hotel.php'));
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO hotel_bookings (patient_id, hotel_id, hotel_name, checkin_date, checkout_date, num_guests, room_type, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$patientId, $hotelId ?: null, $hotelName, $checkinDate, $checkoutDate, $numGuests, $roomType]);
    $bookingId = (int)$pdo->lastInsertId();

    $refNo = generateReferenceNumber('HTL', $bookingId);
    $upStmt = $pdo->prepare("UPDATE hotel_bookings SET reference_no = ? WHERE id = ?");
    $upStmt->execute([$refNo, $bookingId]);

    $nights = (int)round((strtotime($checkoutDate) - strtotime($checkinDate)) / 86400);

    appLog('BOOKING_HOTEL_CREATED', [
        'patient_id' => $patientId,
        'booking_id' => $bookingId,
        'reference_no' => $refNo,
        'nights' => $nights,
    ]);

    setFlash('success', "Hotel reservation submitted! Reference: <strong>" . e($refNo) . "</strong> ({$nights} night" . ($nights > 1 ? 's' : '') . " at " . e($hotelName) . "). Status: Pending confirmation.");
    header('Location: ' . url('patient/bookings.php'));
    exit();
} catch (Exception $e) {
    appLog('BOOKING_HOTEL_ERROR', ['error' => $e->getMessage()]);
    setFormFlash($_POST, ['general' => 'Database error while saving hotel reservation.']);
    setFlash('error', 'Unable to reserve hotel. Please try again.');
    header('Location: ' . url('patient/hotel.php'));
    exit();
}
