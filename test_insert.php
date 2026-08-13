<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli('localhost','root','','mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$stmt = $conn->prepare("INSERT INTO appointments (patient_id,doctor_id,appointment_date,hospital_id,status) VALUES (?,?,?,?,'pending')");
$patient_id = 1;
$doctor_id = 1;
$hospital_id = 1;
$appointment_date = '2026-06-01';
$stmt->bind_param("iisi", $patient_id, $doctor_id, $appointment_date, $hospital_id);
if ($stmt->execute()) {
    echo "SUCCESS: " . $conn->insert_id;
} else {
    echo "ERROR: " . $stmt->error;
}
$stmt->close();
$conn->close();
