<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login_doctor.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mt_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get doctor record ID from doctors table using user_id
$user_id = $_SESSION['user_id'];
$doctor_id = 0;
$doc_query = "SELECT id FROM doctors WHERE user_id = ?";
$stmt = $conn->prepare($doc_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$doc_result = $stmt->get_result();
if ($doc_result->num_rows > 0) {
    $doctor_row = $doc_result->fetch_assoc();
    $doctor_id = $doctor_row['id'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Appointments - Medical Tourism Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('background.png');
            background-size: cover;
            background-position: center;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo img {
            height: 50px;
            margin-right: 10px;
        }
        .name h1 {
            margin: 0;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            margin-right: 20px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }
        nav ul li a:hover {
            text-decoration: underline;
        }
        .container {
            margin: 50px auto;
            width: 90%;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-approved {
            color: green;
            font-weight: bold;
        }
        .status-completed {
            color: blue;
            font-weight: bold;
        }
        .status-cancelled {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <img src="logo.png" alt="Medical Tourism Service Logo">
        </div>
        <div class="name">
            <h1>Medical Tourism Service</h1>
        </div>
        <nav>
            <ul>
                <li><a href="welcome_doctor.php">Dashboard</a></li>
                <li><a href="view_appointments.php">View Appointments</a></li>
                <li><a href="help.php">Help</a></li>
                <li><a href="logout_doctor.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Your Booked Appointments</h2>

        <?php
        // SQL query to retrieve appointment information for this doctor
        $sql = "SELECT a.id, a.patient_id, a.appointment_date, a.status, u.name as patient_name, u.email as patient_email, h.name as hospital_name
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN hospitals h ON a.hospital_id = h.id
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<table>
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Patient Email</th>
                            <th>Hospital</th>
                            <th>Appointment Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($row = $result->fetch_assoc()) {
                $status_class = "status-" . strtolower($row["status"]);
                echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["patient_id"] . "</td>
                        <td>" . htmlspecialchars($row["patient_name"]) . "</td>
                        <td>" . htmlspecialchars($row["patient_email"]) . "</td>
                        <td>" . htmlspecialchars($row["hospital_name"]) . "</td>
                        <td>" . $row["appointment_date"] . "</td>
                        <td class='" . $status_class . "'>" . ucfirst($row["status"]) . "</td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No appointments scheduled for you at this time.</p>";
        }
        
        $stmt->close();
        $conn->close();
        ?>
    </div>

</body>
</html>
