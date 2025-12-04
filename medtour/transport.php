<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Booking - Medical Tourism Service</title>
    <style>
        /* Your CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('transport.png');
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
        .service-info {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        .book-button {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .book-button:hover {
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
         
                <li><a href="services.php">Services</a></li>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="service-info">
            <h2>Transport Booking</h2>
            <p>Book your transportation easily through our transport booking service. We offer various options including car rentals, shuttle services, and private transfers.</p>
            <a href="submit_transport.php" class="book-button">Book Now</a>
        </div>
    </div>
</body>
</html>
