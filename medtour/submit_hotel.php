<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Form - Medical Tourism Service</title>
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
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group input[type="number"] {
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
        </a>
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
            <h2>Book Hotel</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="patient_id">Patient ID:</label>
                    <input type="text" id="patient_id" name="patient_id" required>
                </div>
                <div class="form-group">
                    <label for="hotel_name">Hotel Name:</label>
                    <select id="hotel_name" name="hotel_name" required>
                        <option value="">Select Hotel</option>
                        <option value="Hotel A">Hotel A</option>
                        <option value="Hotel B">Hotel B</option>
                        <!-- Add more hotels as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="checkin_date">Check-in Date:</label>
                    <input type="date" id="checkin_date" name="checkin_date" required>
                </div>
                <div class="form-group">
                    <label for="checkin_time">Check-in Time:</label>
                    <input type="time" id="checkin_time" name="checkin_time" required>
                </div>
                <div class="form-group">
                    <label for="checkout_date">Check-out Date:</label>
                    <input type="date" id="checkout_date" name="checkout_date" required>
                </div>
                <div class="form-group">
                    <label for="num_guests">Number of Guests:</label>
                    <input type="number" id="num_guests" name="num_guests" min="1" required>
                </div>
                <div class="form-group">
                    <label for="room_type">Room Type:</label>
                    <select id="room_type" name="room_type" required>
                        <option value="">Select Room Type</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Suite">Suite</option>
                        <!-- Add more room types as needed -->
                    </select>
                </div>
                <button type="submit" class="submit-button">Book Hotel</button>
            </form>
        </div>
    </div>

    <?php
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if all fields are filled
        if (!empty($_POST['patient_id']) && !empty($_POST['hotel_name']) && !empty($_POST['checkin_date']) && !empty($_POST['checkin_time']) && !empty($_POST['checkout_date']) && !empty($_POST['num_guests']) && !empty($_POST['room_type'])) {
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
            $hotel_name = $_POST['hotel_name'];
            $checkin_date = $_POST['checkin_date'];
            $checkin_time = $_POST['checkin_time'];
            $checkout_date = $_POST['checkout_date'];
            $num_guests = $_POST['num_guests'];
            $room_type = $_POST['room_type'];

            // Insert hotel booking data into the database
            $sql = "INSERT INTO hotel_bookings (patient_id, hotel_name, checkin_date, checkin_time, checkout_date, num_guests, room_type)
                    VALUES ('$patient_id', '$hotel_name', '$checkin_date', '$checkin_time', '$checkout_date', '$num_guests', '$room_type')";

            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Hotel booked successfully!');</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();
        } else {
            echo "<script>alert('All fields are required!');</script>";
        }
    }
    ?>
</body>
</html>
