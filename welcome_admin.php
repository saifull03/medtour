<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}
$admin_name = htmlspecialchars($_SESSION['admin_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – Medical Tourism Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-100 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-slate-900 to-indigo-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg tracking-tight">MedTour <span class="text-indigo-300">Admin</span></span>
        </div>
        <nav class="hidden md:flex items-center gap-6">
            <a href="welcome_admin.php"      class="text-slate-300 hover:text-white text-sm font-medium transition">Dashboard</a>
            <a href="index.php"              class="text-slate-300 hover:text-white text-sm font-medium transition">Site Home</a>
            <a href="help.php"               class="text-slate-300 hover:text-white text-sm font-medium transition">Help</a>
        </nav>
        <a href="logout_admin.php" class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-10">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-violet-700 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 w-72 h-72 bg-white/5 rounded-full translate-x-1/3 -translate-y-1/2 blur-3xl"></div>
        <div class="relative z-10">
            <p class="text-indigo-200 text-sm font-medium mb-1">Administration Panel 🛡️</p>
            <h1 class="text-3xl font-bold mb-2">Welcome, <?php echo $admin_name; ?>!</h1>
            <p class="text-indigo-100 text-sm">Manage and monitor all bookings across transport, visa, hospital, and hotel services.</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 text-center">
            <div class="text-3xl font-bold text-emerald-600">🚗</div>
            <div class="text-slate-700 font-semibold text-sm mt-2">Transport</div>
            <div class="text-slate-400 text-xs">View all bookings</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 text-center">
            <div class="text-3xl font-bold text-violet-600">📄</div>
            <div class="text-slate-700 font-semibold text-sm mt-2">Visa</div>
            <div class="text-slate-400 text-xs">Application records</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 text-center">
            <div class="text-3xl font-bold text-rose-600">🏥</div>
            <div class="text-slate-700 font-semibold text-sm mt-2">Hospital</div>
            <div class="text-slate-400 text-xs">Appointment logs</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 text-center">
            <div class="text-3xl font-bold text-sky-600">🏨</div>
            <div class="text-slate-700 font-semibold text-sm mt-2">Hotel</div>
            <div class="text-slate-400 text-xs">Reservation records</div>
        </div>
    </div>

    <!-- Management Panels -->
    <h2 class="text-xl font-bold text-slate-700 mb-5">Manage Bookings</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">

        <!-- Transport -->
        <a href="view_admin_transport.php" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-emerald-300 hover:shadow-md transition-all duration-300 flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-100 group-hover:bg-emerald-500 rounded-2xl flex items-center justify-center transition-colors duration-300 flex-shrink-0">
                <svg class="w-7 h-7 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Transport Bookings</h3>
                <p class="text-slate-500 text-sm">View, update and manage all transport requests</p>
                <span class="text-emerald-600 text-xs font-semibold mt-1 inline-block group-hover:underline">Open Records →</span>
            </div>
        </a>

        <!-- Visa -->
        <a href="view_admin_visa.php" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-violet-300 hover:shadow-md transition-all duration-300 flex items-center gap-5">
            <div class="w-14 h-14 bg-violet-100 group-hover:bg-violet-500 rounded-2xl flex items-center justify-center transition-colors duration-300 flex-shrink-0">
                <svg class="w-7 h-7 text-violet-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Visa Applications</h3>
                <p class="text-slate-500 text-sm">Review and process all visa assistance requests</p>
                <span class="text-violet-600 text-xs font-semibold mt-1 inline-block group-hover:underline">Open Records →</span>
            </div>
        </a>

        <!-- Hospital -->
        <a href="view_admin_hospital.php" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-rose-300 hover:shadow-md transition-all duration-300 flex items-center gap-5">
            <div class="w-14 h-14 bg-rose-100 group-hover:bg-rose-500 rounded-2xl flex items-center justify-center transition-colors duration-300 flex-shrink-0">
                <svg class="w-7 h-7 text-rose-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Hospital Appointments</h3>
                <p class="text-slate-500 text-sm">View all hospital and appointment bookings</p>
                <span class="text-rose-600 text-xs font-semibold mt-1 inline-block group-hover:underline">Open Records →</span>
            </div>
        </a>

        <!-- Hotel -->
        <a href="view_admin_hotel.php" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-sky-300 hover:shadow-md transition-all duration-300 flex items-center gap-5">
            <div class="w-14 h-14 bg-sky-100 group-hover:bg-sky-500 rounded-2xl flex items-center justify-center transition-colors duration-300 flex-shrink-0">
                <svg class="w-7 h-7 text-sky-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Hotel Reservations</h3>
                <p class="text-slate-500 text-sm">Monitor all patient hotel booking records</p>
                <span class="text-sky-600 text-xs font-semibold mt-1 inline-block group-hover:underline">Open Records →</span>
            </div>
        </a>

    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-3">
            <a href="signup_admin.php" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium px-4 py-2 rounded-lg transition">➕ Add Admin Account</a>
            <a href="signup_doctor.php" class="bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-medium px-4 py-2 rounded-lg transition">👨‍⚕️ Register Doctor</a>
            <a href="help.php"          class="bg-slate-50 hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition">❓ Help & Support</a>
            <a href="logout_admin.php"  class="bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-medium px-4 py-2 rounded-lg transition">🚪 Logout</a>
        </div>
    </div>

</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-10">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services Administration Panel. All rights reserved.</p>
</footer>

</body>
</html>
