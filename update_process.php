<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mt_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$success = false;

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $type = $_POST['type'];
    $status = $_POST['status'];
    
    // Update based on type
    if ($type === 'hospital') {
        $sql = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            $message = "✓ Appointment updated successfully!";
            $success = true;
        } else {
            $message = "Error updating appointment: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($type === 'hotel') {
        $hotel_name = $_POST['hotel_name'] ?? '';
        $room_type = $_POST['room_type'] ?? '';
        $sql = "UPDATE hotel_bookings SET hotel_name = ?, room_type = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $hotel_name, $room_type, $status, $id);
        if ($stmt->execute()) {
            $message = "✓ Hotel booking updated successfully!";
            $success = true;
        } else {
            $message = "Error updating hotel booking: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($type === 'transport') {
        $transport_type = $_POST['transport_type'] ?? '';
        $pickup_location = $_POST['pickup_location'] ?? '';
        $destination = $_POST['destination'] ?? '';
        $sql = "UPDATE transport_bookings SET transport_type = ?, pickup_location = ?, destination = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $transport_type, $pickup_location, $destination, $status, $id);
        if ($stmt->execute()) {
            $message = "✓ Transport booking updated successfully!";
            $success = true;
        } else {
            $message = "Error updating transport booking: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($type === 'visa') {
        $sql = "UPDATE visa_bookings SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            $message = "✓ Visa booking updated successfully!";
            $success = true;
        } else {
            $message = "Error updating visa booking: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Invalid booking type";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Result - Medical Tourism Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
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
            max-width: 600px;
        }
        .message-box {
            background-color: white;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success {
            color: #28a745;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .error {
            color: #dc3545;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        .button {
            flex: 1;
            padding: 12px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
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
                <li><a href="welcome_admin.php">Dashboard</a></li>
                <li><a href="logout_admin.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="message-box">
            <div class="<?php echo $success ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            
            <div class="button-group">
                <a href="welcome_admin.php" class="button">Go to Dashboard</a>
                <button class="button" onclick="window.history.back()">Go Back</button>
            </div>
        </div>
    </div>
</body>
</html>
