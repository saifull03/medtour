<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: submit_hotel.php -> patient/hotel.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('patient/hotel.php') . (str_contains('patient/hotel.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
