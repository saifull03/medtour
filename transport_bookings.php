<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: transport_bookings.php -> patient/bookings.php?service=transport
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('patient/bookings.php?service=transport') . (str_contains('patient/bookings.php?service=transport', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
