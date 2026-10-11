<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: submit_visa.php -> patient/visa.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('patient/visa.php') . (str_contains('patient/visa.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
