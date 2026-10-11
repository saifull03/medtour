<?php
/**
 * Action: Unified User Authentication (Login)
 * MedTour Services
 *
 * Authenticates any user role (patient, doctor, admin) through a common login flow
 * and automatically routes to their respective portal dashboard.
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('public/login.php'));
    exit();
}

requireCsrfToken();

$identifier = trim($_POST['identifier'] ?? ($_POST['username'] ?? ($_POST['email'] ?? '')));
$password   = $_POST['password'] ?? '';
$loginPage  = url('public/login.php');

if (empty($identifier) || empty($password)) {
    setFlash('error', 'Please provide both your email/username and password.');
    header('Location: ' . $loginPage);
    exit();
}

try {
    $pdo = getDbConnection();
    // Common user lookup by email or username (case-insensitive in standard MariaDB/MySQL collation)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR name = ?) LIMIT 1");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ((int)$user['status'] !== 1) {
            setFlash('error', 'This account has been deactivated. Please contact administration.');
            header('Location: ' . $loginPage);
            exit();
        }

        loginUser($user);
        setFlash('success', 'Welcome back, ' . e($user['name']) . '!');
        redirectByRole($user['role']);
    } else {
        appLog('LOGIN_FAILED', ['identifier' => $identifier]);
        setFlash('error', 'Invalid email, username, or password.');
        header('Location: ' . $loginPage);
        exit();
    }
} catch (Exception $e) {
    appLog('LOGIN_EXCEPTION', ['error' => $e->getMessage()]);
    setFlash('error', 'A secure server error occurred. Please try again.');
    header('Location: ' . $loginPage);
    exit();
}
