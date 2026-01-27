<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Medical Tourism Service</title>
    <style>
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
            margin: 5px;
        }
        .button:hover {
            background-color: #555;
        }
        .doctor-info {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: left;
        }
        .doctor-info p {
            margin: 5px 0;
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
                <li><a href="welcome_doctor.php">Dashboard</a></li>
                <li><a href="view_appointments.php">View Appointments</a></li>
                <li><a href="help.php">Help</a></li>
                <li><a href="logout_doctor.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="dashboard-content">
            <?php
            session_start();
            
            // PHP code to retrieve and display doctor information
            $conn = new mysqli('localhost', 'root', '', 'mt_db');

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if user is logged in
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
                echo "Please <a href='login_doctor.php'>login</a> as a doctor first.";
            } else {
                // Retrieve doctor information from database
                $user_id = $_SESSION['user_id'];
                $sql = "SELECT u.*, d.specialization, d.experience, d.consultation_fee FROM users u LEFT JOIN doctors d ON u.id = d.user_id WHERE u.id = $user_id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Doctor found, display welcome message and doctor's information
                    $row = $result->fetch_assoc();
                    $name = $row['name'];
                    $email = $row['email'];
                    $doctor_id = $row['id'];
                    $specialization = $row['specialization'];
                    $experience = $row['experience'];
                    $consultation_fee = $row['consultation_fee'];
                    
                    echo "<h2>Welcome to Medical Tourism Service, Dr. $name!</h2>";
                    
                    echo "<div class='doctor-info'>";
                    echo "<p><strong>Email:</strong> $email</p>";
                    echo "<p><strong>Doctor ID:</strong> $doctor_id</p>";
                    echo "<p><strong>Specialization:</strong> $specialization</p>";
                    echo "<p><strong>Experience:</strong> $experience years</p>";
                    echo "<p><strong>Consultation Fee:</strong> \$" . number_format($consultation_fee, 2) . "</p>";
                    echo "</div>";
                    
                    echo "<h3>Quick Actions</h3>";
                    echo "<a href='view_appointments.php' class='button'>View Appointments</a>";
                    echo "<a href='view_appointments.php' class='button'>Manage Schedule</a>";
                    echo "<a href='help.php' class='button'>Help & Support</a>";
                } else {
                    // Doctor not found
                    echo "Doctor profile not found!";
                }
            }

            // Close database connection
            $conn->close();
            ?>
        </div>
        <form action="logout_doctor.php" method="post">
            <input type="submit" value="Logout" class="button">
        </form>
    </div>
  
</body>
</html>
