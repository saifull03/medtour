<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: login_user.php -> public/login_user.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('public/login_user.php') . (str_contains('public/login_user.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
