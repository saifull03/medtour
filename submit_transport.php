<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: submit_transport.php -> patient/transport.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('patient/transport.php') . (str_contains('patient/transport.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
