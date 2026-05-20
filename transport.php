<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Booking – Medical Tourism Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-100 min-h-screen">
<header class="bg-gradient-to-r from-slate-900 to-emerald-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg">MedTour <span class="text-emerald-300">Services</span></span>
        </a>
        <nav class="hidden md:flex items-center gap-6">
            <a href="index.php"    class="text-slate-300 hover:text-white text-sm font-medium transition">Home</a>
            <a href="services.php" class="text-slate-300 hover:text-white text-sm font-medium transition">Services</a>
            <a href="help.php"     class="text-slate-300 hover:text-white text-sm font-medium transition">Help</a>
            <?php if(isset($_SESSION['user_id'])): ?><a href="welcome.php" class="text-slate-300 hover:text-white text-sm font-medium transition">Dashboard</a><?php endif; ?>
        </nav>
        <?php if(isset($_SESSION['user_id'])): ?>
        <a href="logout_user.php" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        <?php else: ?>
        <a href="login_user.php" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Login</a>
        <?php endif; ?>
    </div>
</header>

<div class="relative bg-emerald-700 text-white py-16 text-center overflow-hidden">
    <div class="absolute inset-0 bg-[url('transport.png')] bg-cover bg-center opacity-20"></div>
    <div class="relative z-10 max-w-2xl mx-auto px-4">
        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">🚗</div>
        <h1 class="text-4xl font-bold mb-2">Transportation</h1>
        <p class="text-emerald-100">Reliable airport pickups, hospital transfers & city rides</p>
    </div>
</div>

<main class="max-w-3xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <h2 class="text-2xl font-bold text-slate-800 mb-3">Book Your Transport</h2>
        <p class="text-slate-500 mb-8 leading-relaxed">Book your transportation easily through our transport booking service. We offer various options including private cars, taxi services, shuttle buses, and medical transfers — all available 24/7.</p>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'patient'): ?>
            <a href="submit_transport.php" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-4 rounded-xl transition shadow-lg hover:shadow-emerald-300/40 text-lg">
                <span>🚗</span> Book Transport Now
            </a>
            <a href="transport_bookings.php" class="ml-3 inline-flex items-center gap-2 border border-emerald-400 text-emerald-600 hover:bg-emerald-50 font-semibold px-6 py-4 rounded-xl transition">
                📋 My Bookings
            </a>
        <?php else: ?>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-amber-800 mb-6">
                <p class="font-semibold mb-1">⚠️ Login Required</p>
                <p class="text-sm">You need to be logged in as a patient to book transport.</p>
            </div>
            <a href="login_user.php" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-4 rounded-xl transition">
                🔑 Login to Book Transport
            </a>
        <?php endif; ?>

        <div class="mt-10 pt-8 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-emerald-50 rounded-xl"><div class="text-2xl mb-2">✈️</div><div class="font-semibold text-slate-700 text-xs">Airport Pickup</div></div>
            <div class="text-center p-4 bg-emerald-50 rounded-xl"><div class="text-2xl mb-2">🏥</div><div class="font-semibold text-slate-700 text-xs">Hospital Transfer</div></div>
            <div class="text-center p-4 bg-emerald-50 rounded-xl"><div class="text-2xl mb-2">🕐</div><div class="font-semibold text-slate-700 text-xs">24/7 Available</div></div>
            <div class="text-center p-4 bg-emerald-50 rounded-xl"><div class="text-2xl mb-2">🛡️</div><div class="font-semibold text-slate-700 text-xs">Verified Drivers</div></div>
        </div>
    </div>
</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-10">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
</footer>
</body>
</html>
