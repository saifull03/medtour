<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Assistance Form - Medical Tourism Service</title>
    <style>
        /* Your CSS styles */
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
        .form-group input[type="email"],
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group select,
        .form-group textarea {
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
            <h2>Visa Assistance Form</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="patient_id">Patient ID:</label>
                    <input type="text" id="patient_id" name="patient_id" required>
                </div>
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="country">Country of Residence:</label>
                    <input type="text" id="country" name="country" required>
                </div>
                <div class="form-group">
                    <label for="passport_number">Passport Number:</label>
                    <input type="text" id="passport_number" name="passport_number" required>
                </div>
                <div class="form-group">
                    <label for="visa_type">Visa Type:</label>
                    <select id="visa_type" name="visa_type" required>
                        <option value="">Select Visa Type</option>
                        <option value="Tourist">Tourist</option>
                        <option value="Business">Business</option>
                        <option value="Student">Student</option>
                        <!-- Add more visa types as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="purpose">Purpose of Visit:</label>
                    <textarea id="purpose" name="purpose" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="appointment_date">Appointment Date:</label>
                    <input type="date" id="appointment_date" name="appointment_date" required>
                </div>
                <div class="form-group">
                    <label for="appointment_time">Appointment Time:</label>
                    <input type="time" id="appointment_time" name="appointment_time" required>
                </div>
                <button type="submit" class="submit-button">Submit</button>
            </form>
        </div>
    </div>

    <?php
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if all fields are filled
        if (!empty($_POST['patient_id']) && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['country']) && !empty($_POST['passport_number']) && !empty($_POST['visa_type']) && !empty($_POST['purpose']) && !empty($_POST['appointment_date']) && !empty($_POST['appointment_time'])) {
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
            $name = $_POST['name'];
            $email = $_POST['email'];
            $country = $_POST['country'];
            $passport_number = $_POST['passport_number'];
            $visa_type = $_POST['visa_type'];
            $purpose = $_POST['purpose'];
            $appointment_date = $_POST['appointment_date'];
            $appointment_time = $_POST['appointment_time'];

            // Concatenate date and time into a datetime format
            $appointment_datetime = $appointment_date . ' ' . $appointment_time;

            // Insert visa assistance data into the database
            $sql = "INSERT INTO visa_assistance (patient_id, name, email, country, passport_number, visa_type, purpose, appointment_date)
                    VALUES ('$patient_id', '$name', '$email', '$country', '$passport_number', '$visa_type', '$purpose', '$appointment_datetime')";

            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Visa Appointment Booked successfully! We have sent a pdf to your email');</script>";
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
