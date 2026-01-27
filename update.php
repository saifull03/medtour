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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';
$record = [];
$error = "";

// Fetch record based on type
if ($type === 'hospital' && $id > 0) {
    $sql = "SELECT a.id, a.patient_id, a.doctor_id, a.hospital_id, a.appointment_date, a.status, u.name as patient_name
            FROM appointments a
            JOIN patients p ON a.patient_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE a.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
    } else {
        $error = "Appointment not found";
    }
    $stmt->close();
} elseif ($type === 'hotel' && $id > 0) {
    $sql = "SELECT h.id, h.patient_id, h.hotel_name, h.checkin_date, h.checkout_date, h.num_guests, h.room_type, h.status, u.name as patient_name
            FROM hotel_bookings h
            JOIN patients p ON h.patient_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE h.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
    } else {
        $error = "Hotel booking not found";
    }
    $stmt->close();
} elseif ($type === 'transport' && $id > 0) {
    $sql = "SELECT t.id, t.patient_id, t.transport_type, t.pickup_location, t.destination, t.date, t.time, t.status, u.name as patient_name
            FROM transport_bookings t
            JOIN patients p ON t.patient_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE t.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
    } else {
        $error = "Transport booking not found";
    }
    $stmt->close();
} elseif ($type === 'visa' && $id > 0) {
    $sql = "SELECT v.id, v.patient_id, v.visa_type, v.country, v.passport_number, v.application_date, v.status, u.name as patient_name
            FROM visa_bookings v
            JOIN patients p ON v.patient_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE v.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
    } else {
        $error = "Visa booking not found";
    }
    $stmt->close();
} else {
    $error = "Invalid type or ID parameter";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking - Medical Tourism Service</title>
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
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-group input:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .submit-button, .back-button {
            flex: 1;
            padding: 12px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-button:hover {
            background-color: #555;
        }
        .back-button {
            background-color: #666;
        }
        .back-button:hover {
            background-color: #888;
        }
        .error {
            color: red;
            padding: 15px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 20px;
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
        <div class="form-container">
            <h2>Update Booking</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($record): ?>
                <form action="update_process.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    
                    <div class="form-group">
                        <label>Patient Name:</label>
                        <input type="text" value="<?php echo htmlspecialchars($record['patient_name']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="pending" <?php if($record['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="approved" <?php if($record['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                            <option value="completed" <?php if($record['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                            <option value="cancelled" <?php if($record['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                    </div>

                    <?php if ($type === 'hospital'): ?>
                        <div class="form-group">
                            <label>Appointment Date:</label>
                            <input type="date" value="<?php echo $record['appointment_date']; ?>" disabled>
                        </div>
                    <?php elseif ($type === 'hotel'): ?>
                        <div class="form-group">
                            <label for="hotel_name">Hotel Name:</label>
                            <input type="text" id="hotel_name" name="hotel_name" value="<?php echo htmlspecialchars($record['hotel_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Check-in Date:</label>
                            <input type="date" value="<?php echo $record['checkin_date']; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Check-out Date:</label>
                            <input type="date" value="<?php echo $record['checkout_date']; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="room_type">Room Type:</label>
                            <select id="room_type" name="room_type" required>
                                <option value="Single" <?php if($record['room_type'] == 'Single') echo 'selected'; ?>>Single</option>
                                <option value="Double" <?php if($record['room_type'] == 'Double') echo 'selected'; ?>>Double</option>
                                <option value="Suite" <?php if($record['room_type'] == 'Suite') echo 'selected'; ?>>Suite</option>
                            </select>
                        </div>
                    <?php elseif ($type === 'transport'): ?>
                        <div class="form-group">
                            <label for="transport_type">Transport Type:</label>
                            <select id="transport_type" name="transport_type" required>
                                <option value="Taxi" <?php if($record['transport_type'] == 'Taxi') echo 'selected'; ?>>Taxi</option>
                                <option value="Bus" <?php if($record['transport_type'] == 'Bus') echo 'selected'; ?>>Bus</option>
                                <option value="Train" <?php if($record['transport_type'] == 'Train') echo 'selected'; ?>>Train</option>
                                <option value="Ambulance" <?php if($record['transport_type'] == 'Ambulance') echo 'selected'; ?>>Ambulance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pickup_location">Pickup Location:</label>
                            <input type="text" id="pickup_location" name="pickup_location" value="<?php echo htmlspecialchars($record['pickup_location']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="destination">Destination:</label>
                            <input type="text" id="destination" name="destination" value="<?php echo htmlspecialchars($record['destination']); ?>" required>
                        </div>
                    <?php elseif ($type === 'visa'): ?>
                        <div class="form-group">
                            <label>Country:</label>
                            <input type="text" value="<?php echo htmlspecialchars($record['country']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Passport Number:</label>
                            <input type="text" value="<?php echo htmlspecialchars($record['passport_number']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Application Date:</label>
                            <input type="date" value="<?php echo $record['application_date']; ?>" disabled>
                        </div>
                    <?php endif; ?>

                    <div class="button-group">
                        <button type="submit" class="submit-button">Update Status</button>
                        <button type="button" class="back-button" onclick="goBack()">Cancel</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
