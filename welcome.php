<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login_user.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];
$sql = "SELECT u.*, p.id as patient_id FROM users u LEFT JOIN patients p ON u.id = p.user_id WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$conn->close();

$name       = htmlspecialchars($row['name'] ?? 'User');
$email      = htmlspecialchars($row['email'] ?? '');
$patient_id = $row['patient_id'] ?? $user_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard – Medical Tourism Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-100 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-slate-900 to-sky-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg tracking-tight">MedTour <span class="text-sky-300">Services</span></span>
        </div>
        <nav class="hidden md:flex items-center gap-6">
            <a href="welcome.php"      class="text-slate-300 hover:text-white text-sm font-medium transition">Dashboard</a>
            <a href="services.php"     class="text-slate-300 hover:text-white text-sm font-medium transition">Services</a>
            <a href="transport_bookings.php" class="text-slate-300 hover:text-white text-sm font-medium transition">My Bookings</a>
            <a href="help.php"         class="text-slate-300 hover:text-white text-sm font-medium transition">Help</a>
        </nav>
        <form action="logout_user.php" method="post">
            <button class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</button>
        </form>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-10">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3 blur-2xl"></div>
        <div class="relative z-10">
            <p class="text-sky-200 text-sm font-medium mb-1">Welcome back 👋</p>
            <h1 class="text-3xl font-bold mb-2">Hello, <?php echo $name; ?>!</h1>
            <p class="text-sky-100 text-sm">Patient ID: <span class="font-mono bg-white/20 px-2 py-0.5 rounded">#<?php echo $patient_id; ?></span></p>
            <p class="text-sky-100 text-sm mt-1">Email: <?php echo $email; ?></p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 text-center">
            <div class="text-2xl font-bold text-sky-600">4</div>
            <div class="text-slate-500 text-xs mt-1">Services Available</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 text-center">
            <div class="text-2xl font-bold text-emerald-600">24/7</div>
            <div class="text-slate-500 text-xs mt-1">Support Available</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 text-center">
            <div class="text-2xl font-bold text-violet-600">50+</div>
            <div class="text-slate-500 text-xs mt-1">Partner Hospitals</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 text-center">
            <div class="text-2xl font-bold text-amber-600">30+</div>
            <div class="text-slate-500 text-xs mt-1">Countries</div>
        </div>
    </div>

    <!-- Services Grid -->
    <h2 class="text-xl font-bold text-slate-700 mb-5">Book a Service</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <!-- Transport -->
        <a href="transport.php?id=<?php echo $patient_id; ?>" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-emerald-300 hover:shadow-md transition-all duration-300 flex flex-col items-center text-center">
            <div class="w-14 h-14 bg-emerald-100 group-hover:bg-emerald-500 rounded-2xl flex items-center justify-center mb-4 transition-colors duration-300">
                <svg class="w-7 h-7 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 mb-1">Transportation</h3>
            <p class="text-slate-500 text-xs">Airport pickups & hospital transfers</p>
            <span class="mt-3 text-emerald-600 text-xs font-semibold group-hover:underline">Book Now →</span>
        </a>

        <!-- Visa -->
        <a href="visa.php?id=<?php echo $patient_id; ?>" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-violet-300 hover:shadow-md transition-all duration-300 flex flex-col items-center text-center">
            <div class="w-14 h-14 bg-violet-100 group-hover:bg-violet-500 rounded-2xl flex items-center justify-center mb-4 transition-colors duration-300">
                <svg class="w-7 h-7 text-violet-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 mb-1">Visa Assistance</h3>
            <p class="text-slate-500 text-xs">Medical visa application support</p>
            <span class="mt-3 text-violet-600 text-xs font-semibold group-hover:underline">Apply Now →</span>
        </a>

        <!-- Hospital -->
        <a href="hospital.php?id=<?php echo $patient_id; ?>" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-rose-300 hover:shadow-md transition-all duration-300 flex flex-col items-center text-center">
            <div class="w-14 h-14 bg-rose-100 group-hover:bg-rose-500 rounded-2xl flex items-center justify-center mb-4 transition-colors duration-300">
                <svg class="w-7 h-7 text-rose-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 mb-1">Hospital Booking</h3>
            <p class="text-slate-500 text-xs">Top hospitals & specialist doctors</p>
            <span class="mt-3 text-rose-600 text-xs font-semibold group-hover:underline">Book Now →</span>
        </a>

        <!-- Hotel -->
        <a href="hotel.php?id=<?php echo $patient_id; ?>" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-sky-300 hover:shadow-md transition-all duration-300 flex flex-col items-center text-center">
            <div class="w-14 h-14 bg-sky-100 group-hover:bg-sky-500 rounded-2xl flex items-center justify-center mb-4 transition-colors duration-300">
                <svg class="w-7 h-7 text-sky-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 mb-1">Hotel Reservation</h3>
            <p class="text-slate-500 text-xs">Comfortable stays near hospitals</p>
            <span class="mt-3 text-sky-600 text-xs font-semibold group-hover:underline">Reserve →</span>
        </a>

    </div>

    <!-- My Bookings Quick Links -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h2 class="text-lg font-bold text-slate-800 mb-4">My Bookings</h2>
        <div class="flex flex-wrap gap-3">
            <a href="transport_bookings.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition">🚗 Transport Bookings</a>
            <a href="help.php"              class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition">❓ Help & Support</a>
            <a href="index.php"             class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition">🏠 Home</a>
        </div>
    </div>

</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-10">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
</footer>

</body>
</html>
