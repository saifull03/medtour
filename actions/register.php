<?php
/**
 * Action: User Registration
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
    header('Location: ' . url('public/index.php'));
    exit();
}

requireCsrfToken();

$role = trim($_POST['role'] ?? ROLE_PATIENT);
$isAdminCreation = false;

// Security constraint: Prevent public registration of admin accounts
if ($role === ROLE_ADMIN) {
    $currentUser = currentUser();
    if (!$currentUser || $currentUser['role'] !== ROLE_ADMIN) {
        setFlash('error', 'Administrator accounts cannot be registered publicly. Only existing administrators can create admin accounts.');
        header('Location: ' . url('public/login_admin.php'));
        exit();
    }
    $isAdminCreation = true;
}

$validation = validateRegistration($_POST, $isAdminCreation);

$redirectTarget = match ($role) {
    ROLE_DOCTOR => url('public/signup_doctor.php'),
    ROLE_ADMIN  => url('public/signup_admin.php'),
    default     => url('public/signup_user.php'),
};

if (!$validation['isValid']) {
    setFormFlash($_POST, $validation['errors']);
    setFlash('error', 'Please resolve the highlighted validation errors below.');
    header('Location: ' . $redirectTarget);
    exit();
}

$name     = trim($_POST['name'] ?? ($_POST['username'] ?? ''));
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$pdo = getDbConnection();

try {
    $pdo->beginTransaction();

    // 1. Insert into users table
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
    $stmt->execute([$name, $email, $hashedPassword, $role]);
    $userId = (int)$pdo->lastInsertId();

    // 2. Insert role-specific profile in the same transaction
    if ($role === ROLE_PATIENT) {
        $phone       = trim($_POST['phone'] ?? '');
        $country     = trim($_POST['country'] ?? '');
        $passportNo  = trim($_POST['passport_no'] ?? '');

        $stmtPat = $pdo->prepare("INSERT INTO patients (user_id, phone, country, passport_no, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmtPat->execute([$userId, $phone ?: null, $country ?: null, $passportNo ?: null]);
    } elseif ($role === ROLE_DOCTOR) {
        $specialization = trim($_POST['specialization'] ?? 'General Medicine');
        $experience     = (int)($_POST['experience'] ?? 0);
        $fee            = (float)($_POST['consultation_fee'] ?? 0.00);
        $hospitalId     = !empty($_POST['hospital_id']) ? (int)$_POST['hospital_id'] : null;

        $stmtDoc = $pdo->prepare("INSERT INTO doctors (user_id, hospital_id, specialization, experience, consultation_fee, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmtDoc->execute([$userId, $hospitalId, $specialization, $experience, $fee]);
    }

    $pdo->commit();

    appLog('REGISTER_SUCCESS', ['user_id' => $userId, 'email' => $email, 'role' => $role]);

    $loginTarget = match ($role) {
        ROLE_DOCTOR => url('public/login_doctor.php'),
        ROLE_ADMIN  => url('public/login_admin.php'),
        default     => url('public/login_user.php'),
    };

    setFlash('success', 'Registration successful! You may now sign in with your credentials.');
    header('Location: ' . $loginTarget);
    exit();
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    appLog('REGISTER_EXCEPTION', ['error' => $e->getMessage()]);
    setFormFlash($_POST, ['general' => 'A database error occurred during registration.']);
    setFlash('error', 'Unable to complete registration. Please try again.');
    header('Location: ' . $redirectTarget);
    exit();
}
