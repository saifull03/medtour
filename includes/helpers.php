<?php
/**
 * Application Helpers & Logging
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Escapes string safely for HTML output (prevents XSS).
 *
 * @param mixed $value
 * @return string
 */
function e(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Resolves application relative URL based on script location.
 *
 * @param string $path
 * @return string
 */
function url(string $path = ''): string
{
    // Determine base URL dynamically
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    // Look for /medtour/ in script name
    $pos = strpos($scriptName, '/medtour/');
    $base = ($pos !== false) ? '/medtour' : '';
    
    $cleanPath = ltrim($path, '/');
    return $base . ($cleanPath ? '/' . $cleanPath : '');
}

/**
 * Formats date into readable string.
 *
 * @param string|null $date
 * @param string $format
 * @return string
 */
function formatDate(?string $date, string $format = 'M d, Y'): string
{
    if (empty($date)) {
        return '—';
    }
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : e($date);
}

/**
 * Formats datetime into readable string.
 *
 * @param string|null $datetime
 * @param string $format
 * @return string
 */
function formatDateTime(?string $datetime, string $format = 'M d, Y g:i A'): string
{
    if (empty($datetime)) {
        return '—';
    }
    $ts = strtotime($datetime);
    return $ts ? date($format, $ts) : e($datetime);
}

/**
 * Returns formatted status badge HTML with accessible attributes.
 *
 * @param string $status
 * @return string
 */
function renderStatusBadge(string $status): string
{
    $statusLower = strtolower(trim($status));
    $classes = match($statusLower) {
        'pending'   => 'badge badge-warning',
        'approved', 'confirmed' => 'badge badge-success',
        'completed' => 'badge badge-info',
        'cancelled', 'rejected' => 'badge badge-danger',
        default     => 'badge badge-secondary',
    };

    return sprintf(
        '<span class="%s" aria-label="Status: %s">%s</span>',
        $classes,
        e(ucfirst($statusLower)),
        e(ucfirst($statusLower))
    );
}

/**
 * Generates human-readable reference number.
 *
 * @param string $prefix
 * @param int $id
 * @return string
 */
function generateReferenceNumber(string $prefix, int $id): string
{
    return sprintf('MED-%s-%05d', strtoupper($prefix), $id);
}

/**
 * General Application Logger
 * Logs authentication events, booking creations, errors, etc.
 * Sensitive data (passwords, passport numbers) is strictly stripped.
 *
 * @param string $action
 * @param array $context
 */
function appLog(string $action, array $context = []): void
{
    $logDir = LOGS_PATH;
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    // Sanitize context: remove sensitive information
    $sanitized = [];
    $sensitiveKeys = ['password', 'confirm_password', 'passport_no', 'passport_number', 'credit_card', 'cvv'];
    foreach ($context as $key => $val) {
        if (in_array(strtolower($key), $sensitiveKeys, true)) {
            $sanitized[$key] = '[REDACTED]';
        } else {
            $sanitized[$key] = is_scalar($val) ? $val : json_encode($val);
        }
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $userId = $_SESSION['user']['id'] ?? ($_SESSION['user_id'] ?? 'Guest');
    $userRole = $_SESSION['user']['role'] ?? ($_SESSION['role'] ?? 'Guest');

    $line = sprintf(
        "[%s] [%s] User:%s Role:%s Action:%s Details:%s\n",
        date('Y-m-d H:i:s'),
        $ip,
        $userId,
        $userRole,
        $action,
        json_encode($sanitized)
    );

    @file_put_contents($logDir . '/app.log', $line, FILE_APPEND);
}

/**
 * Administrator Audit Logger
 * Logs administrator actions into database admin_logs and file log.
 *
 * @param int $adminId
 * @param string $action
 */
function adminLog(int $adminId, string $action): void
{
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$adminId, $action]);
    } catch (Exception $e) {
        // Fallback to file logging if DB log fails
        appLog('ADMIN_LOG_DB_ERROR', ['error' => $e->getMessage()]);
    }
    appLog('ADMIN_ACTION', ['admin_id' => $adminId, 'action' => $action]);
}

/**
 * Checks if status transition is allowed for a user role.
 *
 * @param string $role
 * @param string $currentStatus
 * @param string $targetStatus
 * @return bool
 */
function isStatusTransitionAllowed(string $role, string $currentStatus, string $targetStatus): bool
{
    $current = strtolower($currentStatus);
    $target  = strtolower($targetStatus);

    if ($current === $target) {
        return true;
    }

    if ($role === ROLE_PATIENT) {
        // Patients can only transition pending to cancelled
        return ($current === 'pending' && $target === 'cancelled');
    }

    if ($role === ROLE_DOCTOR) {
        // Doctors can approve pending, complete approved, or cancel pending
        if ($current === 'pending') {
            return in_array($target, ['approved', 'cancelled'], true);
        }
        if ($current === 'approved') {
            return ($target === 'completed');
        }
        return false;
    }

    if ($role === ROLE_ADMIN) {
        // Admin can perform valid lifecycle transitions
        $allowed = [
            'pending'   => ['approved', 'confirmed', 'rejected', 'cancelled'],
            'approved'  => ['completed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled'],
            'rejected'  => [],
            'completed' => [],
            'cancelled' => [],
        ];
        return in_array($target, $allowed[$current] ?? [], true);
    }

    return false;
}
