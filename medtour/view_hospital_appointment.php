<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Hospital Appointments</title>
    <style>
        /* Your CSS styles */
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>View Hospital Appointments</h2>

    <table>
        <tr>
            <th>Patient ID</th>
            <th>Doctor Name</th>
            <th>Hospital Name</th>
            <th>Appointment Time</th>
            <th>Appointment Date</th>
            <th>Cancer Type</th>
        </tr>
        <?php
            // Connect to the database
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "mt_db";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch appointments from the database
            $sql = "SELECT * FROM hospital_appointments";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["patient_id"] . "</td>";
                    echo "<td>" . $row["doctor_name"] . "</td>";
                    echo "<td>" . $row["hospital_name"] . "</td>";
                    echo "<td>" . $row["appointment_time"] . "</td>";
                    echo "<td>" . $row["appointment_date"] . "</td>";
                    echo "<td>" . $row["cancer_type"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No appointments found</td></tr>";
            }

            $conn->close();
        ?>
    </table>
</body>
</html>
