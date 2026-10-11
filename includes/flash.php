<?php
/**
 * Flash Messages & Form Persistence
 * MedTour Services
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sets a flash message.
 *
 * @param string $type ('success', 'error', 'info', 'warning')
 * @param string $message
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash_messages'][] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * Checks if there are any flash messages.
 *
 * @param string|null $type
 * @return bool
 */
function hasFlash(?string $type = null): bool
{
    if (empty($_SESSION['flash_messages'])) {
        return false;
    }
    if ($type === null) {
        return true;
    }
    foreach ($_SESSION['flash_messages'] as $msg) {
        if ($msg['type'] === $type) {
            return true;
        }
    }
    return false;
}

/**
 * Retrieves and clears all flash messages.
 *
 * @return array
 */
function getFlashes(): array
{
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Renders HTML for all pending flash messages.
 *
 * @return string
 */
function renderFlashMessages(): string
{
    $flashes = getFlashes();
    if (empty($flashes)) {
        return '';
    }

    $html = '<div class="flash-messages-container" role="alert">';
    foreach ($flashes as $f) {
        $type = htmlspecialchars($f['type'], ENT_QUOTES, 'UTF-8');
        $text = htmlspecialchars($f['message'], ENT_QUOTES, 'UTF-8');
        $icon = match($f['type']) {
            'success' => '✓',
            'error'   => '⚠️',
            'warning' => '⚡',
            default   => 'ℹ️',
        };
        $html .= sprintf(
            '<div class="flash-message flash-%s"><span class="flash-icon">%s</span><span class="flash-text">%s</span></div>',
            $type,
            $icon,
            $text
        );
    }
    $html .= '</div>';
    return $html;
}

/**
 * Saves form input and field-specific errors for preservation after validation failure.
 *
 * @param array $input
 * @param array $errors
 */
function setFormFlash(array $input, array $errors = []): void
{
    // Never preserve passwords in session
    unset($input['password'], $input['confirm_password'], $input['csrf_token']);
    $_SESSION['form_flash'] = [
        'data'   => $input,
        'errors' => $errors,
    ];
}

/**
 * Retrieves and clears preserved form inputs.
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function old(string $key, mixed $default = '')
{
    return $_SESSION['form_flash']['data'][$key] ?? $default;
}

/**
 * Retrieves field-specific error message if exists.
 *
 * @param string $key
 * @return string
 */
function fieldError(string $key): string
{
    return $_SESSION['form_flash']['errors'][$key] ?? '';
}

/**
 * Clears form flash data after page rendering.
 */
function clearFormFlash(): void
{
    unset($_SESSION['form_flash']);
}
