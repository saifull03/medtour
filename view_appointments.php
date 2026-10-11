<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: view_appointments.php -> doctor/appointments.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('doctor/appointments.php') . (str_contains('doctor/appointments.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
