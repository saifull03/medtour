<?php
/**
 * Authentication and Authorization Manager
 * MedTour Services
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/helpers.php';

/**
 * Returns currently authenticated user array or null.
 *
 * @return array|null
 */
function currentUser(): ?array
{
    if (empty($_SESSION['user']['id'])) {
        return null;
    }
    return $_SESSION['user'];
}

/**
 * Verifies if user is logged in.
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    return currentUser() !== null;
}

/**
 * Requires an authenticated user session.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to access this page.');
        header('Location: ' . url('public/login_user.php'));
        exit();
    }

    // Verify account is still active in database
    verifyAccountActive();
}

/**
 * Requires patient role.
 */
function requirePatient(): void
{
    requireLogin();
    $user = currentUser();
    if ($user['role'] !== ROLE_PATIENT) {
        setFlash('error', 'Access denied. Patient area only.');
        redirectByRole($user['role']);
    }
}

/**
 * Requires doctor role.
 */
function requireDoctor(): void
{
    requireLogin();
    $user = currentUser();
    if ($user['role'] !== ROLE_DOCTOR) {
        setFlash('error', 'Access denied. Doctor area only.');
        redirectByRole($user['role']);
    }
}

/**
 * Requires admin role.
 */
function requireAdmin(): void
{
    requireLogin();
    $user = currentUser();
    if ($user['role'] !== ROLE_ADMIN) {
        setFlash('error', 'Access denied. Administrator privileges required.');
        redirectByRole($user['role']);
    }
}

/**
 * Redirects user to their role-specific dashboard.
 *
 * @param string|null $role
 */
function redirectByRole(?string $role = null): void
{
    if ($role === null) {
        $user = currentUser();
        $role = $user['role'] ?? null;
    }

    match ($role) {
        ROLE_PATIENT => header('Location: ' . url('patient/dashboard.php')),
        ROLE_DOCTOR  => header('Location: ' . url('doctor/dashboard.php')),
        ROLE_ADMIN   => header('Location: ' . url('admin/dashboard.php')),
        default      => header('Location: ' . url('public/index.php')),
    };
    exit();
}

/**
 * Sets session data and regenerates session ID upon successful login.
 *
 * @param array $user User row from users table
 */
function loginUser(array $user): void
{
    session_regenerate_id(true);

    $pdo = getDbConnection();
    $patientId = null;
    $doctorId = null;

    if ($user['role'] === ROLE_PATIENT) {
        $stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $row = $stmt->fetch();
        if ($row) {
            $patientId = (int)$row['id'];
        } else {
            // Guarantee patient profile row exists
            $stmt = $pdo->prepare("INSERT INTO patients (user_id) VALUES (?)");
            $stmt->execute([$user['id']]);
            $patientId = (int)$pdo->lastInsertId();
        }
    } elseif ($user['role'] === ROLE_DOCTOR) {
        $stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $row = $stmt->fetch();
        if ($row) {
            $doctorId = (int)$row['id'];
        }
    }

    // Unified session format
    $_SESSION['user'] = [
        'id'         => (int)$user['id'],
        'name'       => $user['name'],
        'email'      => $user['email'],
        'role'       => $user['role'],
        'status'     => (int)($user['status'] ?? 1),
        'patient_id' => $patientId,
        'doctor_id'  => $doctorId,
    ];

    // Compatibility variables for legacy scripts
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['role']    = $user['role'];
    if ($user['role'] === ROLE_ADMIN) {
        $_SESSION['admin_id']   = (int)$user['id'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_role'] = $user['role'];
    }

    appLog('LOGIN_SUCCESS', [
        'user_id' => $user['id'],
        'email'   => $user['email'],
        'role'    => $user['role'],
    ]);
}

/**
 * Checks database to ensure current user account is still active.
 */
function verifyAccountActive(): void
{
    $userId = $_SESSION['user']['id'] ?? 0;
    if ($userId <= 0) {
        return;
    }

    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $status = $stmt->fetchColumn();

        if ($status === false || (int)$status !== 1) {
            logoutUser();
            setFlash('error', 'Your account has been deactivated. Please contact support.');
            header('Location: ' . url('public/login_user.php'));
            exit();
        }
    } catch (Exception $e) {
        // If DB check fails, log and continue safely
        appLog('STATUS_VERIFY_ERROR', ['error' => $e->getMessage()]);
    }
}

/**
 * Resolves patient ID for the currently logged in patient.
 *
 * @return int
 */
function currentPatientId(): int
{
    requirePatient();
    $user = currentUser();
    if (!empty($user['patient_id'])) {
        return (int)$user['patient_id'];
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $patientId = $stmt->fetchColumn();

    if (!$patientId) {
        $stmt = $pdo->prepare("INSERT INTO patients (user_id) VALUES (?)");
        $stmt->execute([$user['id']]);
        $patientId = (int)$pdo->lastInsertId();
    }

    $_SESSION['user']['patient_id'] = (int)$patientId;
    return (int)$patientId;
}

/**
 * Resolves doctor ID for the currently logged in doctor.
 *
 * @return int
 */
function currentDoctorId(): int
{
    requireDoctor();
    $user = currentUser();
    if (!empty($user['doctor_id'])) {
        return (int)$user['doctor_id'];
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $doctorId = (int)$stmt->fetchColumn();

    $_SESSION['user']['doctor_id'] = $doctorId;
    return $doctorId;
}

/**
 * Clears user session and cookies completely.
 */
function logoutUser(): void
{
    $user = currentUser();
    if ($user) {
        appLog('LOGOUT', ['user_id' => $user['id'], 'role' => $user['role']]);
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}
