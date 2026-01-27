<?php
// Check if the form is submitted
$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $message_type = "error";
    } else {
        // Establish a connection
        $conn = new mysqli('localhost', 'root', '', 'mt_db');

        // Check connection
        if ($conn->connect_error) {
            $message = "Connection failed: " . $conn->connect_error;
            $message_type = "error";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and execute the SQL query to insert data into the users table
            $sql = "INSERT INTO users (name, email, password, role) VALUES ('$username', '$email', '$hashed_password', 'patient')";

            if ($conn->query($sql) === TRUE) {
                // Get the newly inserted user ID
                $user_id = $conn->insert_id;
                
                // Create a patient record for this user
                $patient_sql = "INSERT INTO patients (user_id) VALUES ($user_id)";
                
                if ($conn->query($patient_sql) === TRUE) {
                    $message = "New record created successfully. <a href='login_user.php'>Login here</a>";
                    $message_type = "success";
                } else {
                    $message = "User created but error creating patient profile: " . $conn->error;
                    $message_type = "error";
                }
            } else {
                $message = "Error: " . $conn->error;
                $message_type = "error";
            }

            // Close connection
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Signup</title>
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
            width: 300px;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.95);
        }
        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="submit"] {
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
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            <li><a href="login_user.php">User</a></li>
        <!--     <li><a href="services.php">Services</a></li> -->
            <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>User Signup</h2>
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="#" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" value="Sign Up">
        </form>
        <div class="login-link">
            <a href="login_user.php">Already have an account? Login here.</a>
        </div>
    </div>
</body>
</html>
