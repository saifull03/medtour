<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: signup_admin.php -> public/signup_admin.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('public/signup_admin.php') . (str_contains('public/signup_admin.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
