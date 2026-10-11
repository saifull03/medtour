<?php
/**
 * Action: Create Hospital Appointment Booking
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
    header('Location: ' . url('patient/hospital.php'));
    exit();
}

requirePatient();
requireCsrfToken();

$patientId = currentPatientId();
$errors = [];

$doctorId        = filter_var($_POST['doctor_id'] ?? '', FILTER_VALIDATE_INT);
$hospitalId      = filter_var($_POST['hospital_id'] ?? '', FILTER_VALIDATE_INT);
$appointmentDate = trim($_POST['appointment_date'] ?? '');
$appointmentTime = trim($_POST['appointment_time'] ?? '');
$notes           = trim($_POST['notes'] ?? '');

// Validation
if (!$hospitalId) {
    $errors['hospital_id'] = 'Please select a hospital.';
}
if (!$doctorId) {
    $errors['doctor_id'] = 'Please select a medical doctor.';
}
if (empty($appointmentDate)) {
    $errors['appointment_date'] = 'Appointment date is required.';
} elseif (!isDateNotInPast($appointmentDate)) {
    $errors['appointment_date'] = 'Appointment date cannot be in the past.';
}

$pdo = getDbConnection();

// Verify hospital exists
if ($hospitalId) {
    $stmtH = $pdo->prepare("SELECT id FROM hospitals WHERE id = ?");
    $stmtH->execute([$hospitalId]);
    if (!$stmtH->fetch()) {
        $errors['hospital_id'] = 'The selected hospital does not exist.';
    }
}

// Verify doctor exists and optionally verify hospital relationship
if ($doctorId) {
    $stmtD = $pdo->prepare("SELECT id, hospital_id FROM doctors WHERE id = ?");
    $stmtD->execute([$doctorId]);
    $doc = $stmtD->fetch();
    if (!$doc) {
        $errors['doctor_id'] = 'The selected doctor does not exist.';
    } elseif ($doc['hospital_id'] && $hospitalId && (int)$doc['hospital_id'] !== $hospitalId) {
        $errors['doctor_id'] = 'The selected doctor is affiliated with another hospital facility.';
    }
}

if (!empty($errors)) {
    setFormFlash($_POST, $errors);
    setFlash('error', 'Please resolve the appointment booking errors below.');
    header('Location: ' . url('patient/hospital.php'));
    exit();
}

// Combine date and time for DATETIME column
$datetimeStr = $appointmentDate . ' ' . ($appointmentTime ? $appointmentTime . ':00' : '09:00:00');

try {
    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, hospital_id, appointment_date, status, notes, created_at) VALUES (?, ?, ?, ?, 'pending', ?, NOW())");
    $stmt->execute([$patientId, $doctorId, $hospitalId, $datetimeStr, $notes ?: null]);
    $appointmentId = (int)$pdo->lastInsertId();

    $refNo = generateReferenceNumber('APT', $appointmentId);
    $upStmt = $pdo->prepare("UPDATE appointments SET reference_no = ? WHERE id = ?");
    $upStmt->execute([$refNo, $appointmentId]);

    appLog('BOOKING_HOSPITAL_CREATED', [
        'patient_id' => $patientId,
        'appointment_id' => $appointmentId,
        'reference_no' => $refNo,
    ]);

    setFlash('success', "Appointment scheduled successfully! Reference: <strong>" . e($refNo) . "</strong>. Status: Pending confirmation.");
    header('Location: ' . url('patient/bookings.php'));
    exit();
} catch (Exception $e) {
    appLog('BOOKING_HOSPITAL_ERROR', ['error' => $e->getMessage()]);
    setFormFlash($_POST, ['general' => 'Database error while saving appointment.']);
    setFlash('error', 'Unable to book hospital appointment. Please try again.');
    header('Location: ' . url('patient/hospital.php'));
    exit();
}
