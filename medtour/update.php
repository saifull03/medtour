<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Transport Booking - Medical Tourism Service</title>
    <style>
        /* Your CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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
        }
        .form-container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group select,
        .form-group input[type="time"],
        .form-group input[type="date"],
        .form-group input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .submit-button {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-button:hover {
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
        <div class="form-container">
            <h2>Update Transport Booking</h2>
            <?php
            // Check if ID is provided in the URL
            if(isset($_GET['id'])) {
                // Connect to the database
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "mt_db";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Retrieve data based on ID
                $id = $_GET['id'];
                $sql = "SELECT * FROM transport_bookings WHERE id = $id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
            ?>
            <form action="update_process.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <div class="form-group">
                    <label for="patient_id">Patient ID:</label>
                    <input type="text" id="patient_id" name="patient_id" value="<?php echo $row['patient_id']; ?>" required disabled>
                </div>
                <div class="form-group">
                    <label for="transport_type">Transport Type:</label>
                    <select id="transport_type" name="transport_type" required>
                        <option value="Taxi" <?php if($row['transport_type'] == 'Taxi') echo 'selected'; ?>>Taxi</option>
                        <option value="Bus" <?php if($row['transport_type'] == 'Bus') echo 'selected'; ?>>Bus</option>
                        <option value="Train" <?php if($row['transport_type'] == 'Train') echo 'selected'; ?>>Train</option>
                        <!-- Add more transport types as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="pickup_location">Pickup Location:</label>
                    <input type="text" id="pickup_location" name="pickup_location" value="<?php echo $row['pickup_location']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="destination">Destination:</label>
                    <input type="text" id="destination" name="destination" value="<?php echo $row['destination']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" value="<?php echo $row['date']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" value="<?php echo $row['time']; ?>" required>
                </div>
                <button type="submit" class="submit-button">Update Booking</button>
            </form>
            <?php
                } else {
                    echo "No booking found with the provided ID.";
                }
                $conn->close();
            } else {
                echo "ID parameter is missing in the URL.";
            }
            ?>
        </div>
    </div>
</body>
</html>
