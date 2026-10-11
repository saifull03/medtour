<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: signup_doctor.php -> public/signup_doctor.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('public/signup_doctor.php') . (str_contains('public/signup_doctor.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
