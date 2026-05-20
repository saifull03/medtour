<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services – Medical Tourism Service</title>
    <meta name="description" content="Explore our comprehensive medical tourism services including hotel booking, transportation, hospital appointments, and visa assistance.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-50 min-h-screen">

<!-- Header -->
<header class="bg-slate-900/95 backdrop-blur sticky top-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="text-white font-bold text-lg tracking-tight">MedTour <span class="text-sky-400">Services</span></span>
        </a>
        <nav class="hidden md:flex items-center gap-6">
            <a href="index.php"        class="text-slate-300 hover:text-white text-sm font-medium transition">Home</a>
            <a href="services.php"     class="text-sky-400 text-sm font-medium">Services</a>
            <a href="help.php"         class="text-slate-300 hover:text-white text-sm font-medium transition">Help</a>
            <a href="login_admin.php"  class="text-slate-300 hover:text-white text-sm font-medium transition">Admin</a>
        </nav>
        <div class="flex items-center gap-3">
            <a href="login_user.php"  class="text-sky-400 border border-sky-400 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-sky-400 hover:text-white transition">Login</a>
            <a href="signup_user.php" class="bg-sky-500 hover:bg-sky-600 text-white rounded-lg px-4 py-2 text-sm font-semibold transition">Sign Up</a>
        </div>
    </div>
</header>

<!-- Page Hero -->
<section class="bg-gradient-to-br from-slate-800 to-sky-900 text-white py-16 px-4 text-center">
    <div class="max-w-3xl mx-auto">
        <span class="inline-block bg-sky-400/20 border border-sky-400/40 text-sky-200 text-xs font-semibold uppercase tracking-widest px-4 py-1.5 rounded-full mb-5">Complete Care Package</span>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Our Services</h1>
        <p class="text-sky-100 text-lg">Everything you need for a seamless medical journey — all in one place.</p>
    </div>
</section>

<!-- Services Grid -->
<section class="max-w-6xl mx-auto px-4 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Hotel -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-lg transition-shadow duration-300 group">
            <div class="h-48 bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center">
                <svg class="w-20 h-20 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div class="p-7">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-sky-100 rounded-xl flex items-center justify-center">
                        <span class="text-sky-600 font-bold text-lg">🏨</span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">Hotel Booking</h2>
                </div>
                <p class="text-slate-500 mb-5 leading-relaxed">Book comfortable rooms near top medical facilities. Choose from single, double, or suite options at partner hotels around the world.</p>
                <ul class="space-y-2 mb-6 text-sm text-slate-600">
                    <li class="flex items-center gap-2"><span class="text-sky-500">✓</span> Partner hotels near hospitals</li>
                    <li class="flex items-center gap-2"><span class="text-sky-500">✓</span> Flexible check-in / check-out</li>
                    <li class="flex items-center gap-2"><span class="text-sky-500">✓</span> Single, Double & Suite rooms</li>
                </ul>
                <a href="hotel.php" class="inline-block bg-sky-500 hover:bg-sky-600 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">Book Hotel →</a>
            </div>
        </div>

        <!-- Transport -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-lg transition-shadow duration-300 group">
            <div class="h-48 bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center">
                <svg class="w-20 h-20 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div class="p-7">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <span class="text-emerald-600 font-bold text-lg">🚗</span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">Transportation</h2>
                </div>
                <p class="text-slate-500 mb-5 leading-relaxed">Reliable, comfortable transport from the airport to your hotel and hospital. Our drivers know every route and are available 24/7.</p>
                <ul class="space-y-2 mb-6 text-sm text-slate-600">
                    <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Airport pickups & drop-offs</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Hospital transfer service</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Private car, van & ambulance</li>
                </ul>
                <a href="transport.php" class="inline-block bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">Book Transport →</a>
            </div>
        </div>

        <!-- Hospital -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-lg transition-shadow duration-300 group">
            <div class="h-48 bg-gradient-to-br from-rose-400 to-red-600 flex items-center justify-center">
                <svg class="w-20 h-20 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div class="p-7">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                        <span class="text-rose-600 font-bold text-lg">🏥</span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">Hospital Booking</h2>
                </div>
                <p class="text-slate-500 mb-5 leading-relaxed">Connect with leading hospitals and specialist doctors. Book appointments for treatments ranging from cardiology to oncology and more.</p>
                <ul class="space-y-2 mb-6 text-sm text-slate-600">
                    <li class="flex items-center gap-2"><span class="text-rose-500">✓</span> JCI-accredited hospitals</li>
                    <li class="flex items-center gap-2"><span class="text-rose-500">✓</span> Specialist consultations</li>
                    <li class="flex items-center gap-2"><span class="text-rose-500">✓</span> Surgery & treatment planning</li>
                </ul>
                <a href="hospital.php" class="inline-block bg-rose-500 hover:bg-rose-600 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">Book Appointment →</a>
            </div>
        </div>

        <!-- Visa -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-lg transition-shadow duration-300 group">
            <div class="h-48 bg-gradient-to-br from-violet-400 to-purple-700 flex items-center justify-center">
                <svg class="w-20 h-20 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="p-7">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                        <span class="text-violet-600 font-bold text-lg">📄</span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">Visa Assistance</h2>
                </div>
                <p class="text-slate-500 mb-5 leading-relaxed">Navigate the medical visa process stress-free. Our team helps prepare your application, supporting documents, and tracks your status.</p>
                <ul class="space-y-2 mb-6 text-sm text-slate-600">
                    <li class="flex items-center gap-2"><span class="text-violet-500">✓</span> Tourist, Medical & Business visas</li>
                    <li class="flex items-center gap-2"><span class="text-violet-500">✓</span> Document preparation support</li>
                    <li class="flex items-center gap-2"><span class="text-violet-500">✓</span> Application status tracking</li>
                </ul>
                <a href="visa.php" class="inline-block bg-violet-500 hover:bg-violet-600 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">Apply for Visa →</a>
            </div>
        </div>

    </div>
</section>

<!-- CTA -->
<section class="bg-gradient-to-r from-sky-600 to-blue-700 py-14 px-4 text-white text-center">
    <div class="max-w-xl mx-auto">
        <h2 class="text-3xl font-bold mb-3">Ready to Book?</h2>
        <p class="text-sky-100 mb-7">Create a free account and start booking your medical travel services today.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="signup_user.php" class="bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold px-8 py-3 rounded-xl transition">Create Account</a>
            <a href="login_user.php"  class="border border-white/40 hover:bg-white/10 text-white font-semibold px-8 py-3 rounded-xl transition">Sign In</a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-slate-900 text-slate-400 py-8 px-4 text-center text-sm">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
    <div class="flex justify-center gap-6 mt-3">
        <a href="index.php"       class="hover:text-white transition">Home</a>
        <a href="services.php"    class="hover:text-white transition">Services</a>
        <a href="help.php"        class="hover:text-white transition">Help</a>
        <a href="login_admin.php" class="hover:text-white transition">Admin</a>
    </div>
</footer>

</body>
</html>
