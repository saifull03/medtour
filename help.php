<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: help.php -> public/help.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('public/help.php') . (str_contains('public/help.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
