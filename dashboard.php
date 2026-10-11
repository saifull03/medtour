<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: dashboard.php -> patient/dashboard.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('patient/dashboard.php') . (str_contains('patient/dashboard.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
