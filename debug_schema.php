<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$conn = new mysqli('localhost','root','','mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
echo "<pre>";
foreach (['appointments','doctors','hospitals','patients','users'] as $t) {
    echo "\n=== $t ===\n";
    $r = $conn->query("DESCRIBE $t");
    while ($row = $r->fetch_assoc()) {
        echo "  " . implode(" | ", $row) . "\n";
    }
}
echo "\n=== doctors count: " . $conn->query("SELECT COUNT(*) as c FROM doctors")->fetch_assoc()['c'];
echo "\n=== hospitals count: " . $conn->query("SELECT COUNT(*) as c FROM hospitals")->fetch_assoc()['c'];
echo "\n=== patients count: " . $conn->query("SELECT COUNT(*) as c FROM patients")->fetch_assoc()['c'];
echo "</pre>";
