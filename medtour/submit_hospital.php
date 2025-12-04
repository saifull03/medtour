<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Appointment Form - Medical Tourism Service</title>
    <style>
        /* Your CSS styles */
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
        </a>
        <nav>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
           
                <li><a href="login_admin.php">Admin</a></li>
                <li><a href="login_user.php">User</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="form-container">
            <h2>Book Hospital Appointment</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="patient_id">Patient ID:</label>
                    <input type="text" id="patient_id" name="patient_id" required>
                </div>
                <div class="form-group">
                    <label for="doctor_name">Doctor Name:</label>
                    <select id="doctor_name" name="doctor_name" required>
                        <option value="">Select Doctor</option>
                        <option value="Dr Ratul Rahman">Dr Ratul Rahman</option>
                        <option value="Dr.Saiful">Dr.Saiful</option>
                        <!-- Add more doctors as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="hospital_name">Hospital Name:</label>
                    <select id="hospital_name" name="hospital_name" required>
                        <option value="">Select Hospital</option>
                        <option value="Hospital A">Hospital A</option>
                        <option value="Hospital B">Hospital B</option>
                        <!-- Add more hospitals as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointment_time">Appointment Time:</label>
                    <input type="time" id="appointment_time" name="appointment_time" required>
                </div>
                <div class="form-group">
                    <label for="appointment_date">Appointment Date:</label>
                    <input type="date" id="appointment_date" name="appointment_date" required>
                </div>
                <div class="form-group">
                    <label for="cancer_type">Cancer Type:</label>
                    <input type="text" id="cancer_type" name="cancer_type" required>
                </div>
                <button type="submit" class="submit-button">Book Appointment</button>
            </form>
        </div>
    </div>

    <?php
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if all fields are filled
        if (!empty($_POST['patient_id']) && !empty($_POST['doctor_name']) && !empty($_POST['hospital_name']) && !empty($_POST['appointment_time']) && !empty($_POST['appointment_date']) && !empty($_POST['cancer_type'])) {
            // Connect to the database
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "mt_db";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Sanitize and validate inputs
            $patient_id = $_POST['patient_id'];
            $doctor_name = $_POST['doctor_name'];
            $hospital_name = $_POST['hospital_name'];
            $appointment_time = $_POST['appointment_time'];
            $appointment_date = $_POST['appointment_date'];
            $cancer_type = $_POST['cancer_type'];

            // Insert appointment data into the database
            $sql = "INSERT INTO hospital_appointments (patient_id, doctor_name, hospital_name, appointment_time, appointment_date, cancer_type)
                    VALUES ('$patient_id', '$doctor_name', '$hospital_name', '$appointment_time', '$appointment_date', '$cancer_type')";

            if ($conn->query($sql) === TRUE) {
                // Redirect to view page
                header("Location: view_hospital_appointment.php");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();
        } else {
            echo "<script>alert('All fields are required!');</script>";
        }
    }
    ?>
</body>
</html>
