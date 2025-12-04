<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Bookings - Medical Tourism Service</title>
    <style>
        /* Your CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('transport_bookings.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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
            width: 80%;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: #fff;
        }
        .btn-update, .btn-delete {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn-delete {
            background-color: #f44336;
        }
        .print-button {
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 20px;
        }
        .print-button:hover {
            background-color: #555;
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
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="login_admin.php">Admin</a></li>
                <li><a href="login_user.php">User</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Transport Bookings</h2>
        <table>
            <tr>
                <th>Patient ID</th>
                <th>Transport Type</th>
                <th>Pickup Location</th>
                <th>Destination</th>
                <th>Date</th>
                <th>Time</th>
                <th>Actions</th>
                <th>Delete</th>
            </tr>
            <?php
            // Retrieve and display the inserted data
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "mt_db";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM transport_bookings";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['patient_id'] . "</td>";
                    echo "<td>" . $row['transport_type'] . "</td>";
                    echo "<td>" . $row['pickup_location'] . "</td>";
                    echo "<td>" . $row['destination'] . "</td>";
                    echo "<td>" . $row['date'] . "</td>";
                    echo "<td>" . $row['time'] . "</td>";
                    
                    echo "<td><button class='btn-update' onclick='updateRow(" . $row['id'] . ")'>Update</button></td>";
                    echo "<td><button class='btn-delete' onclick='deleteRow(" . $row['id'] . ")'>Delete</button></td?";
                    
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No bookings found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
        
        <button class="print-button" onclick="window.print()">Print</button>
    </div>

    <script>
        function updateRow(id) {
            // Redirect to update page with the ID parameter
            window.location.href = "update.php?id=" + id;
        }

        function deleteRow(id) {
            // Confirm deletion and then redirect to delete page with the ID parameter
            if (confirm("Are you sure you want to delete this booking?")) {
                window.location.href = "delete.php?id=" + id;
            }
        }
    </script>
</body>
</html>
