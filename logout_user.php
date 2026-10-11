<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: logout_user.php -> public/logout.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('public/logout.php') . (str_contains('public/logout.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
