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
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="patient_id">Patient ID:</label>
                    <input type="text" id="patient_id" name="patient_id" required>
                </div>
                <div class="form-group">
                    <label for="transport_type">Transport Type:</label>
                    <select id="transport_type" name="transport_type" required>
                        <option value="">Select Transport Type</option>
                        <option value="Taxi">Taxi</option>
                        <option value="Bus">Bus</option>
                        <option value="Train">Train</option>
                        <!-- Add more transport types as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="pickup_location">Pickup Location:</label>
                    <input type="text" id="pickup_location" name="pickup_location" required>
                </div>
                <div class="form-group">
                    <label for="destination">Destination:</label>
                    <input type="text" id="destination" name="destination" required>
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
        </div>
    </div>

    <?php
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if all fields are filled
        if (!empty($_POST['patient_id']) && !empty($_POST['transport_type']) && !empty($_POST['pickup_location']) && !empty($_POST['destination']) && !empty($_POST['date']) && !empty($_POST['time'])) {
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

            // Sanitize and validate inputs
            $patient_id = $_POST['patient_id'];
            $transport_type = $_POST['transport_type'];
            $pickup_location = $_POST['pickup_location'];
            $destination = $_POST['destination'];
            $date = $_POST['date'];
            $time = $_POST['time'];

            // Check if the patient ID already has a booking
            $existing_booking_query = "SELECT * FROM transport_bookings WHERE patient_id = '$patient_id'";
            $existing_booking_result = $conn->query($existing_booking_query);
            if ($existing_booking_result->num_rows > 0) {
                echo "<script>alert('A booking already exists for this patient ID!');</script>";
            } else {
                // Insert transport booking data into the database
                $sql = "INSERT INTO transport_bookings (patient_id, transport_type, pickup_location, destination, date, time)
                        VALUES ('$patient_id', '$transport_type', '$pickup_location', '$destination', '$date', '$time')";

               if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Transport booked successfully!');; window.location.href = 'transport_bookings.php'</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            }

            $conn->close();
        } else {
            echo "<script>alert('All fields are required!');;window.location.href = 'view_admin_transport.php'</script>";
        }
    }
    ?>
</body>
</html>
