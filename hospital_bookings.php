<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: hospital_bookings.php -> patient/bookings.php?service=hospital
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('patient/bookings.php?service=hospital') . (str_contains('patient/bookings.php?service=hospital', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
