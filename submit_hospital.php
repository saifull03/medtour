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

// Initialize sample doctors and hospitals if they don't exist
$check_doctors = $conn->query("SELECT COUNT(*) as count FROM doctors");
$doctor_count = $check_doctors->fetch_assoc()['count'];

if ($doctor_count < 10) {
    // Add sample doctors
    $sample_doctors = [
        ['name' => 'Dr. Ahmed Hassan', 'specialization' => 'Cardiology', 'phone' => '01012345678'],
        ['name' => 'Dr. Fatima Al-Rashid', 'specialization' => 'Orthopedics', 'phone' => '01023456789'],
        ['name' => 'Dr. Mohamed Saleh', 'specialization' => 'Neurology', 'phone' => '01034567890'],
        ['name' => 'Dr. Layla Ibrahim', 'specialization' => 'Dermatology', 'phone' => '01045678901'],
        ['name' => 'Dr. Karim Abdullah', 'specialization' => 'Gastroenterology', 'phone' => '01056789012'],
        ['name' => 'Dr. Aisha Mohammed', 'specialization' => 'Pediatrics', 'phone' => '01067890123'],
        ['name' => 'Dr. Hassan Ali', 'specialization' => 'Ophthalmology', 'phone' => '01078901234'],
        ['name' => 'Dr. Noor Khalid', 'specialization' => 'Gynecology', 'phone' => '01089012345'],
        ['name' => 'Dr. Samir Yousef', 'specialization' => 'Psychiatry', 'phone' => '01090123456'],
        ['name' => 'Dr. Dina Farraj', 'specialization' => 'General Surgery', 'phone' => '01001234567']
    ];
    
    foreach ($sample_doctors as $doctor) {
        // Check if doctor user exists, if not create it
        $check_user = $conn->query("SELECT id FROM users WHERE email = '" . $conn->real_escape_string(strtolower($doctor['name'])) . "@doctor.com' AND role = 'doctor'");
        if ($check_user->num_rows === 0) {
            // Insert user
            $hashed_password = password_hash('doctor123', PASSWORD_DEFAULT);
            $insert_user = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'doctor', 'active')";
            $stmt = $conn->prepare($insert_user);
            $user_email = strtolower(str_replace(' ', '.', $doctor['name'])) . "@doctor.com";
            $stmt->bind_param("sss", $doctor['name'], $user_email, $hashed_password);
            $stmt->execute();
            $user_id = $conn->insert_id;
            $stmt->close();
            
            // Insert doctor
            $insert_doctor = "INSERT INTO doctors (user_id, specialization, phone) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_doctor);
            $stmt->bind_param("iss", $user_id, $doctor['specialization'], $doctor['phone']);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$check_hospitals = $conn->query("SELECT COUNT(*) as count FROM hospitals");
$hospital_count = $check_hospitals->fetch_assoc()['count'];

if ($hospital_count < 10) {
    // Add sample hospitals
    $sample_hospitals = [
        ['name' => 'Cairo Medical Center', 'location' => 'Cairo, Egypt', 'phone' => '20202222222', 'email' => 'info@cairomed.com'],
        ['name' => 'Al-Nile Hospital', 'location' => 'Giza, Egypt', 'phone' => '20212222222', 'email' => 'contact@alnile.com'],
        ['name' => 'Nasr City Hospital', 'location' => 'Nasr City, Cairo', 'phone' => '20203333333', 'email' => 'info@nasrcity.com'],
        ['name' => 'Maadi Hospital', 'location' => 'Maadi, Cairo', 'phone' => '20204444444', 'email' => 'contact@maadi.com'],
        ['name' => 'Heliopolis Medical Center', 'location' => 'Heliopolis, Cairo', 'phone' => '20205555555', 'email' => 'info@helio.com'],
        ['name' => 'Zamalek Hospital', 'location' => 'Zamalek, Cairo', 'phone' => '20206666666', 'email' => 'contact@zamalek.com'],
        ['name' => 'New Cairo Hospital', 'location' => 'New Cairo', 'phone' => '20207777777', 'email' => 'info@newcairo.com'],
        ['name' => 'Sheikh Zayed Hospital', 'location' => 'Sheikh Zayed City, Giza', 'phone' => '20208888888', 'email' => 'contact@zayed.com'],
        ['name' => 'Al-Obour Hospital', 'location' => 'Obour City', 'phone' => '20209999999', 'email' => 'info@obour.com'],
        ['name' => 'Nile Badrawi Hospital', 'location' => 'Giza, Egypt', 'phone' => '20210101010', 'email' => 'contact@nilebadrawi.com']
    ];
    
    foreach ($sample_hospitals as $hospital) {
        $check_hosp = $conn->query("SELECT id FROM hospitals WHERE name = '" . $conn->real_escape_string($hospital['name']) . "'");
        if ($check_hosp->num_rows === 0) {
            $insert_hospital = "INSERT INTO hospitals (name, location, phone, email) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_hospital);
            $stmt->bind_param("ssss", $hospital['name'], $hospital['location'], $hospital['phone'], $hospital['email']);
            $stmt->execute();
            $stmt->close();
        }
    }
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
    if (!empty($_POST['appointment_date']) && !empty($_POST['doctor_id']) && !empty($_POST['hospital_id'])) {
        
        $appointment_date = $_POST['appointment_date'];
        $doctor_id = intval($_POST['doctor_id']);
        $hospital_id = intval($_POST['hospital_id']);
        
        // Insert appointment into appointments table
        $insert_sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, hospital_id, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("isii", $patient_id, $appointment_date, $doctor_id, $hospital_id);
        
        if ($stmt->execute()) {
            $message = "<p style='color: green; font-size: 18px; margin: 20px 0;'>✓ Appointment booked successfully! Appointment ID: " . $conn->insert_id . "</p>";
        } else {
            $message = "<p style='color: red;'>Error booking appointment: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color: red;'>All fields (Doctor, Hospital, and Date) are required!</p>";
    }
}

// Get list of hospitals
$hospitals = [];
$hospital_query = "SELECT id, name, location FROM hospitals ORDER BY name";
$hospital_result = $conn->query($hospital_query);
if ($hospital_result->num_rows > 0) {
    while ($row = $hospital_result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}

// Get list of doctors
$doctors = [];
$doctor_query = "SELECT d.id, u.name, d.specialization FROM doctors d JOIN users u ON d.user_id = u.id WHERE u.role = 'doctor' ORDER BY u.name";
$doctor_result = $conn->query($doctor_query);
if ($doctor_result->num_rows > 0) {
    while ($row = $doctor_result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Appointment - Medical Tourism Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('hospital.png');
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
        .form-group select,
        .form-group input[type="time"],
        .form-group input[type="date"],
        .form-group input[type="text"] {
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: #fff;
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
            <h2>Book Hospital Appointment</h2>
            <?php echo $message; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="user_id">User ID:</label>
                    <input type="text" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" readonly>
                    <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $patient_id; ?>">
                </div>
                <div class="form-group">
                    <label for="doctor_id">Select Doctor:</label>
                    <select id="doctor_id" name="doctor_id" required>
                        <option value="">Choose a doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['id']; ?>">
                                <?php echo htmlspecialchars($doctor['name']) . " - " . htmlspecialchars($doctor['specialization']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hospital_id">Select Hospital:</label>
                    <select id="hospital_id" name="hospital_id" required>
                        <option value="">Choose a hospital</option>
                        <?php foreach ($hospitals as $hospital): ?>
                            <option value="<?php echo $hospital['id']; ?>">
                                <?php echo htmlspecialchars($hospital['name']) . " (" . htmlspecialchars($hospital['location']) . ")"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointment_date">Appointment Date:</label>
                    <input type="date" id="appointment_date" name="appointment_date" required>
                </div>
                <button type="submit" class="submit-button">Book Appointment</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
