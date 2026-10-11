<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: update.php -> admin/update_booking.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('admin/update_booking.php') . (str_contains('admin/update_booking.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
