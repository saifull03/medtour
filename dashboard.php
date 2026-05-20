<?php
session_start();

// Smart redirect based on session role
if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
    header("Location: welcome_admin.php");
    exit();
} elseif (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? '';
    if ($role === 'patient') {
        header("Location: welcome.php");
        exit();
    } elseif ($role === 'doctor') {
        header("Location: welcome_doctor.php");
        exit();
    }
}

// Not logged in — redirect to home landing page
header("Location: index.php");
exit();
