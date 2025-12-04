<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login Portal</title>
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
        }
        input[type="text"],
        input[type="password"],
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
        .signup-link {
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
          <!--   <li><a href="services.php">Services</a></li> -->
            <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>User Login</h2>
        <form action="#" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <div class="signup-link">
            <a href="signup_user.php">Don't have an account? Sign up here.</a>
        </div>
    </div>
</body>
</html>

<?php
session_start(); // Start session to store user data

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to the database (replace database credentials as necessary)
    $conn = new mysqli('localhost', 'root', '', 'mt_db');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute SQL query to check user credentials
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User found, verify password
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Password is correct, store user data in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            
            // Redirect user to welcome page with auto ID as patient ID
            header("Location: welcome.php?id={$row['id']}");
            exit();
        } else {
            // Incorrect password
            echo "Incorrect username or password";
        }
    } else {
        // User not found
        echo "User not found";
    }

    // Close database connection
    $stmt->close();
    $conn->close();
}
?>

