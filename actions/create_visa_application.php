<?php
/**
 * Action: Create Visa Application
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
    header('Location: ' . url('patient/visa.php'));
    exit();
}

requirePatient();
requireCsrfToken();

$patientId = currentPatientId();
$errors = [];

$visaType   = trim($_POST['visa_type'] ?? 'Medical');
$country    = trim($_POST['country'] ?? '');
$passport   = trim($_POST['passport_number'] ?? '');
$appDate    = trim($_POST['application_date'] ?? '');

$allowedVisaTypes = ['Medical', 'Tourist', 'Business', 'Student'];
if (empty($visaType) || !in_array($visaType, $allowedVisaTypes, true)) {
    $errors['visa_type'] = 'Please select a valid visa category.';
}

if (empty($country)) {
    $errors['country'] = 'Destination country is required.';
}

if (empty($passport)) {
    $errors['passport_number'] = 'Passport number is required.';
} elseif (!isValidPassport($passport)) {
    $errors['passport_number'] = 'Passport number must be 5 to 20 alphanumeric characters.';
}

if (empty($appDate)) {
    $errors['application_date'] = 'Intended travel/application date is required.';
} elseif (!isDateNotInPast($appDate)) {
    $errors['application_date'] = 'Application date cannot be in the past.';
}

if (!empty($errors)) {
    setFormFlash($_POST, $errors);
    setFlash('error', 'Please resolve the visa application errors below.');
    header('Location: ' . url('patient/visa.php'));
    exit();
}

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("INSERT INTO visa_bookings (patient_id, visa_type, country, passport_number, application_date, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$patientId, $visaType, $country, strtoupper($passport), $appDate]);
    $bookingId = (int)$pdo->lastInsertId();

    $refNo = generateReferenceNumber('VSA', $bookingId);
    $upStmt = $pdo->prepare("UPDATE visa_bookings SET reference_no = ? WHERE id = ?");
    $upStmt->execute([$refNo, $bookingId]);

    // Also update patient profile passport number if patient profile doesn't have one
    $stmtUpPat = $pdo->prepare("UPDATE patients SET passport_no = ? WHERE id = ? AND (passport_no IS NULL OR passport_no = '')");
    $stmtUpPat->execute([strtoupper($passport), $patientId]);

    appLog('BOOKING_VISA_CREATED', [
        'patient_id' => $patientId,
        'booking_id' => $bookingId,
        'reference_no' => $refNo,
        'country' => $country,
    ]);

    setFlash('success', "Visa application submitted! Reference: <strong>" . e($refNo) . "</strong> (" . e($visaType) . " visa for " . e($country) . "). Status: Pending review.");
    header('Location: ' . url('patient/bookings.php'));
    exit();
} catch (Exception $e) {
    appLog('BOOKING_VISA_ERROR', ['error' => $e->getMessage()]);
    setFormFlash($_POST, ['general' => 'Database error while saving visa application.']);
    setFlash('error', 'Unable to submit visa application. Please try again.');
    header('Location: ' . url('patient/visa.php'));
    exit();
}
