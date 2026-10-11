<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: submit_hospital.php -> patient/hospital.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('patient/hospital.php') . (str_contains('patient/hospital.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
