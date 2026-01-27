<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Hotel Bookings - Medical Tourism Service</title>
    <style>
        /* Your CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('hotel.png');
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
            width: 80%;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        .update-button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .update-button:hover {
            background-color: #45a049;
        }
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-form input[type="submit"] {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form input[type="submit"]:hover {
            background-color: #0056b3;
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
        <h2>Hotel Bookings</h2>
        <div class="search-form">
            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="text" name="patient_id" placeholder="Enter Patient ID" value="<?php echo isset($_GET['patient_id']) ? $_GET['patient_id'] : ''; ?>">
                <input type="submit" value="Search">
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Hotel Name</th>
                        <th>Check-in Date</th>
                        <th>Check-in Time</th>
                        <th>Check-out Date</th>
                        <th>Number of Guests</th>
                        <th>Room Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database connection
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "mt_db";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Initialize search variable
                    $search_patient_id = "";

                    // Check if search form is submitted
                    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["patient_id"])) {
                        $search_patient_id = $_GET["patient_id"];
                    }

                    // Fetch data from the database
                    $sql = "SELECT * FROM hotel_bookings";

                    // If search query is provided, add WHERE clause
                    if (!empty($search_patient_id)) {
                        $sql .= " WHERE patient_id = '$search_patient_id'";
                    }

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row['patient_id']."</td>";
                            echo "<td>".$row['hotel_name']."</td>";
                            echo "<td>".$row['checkin_date']."</td>";
                            echo "<td>".$row['checkin_time']."</td>";
                            echo "<td>".$row['checkout_date']."</td>";
                            echo "<td>".$row['num_guests']."</td>";
                            echo "<td>".$row['room_type']."</td>";
                            echo "<td><button class='update-button' onclick='updateBooking(".$row['id'].")'>Update</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No hotel bookings found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function updateBooking(id) {
            // Redirect to the update page with the ID
            window.location.href = "update_hotel_booking.php?id=" + id;
        }
    </script>
</body>
</html>
