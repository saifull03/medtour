<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Booking Details - Medical Tourism Service</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('transport.png');
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
        header nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        header nav ul li {
            margin-right: 20px;
        }
        header nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }
        header nav ul li a:hover {
            text-decoration: underline;
        }
        .container {
            margin: 50px auto;
            width: 80%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #333;
            color: #fff;
        }
        tbody tr:hover {
            background-color: #f5f5f5;
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

        /* Search Form Styles */
        form {
            margin-bottom: 20px;
        }

        label {
            margin-right: 10px;
        }

        input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        input[type="submit"] {
            padding: 8px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Medical Tourism Service Logo">
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
        <h2>Transport Booking Details</h2>
        <!-- Search Form -->
        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="patient_id">Search by Patient ID:</label>
            <input type="text" id="patient_id" name="patient_id" value="<?php echo isset($_GET['patient_id']) ? $_GET['patient_id'] : ''; ?>">
            <input type="submit" value="Search">
        </form>

        <table>
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Transport Type</th>
                    <th>Pickup Location</th>
                    <th>Destination</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Action</th> <!-- Added column for action buttons -->
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
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
            </tbody>
        </table>
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
