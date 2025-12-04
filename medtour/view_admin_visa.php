<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Assistance Details - Medical Tourism Service</title>
    <style>
        /* Your CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('visa.png');
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
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
        }
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .search-form button {
            padding: 8px 16px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #555;
        }
        /* Styling for Update button */
        .update-button {
            padding: 8px 16px;
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none; /* Remove underline */
        }
        .update-button:hover {
            background-color: #45a049; /* Darker green */
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
        <h2>Visa Assistance Details</h2>
        <div class="search-form">
            <form method="GET">
                <label for="search_patient_id">Search Patient ID:</label>
                <input type="text" id="search_patient_id" name="search_patient_id" value="<?php echo isset($_GET['search_patient_id']) ? $_GET['search_patient_id'] : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>Passport Number</th>
                        <th>Visa Type</th>
                        <th>Purpose of Visit</th>
                        <th>Appointment Date</th>
                        <th>Action</th> <!-- Added Update Button Column -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Connect to the database
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "mt_db";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Search for specific patient ID if provided
                    $search_patient_id = isset($_GET['search_patient_id']) ? $_GET['search_patient_id'] : '';
                    $sql = "SELECT * FROM visa_assistance";
                    if (!empty($search_patient_id)) {
                        $sql .= " WHERE patient_id LIKE '%$search_patient_id%'";
                    }
                    
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["patient_id"] . "</td>";
                            echo "<td>" . $row["name"] . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>" . $row["country"] . "</td>";
                            echo "<td>" . $row["passport_number"] . "</td>";
                            echo "<td>" . $row["visa_type"] . "</td>";
                            echo "<td>" . $row["purpose"] . "</td>";
                            echo "<td>" . $row["appointment_date"] . "</td>";
                            echo "<td><a class='update-button' href='update.php?patient_id=" . $row["patient_id"] . "'>Update</a></td>"; // Added Update Button with class
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No data available</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
