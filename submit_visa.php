<?php
session_start();

// Check if user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login_user.php");
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

// Get patient_id from patients table
$user_id = $_SESSION['user_id'];
$patient_query = "SELECT id FROM patients WHERE user_id = ?";
$stmt = $conn->prepare($patient_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$patient_result = $stmt->get_result();

if ($patient_result->num_rows === 0) {
    // Create patient record if doesn't exist
    $insert_patient = "INSERT INTO patients (user_id, phone, country, passport_no) VALUES (?, NULL, NULL, NULL)";
    $stmt = $conn->prepare($insert_patient);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $patient_id = $conn->insert_id;
    $stmt->close();
} else {
    $patient_row = $patient_result->fetch_assoc();
    $patient_id = $patient_row['id'];
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['visa_type']) && !empty($_POST['country']) && !empty($_POST['passport_number']) && !empty($_POST['application_date'])) {
        
        $visa_type = $_POST['visa_type'];
        $country = $_POST['country'];
        $passport_number = $_POST['passport_number'];
        $application_date = $_POST['application_date'];
        
        // Insert visa booking into visa_bookings table
        $insert_sql = "INSERT INTO visa_bookings (patient_id, visa_type, country, passport_number, application_date, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("issss", $patient_id, $visa_type, $country, $passport_number, $application_date);
        
        if ($stmt->execute()) {
            $message = "<p style='color: green; font-size: 18px; font-weight: bold;'>Visa application submitted successfully! We have sent confirmation to your email.</p>";
        } else {
            $message = "<p style='color: red; font-size: 18px;'>Error submitting application: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color: red; font-size: 18px;'>All fields are required!</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Assistance Form - Medical Tourism Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('visa.png');
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
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group select {
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
        <div class="form-container">
            <h2>Visa Assistance Form</h2>
            <?php echo $message; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="user_id">User ID:</label>
                    <input type="text" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" readonly>
                    <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $patient_id; ?>"
                </div>
                <div class="form-group">
                    <label for="visa_type">Visa Type:</label>
                    <select id="visa_type" name="visa_type" required>
                        <option value="">Select Visa Type</option>
                        <option value="Tourist">Tourist</option>
                        <option value="Business">Business</option>
                        <option value="Medical">Medical</option>
                        <option value="Student">Student</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="country">Destination Country:</label>
                    <input type="text" id="country" name="country" placeholder="e.g., Thailand, Turkey" required>
                </div>
                <div class="form-group">
                    <label for="passport_number">Passport Number:</label>
                    <input type="text" id="passport_number" name="passport_number" placeholder="Enter your passport number" required>
                </div>
                <div class="form-group">
                    <label for="application_date">Application Date:</label>
                    <input type="date" id="application_date" name="application_date" required>
                </div>
                <button type="submit" class="submit-button">Submit Visa Application</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
