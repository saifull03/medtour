<?php
/**
 * Action: Delete Booking
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('admin/dashboard.php'));
    exit();
}

requireAdmin();
requireCsrfToken();

$user = currentUser();
$id   = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
$type = trim($_POST['type'] ?? '');

$tableMap = [
    'hospital'  => 'appointments',
    'hotel'     => 'hotel_bookings',
    'transport' => 'transport_bookings',
    'visa'      => 'visa_bookings',
];

if (!$id || !isset($tableMap[$type])) {
    setFlash('error', 'Invalid booking identifier or record type.');
    header('Location: ' . url('admin/dashboard.php'));
    exit();
}

$table = $tableMap[$type];
$pdo = getDbConnection();

try {
    $stmtFind = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
    $stmtFind->execute([$id]);
    $record = $stmtFind->fetch();

    if (!$record) {
        setFlash('error', 'Booking record not found.');
        header('Location: ' . url("admin/{$type}_bookings.php"));
        exit();
    }

    $ref = $record['reference_no'] ?? "#{$id}";

    $stmtDel = $pdo->prepare("DELETE FROM `$table` WHERE id = ?");
    $stmtDel->execute([$id]);

    adminLog($user['id'], "Permanently deleted {$type} booking {$ref} (ID: {$id})");

    setFlash('success', "Record {$ref} has been permanently deleted.");
    header('Location: ' . url("admin/{$type}_bookings.php"));
    exit();
} catch (Exception $e) {
    appLog('BOOKING_DELETE_ERROR', ['error' => $e->getMessage()]);
    setFlash('error', 'Unable to delete booking record due to database constraints.');
    header('Location: ' . url("admin/{$type}_bookings.php"));
    exit();
}
