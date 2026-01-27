<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Employee</title>
</head>
<body>
    <h2>Add New Employee</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="a_id">ID:</label><br>
        <input type="text" id="a_id" name="a_id"><br>
        <label for="a_name">Name:</label><br>
        <input type="text" id="a_name" name="a_name"><br>
        <label for="a_salary">Salary:</label><br>
        <input type="text" id="a_salary" name="a_salary"><br><br>
        <input type="submit" value="Submit">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "db_abc";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO tb_ar (a_id, a_name, a_salary) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $a_id, $a_name, $a_salary);

        $a_id = $_POST['a_id'];
        $a_name = $_POST['a_name'];
        $a_salary = $_POST['a_salary'];

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
