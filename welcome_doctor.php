<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: welcome_doctor.php -> doctor/dashboard.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('doctor/dashboard.php') . (str_contains('doctor/dashboard.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
