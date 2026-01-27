<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Appointments</title>
    <style>
        /* Your CSS styles */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mt_db";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve appointment information
$sql = "SELECT v.patient_id, 
               v.appointment_datetime AS visa_datetime, 
               t.appointment_datetime AS transport_datetime, 
               h.appointment_datetime AS hotel_datetime, 
               hos.appointment_datetime AS hospital_datetime
        FROM visa_assistance v
        LEFT JOIN transport_bookings t ON v.patient_id = t.patient_id
        LEFT JOIN hotel_bookings h ON v.patient_id = h.patient_id
        LEFT JOIN hospital_appointments hos ON v.patient_id = hos.patient_id
        WHERE v.patient_id = 3";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<table>
            <tr>
                <th>Patient ID</th>
                <th>Visa Date & Time</th>
                <th>Transport Date & Time</th>
                <th>Hotel Date & Time</th>
                <th>Hospital Date & Time</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["patient_id"] . "</td>
                <td>" . $row["visa_datetime"] . "</td>
                <td>" . $row["transport_datetime"] . "</td>
                <td>" . $row["hotel_datetime"] . "</td>
                <td>" . $row["hospital_datetime"] . "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "No appointments found for patient ID 3";
}

$conn->close();
?>

</body>
</html>
