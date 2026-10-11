<?php
/**
 * Database Migration Runner & Verification
 */
$conn = new mysqli('localhost', 'root', '', 'mt_db');
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error . "\n");
}
$conn->set_charset("utf8mb4");

// Check admin user
$res = $conn->query("SELECT id, name, email, role, password FROM users WHERE email = 'admin@medtour.com'");
$admin = $res ? $res->fetch_assoc() : null;

if (!$admin) {
    $hash = password_hash('Admin@123456', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 1)");
    $name = 'System Administrator';
    $email = 'admin@medtour.com';
    $stmt->bind_param("sss", $name, $email, $hash);
    $stmt->execute();
    echo "Admin created successfully!\n";
} else {
    // Ensure password is valid
    if (!password_verify('Admin@123456', $admin['password'])) {
        $hash = password_hash('Admin@123456', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $admin['id']);
        $stmt->execute();
        echo "Admin password updated to Admin@123456!\n";
    } else {
        echo "Admin already exists and password verified!\n";
    }
}

// Verify tables and columns
$tables = ['hotels', 'appointments', 'hotel_bookings', 'transport_bookings', 'visa_bookings'];
foreach ($tables as $t) {
    $c = $conn->query("SELECT COUNT(*) as cnt FROM `$t`")->fetch_assoc()['cnt'];
    echo "Table $t rows: $c\n";
}

$conn->close();
