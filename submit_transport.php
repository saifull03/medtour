<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Booking Form - Medical Tourism Service</title>
    <style>
        /* Your CSS styles */
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
            <h2>Book Transport</h2>
            <?php
            session_start();
            
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                echo "<p style='color: red;'><a href='login_user.php'>Login</a> first to book transport.</p>";
            } else {
                // Get patient ID from users table
                $conn = new mysqli('localhost', 'root', '', 'mt_db');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                
                $user_id = $_SESSION['user_id'];
                $patient_query = "SELECT id FROM patients WHERE user_id = $user_id";
                $patient_result = $conn->query($patient_query);
                
                if ($patient_result->num_rows > 0) {
                    $patient_row = $patient_result->fetch_assoc();
                    $patient_id = $patient_row['id'];
                } else {
                    // Create patient record if it doesn't exist
                    $create_patient = "INSERT INTO patients (user_id) VALUES ($user_id)";
                    if ($conn->query($create_patient) === TRUE) {
                        $patient_id = $conn->insert_id;
                    } else {
                        echo "<p style='color: red;'>Error creating patient profile: " . $conn->error . "</p>";
                        $conn->close();
                        exit;
                    }
                }
                
                $conn->close();
                ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="user_id">User ID:</label>
                    <input type="text" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" readonly>
                    <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $patient_id; ?>"
                </div>
                <div class="form-group">
                    <label for="transport_type">Transport Type:</label>
                    <select id="transport_type" name="transport_type" required>
                        <option value="">Select Transport Type</option>
                        <option value="Taxi">Taxi</option>
                        <option value="Bus">Bus</option>
                        <option value="Train">Train</option>
                        <option value="Private Car">Private Car</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="pickup_location">Pickup Location:</label>
                    <input type="text" id="pickup_location" name="pickup_location" placeholder="e.g., Airport, Hotel" required>
                </div>
                <div class="form-group">
                    <label for="destination">Destination:</label>
                    <input type="text" id="destination" name="destination" placeholder="e.g., Hospital, Hotel" required>
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" required>
                </div>
                <button type="submit" class="submit-button">Book Transport</button>
            </form>
            <?php
            }
            
            // Check if the form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
                // Check if all fields are filled
                if (!empty($_POST['patient_id']) && !empty($_POST['transport_type']) && !empty($_POST['pickup_location']) && !empty($_POST['destination']) && !empty($_POST['date']) && !empty($_POST['time'])) {
                    // Connect to the database
                    $conn = new mysqli('localhost', 'root', '', 'mt_db');

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Sanitize and validate inputs
                    $patient_id = intval($_POST['patient_id']);
                    $transport_type = $conn->real_escape_string($_POST['transport_type']);
                    $pickup_location = $conn->real_escape_string($_POST['pickup_location']);
                    $destination = $conn->real_escape_string($_POST['destination']);
                    $date = $conn->real_escape_string($_POST['date']);
                    $time = $conn->real_escape_string($_POST['time']);

                    // Insert transport booking data into the database
                    $sql = "INSERT INTO transport_bookings (patient_id, transport_type, pickup_location, destination, date, time)
                            VALUES ($patient_id, '$transport_type', '$pickup_location', '$destination', '$date', '$time')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<p style='color: green;'><strong>Transport booked successfully!</strong></p>";
                    } else {
                        echo "<p style='color: red;'>Error booking transport: " . $conn->error . "</p>";
                    }

                    $conn->close();
                } else {
                    echo "<p style='color: red;'>All fields are required!</p>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
