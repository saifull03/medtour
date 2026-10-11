<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: visa_bookings.php -> patient/bookings.php?service=visa
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('patient/bookings.php?service=visa') . (str_contains('patient/bookings.php?service=visa', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
