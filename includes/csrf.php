<?php
/**
 * CSRF Protection
 * MedTour Services
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generates and returns a CSRF token for the current session.
 *
 * @return string
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Returns an HTML hidden input containing the CSRF token.
 *
 * @return string
 */
function csrfField(): string
{
    $token = htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Validates the submitted CSRF token from POST requests.
 *
 * @return bool
 */
function validateCsrfToken(): bool
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }

    $submitted = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (empty($submitted) || empty($sessionToken)) {
        return false;
    }

    return hash_equals($sessionToken, $submitted);
}

/**
 * Enforces CSRF token validation or terminates request with 403 Forbidden.
 */
function requireCsrfToken(): void
{
    if (!validateCsrfToken()) {
        http_response_code(403);
        require_once __DIR__ . '/flash.php';
        setFlash('error', 'Invalid or expired session security token. Please try again.');
        $referer = $_SERVER['HTTP_REFERER'] ?? '../public/index.php';
        header("Location: " . $referer);
        exit();
    }
}
