<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Medical Tourism Service</title>
    <style>

        :root {
            --primary-color: #333;
            --secondary-color: #fff;
            --background-color: #f4f4f4;
            --link-color: #007bff;
            --link-hover-color: #0056b3;
            --border-radius: 5px;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('background.png');
            background-size: cover;
            background-position: center;
        }

        header {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: colorChange 5s infinite alternate;
        }

        @keyframes colorChange {
            0% {
                background-color: var(--primary-color);
            }
            100% {
                background-color: #ff8c00; /* Orange color */
            }
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

        nav ul li:not(:last-child) {
            margin-right: 20px;
        }

        nav ul li a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            color: var(--link-hover-color);
            text-decoration: underline;
        }

        .container {
            margin: 50px auto;
            width: 80%;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.9); /* Add a semi-transparent white background */
            padding: 20px; /* Add padding to separate content from the background */
            border-radius: 10px; /* Add border-radius for rounded corners */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add box shadow for depth */
        }

        .dashboard-content {
            background-color: var(--background-color);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Slides */
        .slides-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .slide {
            flex: 0 0 50%;
            margin: 0 10px;
            overflow: hidden;
            position: relative;
            transition: transform 0.5s ease;
        }

        .slide img {
            width: 100%;
            border-radius: var(--border-radius);
        }

        /* JavaScript will add the 'hidden' class */
        .slide.hidden {
            transform: translateY(100%);
            opacity: 0;
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
                <li><a href="services.php">Services</a></li>
                <li><a href="login_admin.php">Admin</a></li>
                <li><a href="login_user.php">User</a></li>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
       <div class="dashboard-content">
    <h2>About Medical Tourism</h2>
    <p>Medical tourism, also known as health tourism or medical travel, refers to the practice of traveling to another country to receive medical treatment or procedures. It has gained popularity in recent years due to several factors, including cost savings, accessibility to high-quality medical care, shorter waiting times, and opportunities for combining treatment with leisure travel.</p>
    <p>Patients often seek medical tourism for a variety of reasons, such as specialized treatments not available in their home country, lower costs compared to domestic healthcare, access to advanced medical technologies, or seeking privacy and confidentiality.</p>
    <p>Popular destinations for medical tourism include countries known for their world-class healthcare facilities, skilled medical professionals, and affordable treatment options.</p>
    <!-- Add more content here as needed -->
</div>


        <!-- Slides -->
        <div class="slides-container">
            <div class="slide">
                <img src="slide1.jpg" alt="Slide 1">
            </div>
            <div class="slide">
                <img src="slide2.jpg" alt="Slide 2">
            </div>
        </div>
    </div>

    <script>
        // JavaScript for sliding effect
        window.addEventListener('load', function() {
            var slides = document.querySelectorAll('.slide');

            // Initially hide all slides
            slides.forEach(function(slide) {
                slide.classList.add('hidden');
            });

            // Function to reveal slides
            function revealSlides() {
                slides.forEach(function(slide, index) {
                    setTimeout(function() {
                        slide.classList.remove('hidden');
                    }, index * 500); // Delay each slide by 500ms
                });
            }

            // Call revealSlides function after 1 second (adjust timing as needed)
            setTimeout(revealSlides, 1000);
        });
    </script>
</body>
</html>
