<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];
    $consultation_fee = $_POST['consultation_fee'];

    // Assuming you have a MySQL database setup with XAMPP, establish a connection
    $conn = new mysqli('localhost', 'root', '', 'mt_db');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the SQL query to insert data into the users table
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$username', '$email', '$hashed_password', 'doctor')";

    if ($conn->query($sql) === TRUE) {
        // Get the inserted user ID
        $user_id = $conn->insert_id;
        
        // Insert doctor-specific information into doctors table
        $doctor_sql = "INSERT INTO doctors (user_id, specialization, experience, consultation_fee) VALUES ($user_id, '$specialization', $experience, $consultation_fee)";
        
        if ($conn->query($doctor_sql) === TRUE) {
            echo "Doctor account created successfully. <a href='login_doctor.php'>Login here</a>";
        } else {
            echo "Error creating doctor record: " . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Signup</title>
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
        .logo {
            display: flex;
            align-items: center;
        }
        .logo img {
            height: 50px;
            margin-right: 10px;
        }
        .container {
            margin: 50px auto;
            width: 400px;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.95);
        }
        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="number"],
        input[type="submit"],
        select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
        .login-link {
            text-align: center;
            margin-top: 10px;
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
                <li><a href="login_doctor.php">Doctor</a></li>
                <li><a href="login_user.php">User</a></li>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Doctor Signup</h2>
        <form action="#" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="specialization" placeholder="Specialization (e.g., Cardiology)" required>
            <input type="number" name="experience" placeholder="Years of Experience" required>
            <input type="number" name="consultation_fee" placeholder="Consultation Fee" step="0.01" required>
            <input type="submit" value="Sign Up">
        </form>
        <div class="login-link">
            <a href="login_doctor.php">Already have an account? Login here.</a>
        </div>
    </div>
</body>
</html>
