<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information - Medical Tourism Service</title>
    <style>
        /* CSS styles (same as provided in the previous HTML) */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('background.png');
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
        .dashboard-content {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
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
                <li><a href="services.php">Services</a></li>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="dashboard-content">
            <?php
            // PHP code to retrieve and display user information
            // Connect to the database (replace database credentials as necessary)
            $conn = new mysqli('localhost', 'root', '', 'mt_db');

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Retrieve user information based on auto-incremented ID
            $user_id = $_GET['id']; // Assuming you pass the user ID through URL
            $sql = "SELECT * FROM users WHERE id = $user_id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // User found, display welcome message and user's information
                $row = $result->fetch_assoc();
                $username = $row['username'];
                $patient_id = $row['id'];
                echo "<h2>Welcome to Medical Tourism Service, $username!</h2>";
                echo "<p>Your patient ID is: $patient_id</p>";

               


         // Adding buttons for other services
                echo "<a href='transport.php?id=$patient_id' class='button'>Transportation</a>";
                echo "<a href='visa.php?id=$patient_id' class='button'>Visa Assistance</a>";
                echo "<a href='hospital.php?id=$patient_id' class='button'>Hospital Booking</a>";
                echo "<a href='hotel.php?id=$patient_id' class='button'>Hotel Reservation</a>";  
            } else {
                // User not found
                echo "User not found!";
            }

            // Close database connection
            $conn->close();
            ?>
        </div>
        <form action="logout_user.php" method="post">
            <input type="submit" value="Logout" class="button">
        </form>
    </div>
  
</body>
</html>
