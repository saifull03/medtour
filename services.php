<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: services.php -> public/services.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('public/services.php') . (str_contains('public/services.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
