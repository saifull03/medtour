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
        .message {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        .back-button {
            margin-top: 20px;
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button:hover {
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
        <div class="message">
            <?php
            // Check if form data is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Connect to the database
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "mt_db";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Sanitize and validate inputs
                $id = $_POST['id'];
                $patient_id = $_POST['patient_id'];
                $transport_type = $_POST['transport_type'];
                $pickup_location = $_POST['pickup_location'];
                $destination = $_POST['destination'];
                $date = $_POST['date'];
                $time = $_POST['time'];

                // Update transport booking data in the database
                $sql = "UPDATE transport_bookings SET patient_id='$patient_id', transport_type='$transport_type', pickup_location='$pickup_location', destination='$destination', date='$date', time='$time' WHERE id=$id";

                if ($conn->query($sql) === TRUE) {
                    echo "Transport booking updated successfully.";
                } else {
                    echo "Error updating booking: " . $conn->error;
                }

                $conn->close();
            } else {
                echo "Form data not submitted.";
            }
            ?>
        </div>
        <button class="back-button" onclick="goBack()">Back</button>
    </div>

    <script>
        function goBack() {
            window.location.href = "transport_bookings.php";
        }
    </script>
</body>
</html>
