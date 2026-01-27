<?php
// Check admin session
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Applications - Medical Tourism Service</title>
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
            width: 90%;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 5px;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #333;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f0f0f0;
        }
        .search-form {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f4f4f4;
            border-radius: 5px;
        }
        .search-form input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            margin-right: 10px;
        }
        .search-form button {
            padding: 8px 16px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #555;
        }
        .update-button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
        }
        .update-button:hover {
            background-color: #45a049;
        }
        .status-pending {
            color: #ff9800;
            font-weight: bold;
        }
        .status-approved {
            color: #4CAF50;
            font-weight: bold;
        }
        .status-rejected {
            color: #f44336;
            font-weight: bold;
        }
        h2 {
            color: #333;
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
        <h2>Visa Applications</h2>
        <div class="search-form">
            <form method="GET">
                <label for="search_patient_id">Search Patient ID:</label>
                <input type="text" id="search_patient_id" name="search_patient_id" placeholder="Enter patient ID" value="<?php echo isset($_GET['search_patient_id']) ? htmlspecialchars($_GET['search_patient_id']) : ''; ?>">
                <button type="submit">Search</button>
                <a href="view_admin_visa.php" style="margin-left: 10px; padding: 8px 16px; background-color: #666; color: white; text-decoration: none; border-radius: 5px;">Clear</a>
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient ID</th>
                        <th>Visa Type</th>
                        <th>Country</th>
                        <th>Passport Number</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Connect to the database
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "mt_db";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Search for specific patient ID if provided
                    $search_patient_id = isset($_GET['search_patient_id']) ? $_GET['search_patient_id'] : '';
                    $sql = "SELECT * FROM visa_bookings";
                    if (!empty($search_patient_id)) {
                        $sql .= " WHERE patient_id LIKE '%" . $conn->real_escape_string($search_patient_id) . "%'";
                    }
                    $sql .= " ORDER BY id DESC";
                    
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            $status_class = "status-" . strtolower($row["status"]);
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["patient_id"] . "</td>";
                            echo "<td>" . htmlspecialchars($row["visa_type"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["country"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["passport_number"]) . "</td>";
                            echo "<td>" . $row["application_date"] . "</td>";
                            echo "<td class='" . $status_class . "'>" . ucfirst($row["status"]) . "</td>";
                            echo "<td>" . $row["created_at"] . "</td>";
                            echo "<td><a class='update-button' href='update.php?id=" . $row["id"] . "&type=visa'>Update</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' style='text-align: center; padding: 20px;'>No visa applications found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
