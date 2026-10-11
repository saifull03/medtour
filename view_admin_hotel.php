<?php
/**
 * Backwards Compatibility Redirect
 * Legacy URL: view_admin_hotel.php -> admin/hotel_bookings.php
 */
require_once __DIR__ . '/includes/helpers.php';
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: " . url('admin/hotel_bookings.php') . (str_contains('admin/hotel_bookings.php', '?') && $qs ? str_replace('?', '&', $qs) : $qs), true, 302);
exit();
