<?php
/**
 * Logout Handler
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';

logoutUser();

// Start clean new session just for the flash message
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
setFlash('info', 'You have been securely signed out.');
header('Location: ' . url('public/index.php'));
exit();
