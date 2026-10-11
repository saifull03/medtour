<?php
/**
 * Action: User Authentication (Login)
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

requireCsrfToken();

$identifier = trim($_POST['identifier'] ?? ($_POST['username'] ?? ''));
$password   = $_POST['password'] ?? '';
$role       = trim($_POST['role'] ?? ROLE_PATIENT);
$redirectMap = [
    ROLE_PATIENT => url('public/login_user.php'),
    ROLE_DOCTOR  => url('public/login_doctor.php'),
    ROLE_ADMIN   => url('public/login_admin.php'),
];
$fallbackRedirect = $redirectMap[$role] ?? url('public/login_user.php');

if (empty($identifier) || empty($password)) {
    setFlash('error', 'Please provide both credentials to sign in.');
    header('Location: ' . $fallbackRedirect);
    exit();
}

try {
    $pdo = getDbConnection();
    // Support login by either email or username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR name = ?) AND role = ? LIMIT 1");
    $stmt->execute([$identifier, $identifier, $role]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ((int)$user['status'] !== 1) {
            setFlash('error', 'This account has been deactivated. Please contact administration.');
            header('Location: ' . $fallbackRedirect);
            exit();
        }

        loginUser($user);
        setFlash('success', 'Welcome back, ' . e($user['name']) . '!');
        redirectByRole($user['role']);
    } else {
        appLog('LOGIN_FAILED', ['identifier' => $identifier, 'role' => $role]);
        setFlash('error', 'Invalid credentials. Please verify your login details.');
        header('Location: ' . $fallbackRedirect);
        exit();
    }
} catch (Exception $e) {
    appLog('LOGIN_EXCEPTION', ['error' => $e->getMessage()]);
    setFlash('error', 'A secure server error occurred. Please try again.');
    header('Location: ' . $fallbackRedirect);
    exit();
}
