<?php
/**
 * Input Validation & Sanitization
 * MedTour Services
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Validates email format using filter_var.
 *
 * @param string $email
 * @return bool
 */
function isValidEmail(string $email): bool
{
    return (bool)filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}

/**
 * Validates password strength:
 * Minimum 8 characters, at least 1 lowercase, 1 uppercase, 1 digit.
 *
 * @param string $password
 * @return bool
 */
function isStrongPassword(string $password): bool
{
    if (strlen($password) < 8) {
        return false;
    }
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    return true;
}

/**
 * Checks if email already exists in users table.
 *
 * @param string $email
 * @param int|null $excludeUserId
 * @return bool
 */
function isEmailTaken(string $email, ?int $excludeUserId = null): bool
{
    $pdo = getDbConnection();
    if ($excludeUserId) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $stmt->execute([trim($email), $excludeUserId]);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([trim($email)]);
    }
    return ((int)$stmt->fetchColumn()) > 0;
}

/**
 * Validates registration data.
 *
 * @param array $data
 * @param bool $allowAdminCreation
 * @return array [isValid => bool, errors => array]
 */
function validateRegistration(array $data, bool $allowAdminCreation = false): array
{
    $errors = [];
    $name = trim($data['name'] ?? ($data['username'] ?? ''));
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';
    $role = trim($data['role'] ?? 'patient');

    // Name validation
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    } elseif (strlen($name) < 2 || strlen($name) > 100) {
        $errors['name'] = 'Name must be between 2 and 100 characters.';
    }

    // Email validation
    if (empty($email)) {
        $errors['email'] = 'Email address is required.';
    } elseif (!isValidEmail($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (isEmailTaken($email)) {
        $errors['email'] = 'This email address is already registered.';
    }

    // Password validation
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (!isStrongPassword($password)) {
        $errors['password'] = 'Password must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, and one number.';
    }

    // Password confirmation
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Role validation
    $allowedRoles = [ROLE_PATIENT, ROLE_DOCTOR];
    if ($allowAdminCreation) {
        $allowedRoles[] = ROLE_ADMIN;
    }
    if (!in_array($role, $allowedRoles, true)) {
        $errors['role'] = 'Invalid account role selected.';
    }

    // Doctor-specific fields
    if ($role === ROLE_DOCTOR) {
        $specialization = trim($data['specialization'] ?? '');
        $experience = filter_var($data['experience'] ?? '', FILTER_VALIDATE_INT);
        $fee = filter_var($data['consultation_fee'] ?? '', FILTER_VALIDATE_FLOAT);

        if (empty($specialization)) {
            $errors['specialization'] = 'Medical specialization is required.';
        }
        if ($experience === false || $experience < 0 || $experience > 60) {
            $errors['experience'] = 'Please provide valid years of medical experience (0-60).';
        }
        if ($fee === false || $fee < 0) {
            $errors['consultation_fee'] = 'Please enter a valid consultation fee.';
        }
    }

    return [
        'isValid' => empty($errors),
        'errors'  => $errors,
    ];
}

/**
 * Validates date is not in the past (comparison against today's date).
 *
 * @param string $date
 * @return bool
 */
function isDateNotInPast(string $date): bool
{
    $ts = strtotime($date);
    if (!$ts) {
        return false;
    }
    $today = strtotime('today');
    return ($ts >= $today);
}

/**
 * Validates passport number format (alphanumeric 5-20 characters).
 *
 * @param string $passport
 * @return bool
 */
function isValidPassport(string $passport): bool
{
    $clean = trim($passport);
    return (bool)preg_match('/^[A-Z0-9]{5,20}$/i', $clean);
}
