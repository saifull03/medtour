<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: login_admin.php -> public/login_admin.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('public/login_admin.php') . (str_contains('public/login_admin.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
