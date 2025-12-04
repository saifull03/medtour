<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Admin</title>
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
            text-decoration: underline;{
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('background.png');
            background-size: cover;
            background-position: center;
        }
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
            text-align: center;
        }
        .container button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- PHP code to retrieve and display username and admin ID -->
    <?php
    session_start();
    // Check if admin is logged in
    if(isset($_SESSION['admin_username'])) {
        $admin_username = $_SESSION['admin_username'];
       
    } else {
        // Redirect to login page if not logged in
        header("Location: login_admin.php");
        exit();
    }
    ?>
    <!-- HTML content -->
    <header>
        <div class="logo">
            <img src="logo.png" alt="Medical Tourism Service Logo">
            <h1>Medical Tourism Service</h1>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="login_user.php">User</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Welcome, <?php echo $admin_username; ?>!</h2>
      
        <p>Manage And Monitor All the booking by one click</p>
        
        <!-- Buttons for services -->
        <button onclick="location.href='view_admin_transport.php'">Transport</button>
        <button onclick="location.href='view_admin_visa.php'">Visa Assistant</button>
        <button onclick="location.href='view_admin_hospital.php'">Hospital</button>
        <button onclick="location.href='view_admin_hotel.php'">Hotel</button>
        
        <!-- Logout button -->
        <p><a href="logout_admin.php">Logout</a></p>
    </div>
</body>
</html>
